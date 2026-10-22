<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Cv;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // 5. Lọc theo Mức lương
        if ($request->filled('salary_range')) {
            switch ($request->salary_range) {
                case 'under_15':
                    $query->where('salary_max', '<=', 15000000);
                    break;
                case '15_25':
                    $query->where('salary_min', '>=', 15000000)->where('salary_max', '<=', 25000000);
                    break;
                case '25_40':
                    $query->where('salary_min', '>=', 25000000)->where('salary_max', '<=', 40000000);
                    break;
                case 'above_40':
                    $query->where('salary_min', '>=', 40000000);
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
        $locations = Location::all();
        $skills = Skill::all();

        return view('jobs.index', compact('jobs', 'categories', 'locations', 'skills'));
    }

    /**
     * Chi tiết tin tuyển dụng
     */
    public function show($id)
    {
        $job = JobPost::with(['company.user', 'category', 'location', 'skills'])->findOrFail($id);

        // Tăng lượt xem
        $job->increment('views_count');

        // Việc làm liên quan cùng ngành nghề
        $relatedJobs = JobPost::with(['company', 'location'])
            ->active()
            ->where('id', '!=', $job->id)
            ->where('category_id', $job->category_id)
            ->take(3)
            ->get();

        // Kiểm tra xem ứng viên đã ứng tuyển chưa
        $hasApplied = false;
        $userCvs = collect();
        if (Auth::check() && Auth::user()->isCandidate()) {
            $hasApplied = Application::where('job_post_id', $job->id)
                ->where('user_id', Auth::id())
                ->exists();
            $userCvs = Cv::where('user_id', Auth::id())->get();
        }

        return view('jobs.show', compact('job', 'relatedJobs', 'hasApplied', 'userCvs'));
    }

    /**
     * Ứng tuyển vào công việc
     */
    public function apply(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập với tài khoản Ứng viên để nộp hồ sơ.');
        }

        $user = Auth::user();
        if (!$user->isCandidate()) {
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
            'existing_cv_id' => ['required_if:cv_option,existing', 'nullable', 'exists:cvs,id'],
            'cv_file' => ['required_if:cv_option,new', 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'existing_cv_id.required_if' => 'Vui lòng chọn một CV đã có trong tài khoản.',
            'cv_file.required_if' => 'Vui lòng tải lên tệp CV (PDF hoặc Word).',
            'cv_file.mimes' => 'Tệp CV phải có định dạng .pdf, .doc hoặc .docx.',
            'cv_file.max' => 'Dung lượng tệp CV không được vượt quá 5MB.',
        ]);

        $cvId = null;

        if ($request->cv_option === 'existing') {
            $cvId = $request->existing_cv_id;
        } else {
            // Tải lên CV mới
            $file = $request->file('cv_file');
            $filename = 'cv_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('cvs', $filename, 'public');

            $cv = Cv::create([
                'user_id' => $user->id,
                'title' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'is_primary' => !Cv::where('user_id', $user->id)->exists(),
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

        return back()->with('success', 'Chúc mừng! Hồ sơ của bạn đã được gửi thành công đến nhà tuyển dụng.');
    }
}
