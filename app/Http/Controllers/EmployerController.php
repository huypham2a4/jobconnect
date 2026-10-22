<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployerController extends Controller
{
    protected function getEmployerCompany(): Company
    {
        $user = Auth::user();
        if (!$user->company) {
            // Tự động tạo công ty nếu chưa có
            $slug = Str::slug($user->name);
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $user->name . ' Company',
                'slug' => $slug . '-' . time(),
                'city' => 'TP. Hồ Chí Minh',
            ]);
            return $company;
        }
        return $user->company;
    }

    /**
     * Dashboard tổng quan Nhà tuyển dụng
     */
    public function dashboard()
    {
        $company = $this->getEmployerCompany();

        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $stats = [
            'total_jobs' => $jobIds->count(),
            'active_jobs' => JobPost::where('company_id', $company->id)->approved()->notExpired()->count(),
            'pending_jobs' => JobPost::where('company_id', $company->id)->where('status', 'pending')->count(),
            'total_applications' => Application::whereIn('job_post_id', $jobIds)->count(),
            'interviews_scheduled' => Interview::whereHas('application', function ($q) use ($jobIds) {
                $q->whereIn('job_post_id', $jobIds);
            })->count(),
        ];

        $recentJobs = JobPost::where('company_id', $company->id)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        $recentApplications = Application::whereIn('job_post_id', $jobIds)
            ->with(['jobPost', 'user', 'cv', 'interview'])
            ->latest()
            ->take(5)
            ->get();

        return view('employer.dashboard', compact('company', 'stats', 'recentJobs', 'recentApplications'));
    }

    /**
     * Danh sách tin tuyển dụng của công ty
     */
    public function jobs()
    {
        $company = $this->getEmployerCompany();
        $jobs = JobPost::where('company_id', $company->id)
            ->with(['category', 'location'])
            ->withCount('applications')
            ->latest()
            ->paginate(10);

        return view('employer.jobs.index', compact('company', 'jobs'));
    }

    /**
     * Form tạo tin tuyển dụng mới
     */
    public function createJob()
    {
        $company = $this->getEmployerCompany();
        $categories = Category::all();
        $locations = Location::all();
        $skills = Skill::all();

        return view('employer.jobs.create', compact('company', 'categories', 'locations', 'skills'));
    }

    /**
     * Lưu tin tuyển dụng mới
     */
    public function storeJob(Request $request)
    {
        $company = $this->getEmployerCompany();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'type' => ['required', 'in:full-time,part-time,remote,internship,hybrid'],
            'experience_level' => ['required', 'in:all,intern,fresher,junior,middle,senior,lead'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'salary_negotiable' => ['nullable', 'boolean'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'description' => ['required', 'string', 'min:30'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề vị trí tuyển dụng.',
            'category_id.required' => 'Vui lòng chọn ngành nghề.',
            'location_id.required' => 'Vui lòng chọn địa điểm làm việc.',
            'deadline.required' => 'Vui lòng chọn hạn nộp hồ sơ.',
            'deadline.after_or_equal' => 'Hạn nộp hồ sơ phải từ hôm nay trở đi.',
            'description.required' => 'Vui lòng nhập mô tả chi tiết công việc.',
            'description.min' => 'Mô tả công việc tối thiểu 30 ký tự.',
        ]);

        $slug = Str::slug($validated['title']);
        $count = JobPost::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $job = JobPost::create([
            'company_id' => $company->id,
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'experience_level' => $validated['experience_level'],
            'salary_min' => $validated['salary_min'] ? $validated['salary_min'] * 1000000 : null,
            'salary_max' => $validated['salary_max'] ? $validated['salary_max'] * 1000000 : null,
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'deadline' => $validated['deadline'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'],
            'benefits' => $validated['benefits'],
            'status' => 'approved', // Mặc định hiển thị hoặc pending
            'is_featured' => false,
        ]);

        if (!empty($validated['skills'])) {
            $job->skills()->sync($validated['skills']);
        }

        return redirect()->route('employer.jobs.index')->with('success', 'Đăng tin tuyển dụng thành công!');
    }

    /**
     * Form chỉnh sửa tin tuyển dụng
     */
    public function editJob($id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);
        $categories = Category::all();
        $locations = Location::all();
        $skills = Skill::all();
        $jobSkillIds = $job->skills->pluck('id')->toArray();

        return view('employer.jobs.edit', compact('company', 'job', 'categories', 'locations', 'skills', 'jobSkillIds'));
    }

    /**
     * Cập nhật tin tuyển dụng
     */
    public function updateJob(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'type' => ['required', 'in:full-time,part-time,remote,internship,hybrid'],
            'experience_level' => ['required', 'in:all,intern,fresher,junior,middle,senior,lead'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'salary_negotiable' => ['nullable', 'boolean'],
            'deadline' => ['required', 'date'],
            'description' => ['required', 'string', 'min:30'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        $job->update([
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'experience_level' => $validated['experience_level'],
            'salary_min' => $validated['salary_min'] ? $validated['salary_min'] * 1000000 : null,
            'salary_max' => $validated['salary_max'] ? $validated['salary_max'] * 1000000 : null,
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'deadline' => $validated['deadline'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'],
            'benefits' => $validated['benefits'],
        ]);

        if (isset($validated['skills'])) {
            $job->skills()->sync($validated['skills']);
        } else {
            $job->skills()->detach();
        }

        return redirect()->route('employer.jobs.index')->with('success', 'Cập nhật tin tuyển dụng thành công!');
    }

    /**
     * Đóng hoặc Mở lại tin tuyển dụng
     */
    public function toggleJobStatus($id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        $newStatus = $job->status === 'closed' ? 'approved' : 'closed';
        $job->update(['status' => $newStatus]);

        $msg = $newStatus === 'closed' ? 'Đã đóng tin tuyển dụng.' : 'Đã mở lại tin tuyển dụng.';
        return back()->with('success', $msg);
    }

    /**
     * Gia hạn tin tuyển dụng (kéo dài thời gian hiển thị thêm số ngày quy định)
     */
    public function renewJob(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        $days = (int) $request->input('extend_days', 30);
        $currentDeadline = Carbon::parse($job->deadline);
        $baseDate = $currentDeadline->isFuture() ? $currentDeadline : Carbon::now();
        $newDeadline = $baseDate->copy()->addDays($days)->format('Y-m-d');

        $job->update([
            'deadline' => $newDeadline,
            'status' => 'approved',
        ]);

        return back()->with('success', "Đã gia hạn tin tuyển dụng '{$job->title}' thêm {$days} ngày (đến ngày {$newDeadline}).");
    }

    /**
     * Danh sách ứng viên đã nộp hồ sơ
     */
    public function applications(Request $request, $job_id = null)
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $query = Application::whereIn('job_post_id', $jobIds)->with(['jobPost', 'user.skills', 'cv', 'interview']);

        if ($job_id) {
            $query->where('job_post_id', $job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(12)->withQueryString();
        $companyJobs = JobPost::where('company_id', $company->id)->get();

        return view('employer.applications.index', compact('company', 'applications', 'companyJobs', 'job_id'));
    }

    /**
     * Cập nhật trạng thái ứng viên
     * Lưu ý: Khi chuyển trạng thái từ 'submitted' sang 'viewed'/'interview'/'accepted',
     * thông tin SĐT và Email của ứng viên sẽ được mở khóa để nhà tuyển dụng liên hệ!
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $application = Application::whereIn('job_post_id', $jobIds)->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:submitted,viewed,interview,accepted,rejected'],
        ]);

        $application->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái hồ sơ ứng viên thành: ' . $application->status_label);
    }

    /**
     * Gửi lời mời phỏng vấn cho ứng viên
     */
    public function inviteInterview(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $application = Application::whereIn('job_post_id', $jobIds)->findOrFail($id);

        $validated = $request->validate([
            'interview_date' => ['required', 'date', 'after:now'],
            'interview_type' => ['required', 'in:online,offline'],
            'location_or_link' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'interview_date.required' => 'Vui lòng chọn thời gian phỏng vấn.',
            'interview_date.after' => 'Thời gian phỏng vấn phải ở tương lai.',
            'location_or_link.required' => 'Vui lòng cung cấp link họp trực tuyến hoặc địa chỉ phỏng vấn.',
        ]);

        // Cập nhật hoặc tạo mới lịch phỏng vấn
        Interview::updateOrCreate(
            ['application_id' => $application->id],
            [
                'interview_date' => $validated['interview_date'],
                'interview_type' => $validated['interview_type'],
                'location_or_link' => $validated['location_or_link'],
                'note' => $validated['note'],
                'status' => 'scheduled',
            ]
        );

        // Chuyển trạng thái hồ sơ sang 'interview'
        $application->update(['status' => 'interview']);

        return back()->with('success', 'Đã gửi lời mời phỏng vấn thành công cho ứng viên!');
    }

    /**
     * Trang chỉnh sửa thông tin doanh nghiệp
     */
    public function companyEdit()
    {
        $company = $this->getEmployerCompany();
        return view('employer.company', compact('company'));
    }

    /**
     * Lưu cập nhật thông tin doanh nghiệp
     */
    public function companyUpdate(Request $request)
    {
        $company = $this->getEmployerCompany();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'size' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:3000'],
        ], [
            'name.required' => 'Tên công ty không được để trống.',
            'city.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'website.url' => 'Địa chỉ website không hợp lệ.',
        ]);

        $company->update($validated);

        return back()->with('success', 'Cập nhật thông tin công ty thành công!');
    }
}
