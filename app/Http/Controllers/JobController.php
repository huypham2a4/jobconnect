<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Cv;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\JobMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Danh sách việc làm có lọc, tìm kiếm và phân trang
     */
    public function index(Request $request)
    {
        $query = JobPost::with(['company', 'category', 'location', 'skills'])->active();

        // 1. Tìm theo từ khóa
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('requirements', 'like', "%{$keyword}%")
                    ->orWhereHas('company', function ($c) use ($keyword) {
                        $c->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('skills', function ($s) use ($keyword) {
                        $s->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // 2. Lọc theo Ngành nghề
        if ($request->filled('category')) {
            $catSlug = $request->category;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        // 3. Lọc theo Địa điểm
        if ($request->filled('location')) {
            $locSlug = $request->location;
            $query->whereHas('location', function ($q) use ($locSlug) {
                $q->where('slug', $locSlug);
            });
        }

        // 4. Lọc theo Hình thức (full-time, remote...)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 4b. Lọc theo Kinh nghiệm
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        // 5. Lọc theo Mức lương (hỗ trợ giao thoa khoảng lương min - max)
        if ($request->filled('salary_range')) {
            switch ($request->salary_range) {
                case 'under_15':
                    $query->where('salary_negotiable', false)
                        ->whereRaw('COALESCE(salary_min, salary_max) <= ?', [15000000]);
                    break;
                case '15_25':
                    $query->where('salary_negotiable', false)
                        ->whereRaw('COALESCE(salary_max, salary_min) >= ?', [15000000])
                        ->whereRaw('COALESCE(salary_min, salary_max) <= ?', [25000000]);
                    break;
                case '25_40':
                    $query->where('salary_negotiable', false)
                        ->whereRaw('COALESCE(salary_max, salary_min) >= ?', [25000000])
                        ->whereRaw('COALESCE(salary_min, salary_max) <= ?', [40000000]);
                    break;
                case 'above_40':
                    $query->where('salary_negotiable', false)
                        ->whereRaw('COALESCE(salary_max, salary_min) >= ?', [40000000]);
                    break;
                case 'negotiable':
                    $query->where('salary_negotiable', true);
                    break;
            }
        }

        // Sắp xếp
        $sort = $request->get('sort', 'latest');
        if ($sort === 'salary_high') {
            $query->orderByDesc('salary_max');
        } elseif ($sort === 'deadline') {
            $query->orderBy('deadline', 'asc');
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(9)->withQueryString();
        $categories = Category::withCount(['jobPosts' => function ($q) {
            $q->active();
        }])->get();
        $locations = Location::orderedForSelect();
        $skills = Skill::all();

        return view('jobs.index', compact('jobs', 'categories', 'locations', 'skills'));
    }

    /**
     * Chi tiết tin tuyển dụng
     */
    public function show($id, JobMatchingService $matcher)
    {
        $job = JobPost::with(['company.user', 'category', 'location', 'skills'])->findOrFail($id);

        $isPreviewer = Auth::check() && (Auth::user()->isAdmin() || $job->company?->user_id === Auth::id());

        if ($job->status !== 'approved' || $job->is_expired) {
            abort_unless($isPreviewer, 404);
        }

        // Tăng lượt xem (không tính lượt Admin hoặc chính nhà tuyển dụng xem trước tin của mình)
        if (! $isPreviewer) {
            $job->increment('views_count');
        }

        // Việc làm liên quan cùng ngành nghề
        $relatedJobs = JobPost::with(['company', 'location'])
            ->active()
            ->where('id', '!=', $job->id)
            ->where('category_id', $job->category_id)
            ->take(3)
            ->get();

        // Kiểm tra xem ứng viên đã ứng tuyển chưa + Phân tích độ phù hợp kỹ năng
        $hasApplied = false;
        $userCvs = collect();
        $matchAnalysis = null;
        if (Auth::check() && Auth::user()->isCandidate()) {
            $hasApplied = Application::where('job_post_id', $job->id)
                ->where('user_id', Auth::id())
                ->where('applied_at', '>=', now()->subMonths(6))
                ->exists();
            $userCvs = Cv::where('user_id', Auth::id())->get();
            $matchAnalysis = $matcher->analyze(Auth::user(), $job);
        }

        return view('jobs.show', compact('job', 'relatedJobs', 'hasApplied', 'userCvs', 'matchAnalysis'));
    }

    /**
     * Ứng tuyển vào công việc
     */
    public function apply(Request $request, $id)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập với tài khoản Ứng viên để nộp hồ sơ.');
        }

        $user = Auth::user();
        if (! $user->isCandidate()) {
            return back()->with('error', 'Chỉ tài khoản Ứng viên mới có thể nộp hồ sơ ứng tuyển.');
        }

        $job = JobPost::active()->findOrFail($id);

        // Quy tắc: Mỗi ứng viên chỉ ứng tuyển 1 lần / vị trí trong 6 tháng
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $recentApp = Application::where('job_post_id', $job->id)
            ->where('user_id', $user->id)
            ->where('applied_at', '>=', $sixMonthsAgo)
            ->first();

        if ($recentApp) {
            return back()->with('error', 'Bạn đã nộp hồ sơ cho vị trí này trong vòng 6 tháng gần nhất. Không thể nộp lại.');
        }

        $request->validate([
            'cv_option' => ['required', 'in:existing,new'],
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'existing_cv_id' => ['required_if:cv_option,existing', 'nullable', Rule::exists('cvs', 'id')->where('user_id', $user->id)],
            'cv_file' => ['required_if:cv_option,new', 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'existing_cv_id.required_if' => 'Vui lòng chọn một CV đã có trong tài khoản.',
            'cv_file.required_if' => 'Vui lòng tải lên tệp CV (PDF hoặc Word).',
            'cv_file.mimes' => 'Tệp CV phải có định dạng .pdf, .doc hoặc .docx.',
            'cv_file.max' => 'Dung lượng tệp CV không được vượt quá 5MB.',
        ]);

        return DB::transaction(function () use ($request, $user, $job) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if (Application::where('job_post_id', $job->id)->where('user_id', $user->id)->where('applied_at', '>=', now()->subMonths(6))->exists()) {
                return back()->with('error', 'Bạn đã ứng tuyển vị trí này trong 6 tháng gần nhất.');
            }
            $cvId = null;

            if ($request->cv_option === 'existing') {
                $cvId = $request->existing_cv_id;
            } else {
                // Tải lên CV mới
                $file = $request->file('cv_file');
                $filename = 'cv_'.$user->id.'_'.Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('cvs', $filename, 'public');

                $cv = Cv::create([
                    'user_id' => $user->id,
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'is_primary' => ! Cv::where('user_id', $user->id)->exists(),
                ]);

                $cvId = $cv->id;
            }

            Application::create([
                'job_post_id' => $job->id,
                'user_id' => $user->id,
                'cv_id' => $cvId,
                'cover_letter' => $request->cover_letter,
                'status' => 'submitted',
                'applied_at' => Carbon::now(),
            ]);

            $job->loadMissing('company');
            UserNotification::notifyUser(
                $job->company?->user_id,
                'application_submitted',
                'Có hồ sơ ứng tuyển mới',
                "{$user->name} vừa nộp hồ sơ vào vị trí \"{$job->title}\".",
                route('employer.applications', ['job_id' => $job->id])
            );

            return back()->with('success', 'Chúc mừng! Hồ sơ của bạn đã được gửi thành công đến nhà tuyển dụng.');
        });
    }
}
