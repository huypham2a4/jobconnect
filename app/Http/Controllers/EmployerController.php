<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\RolePermission;
use App\Models\Skill;
use App\Models\UserNotification;
use App\Services\JobMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerController extends Controller
{
    protected function getEmployerCompany(): Company
    {
        $user = Auth::user();
        if (! $user->company) {
            // Tự động tạo công ty nếu chưa có
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $user->name.' Company',
                'slug' => $this->uniqueSlug(Company::class, $user->name, null, 'cong-ty'),
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
     * Quy tắc kiểm tra dữ liệu tin tuyển dụng (dùng chung cho tạo mới và cập nhật).
     *
     * @return array<string, mixed>
     */
    protected function validateJob(Request $request, bool $isNew): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'type' => ['required', 'in:full-time,part-time,remote,internship,hybrid'],
            'experience_level' => ['required', 'in:all,intern,fresher,junior,middle,senior,lead'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'salary_max' => array_merge(
                ['nullable', 'numeric', 'min:0', 'max:1000'],
                $request->filled('salary_min') ? ['gte:salary_min'] : []
            ),
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
            'salary_min.max' => 'Lương tối thiểu nhập theo đơn vị triệu đồng và không quá 1000.',
            'salary_max.max' => 'Lương tối đa nhập theo đơn vị triệu đồng và không quá 1000.',
            'salary_max.gte' => 'Lương tối đa phải lớn hơn hoặc bằng lương tối thiểu.',
            'deadline.required' => 'Vui lòng chọn hạn nộp hồ sơ.',
            'deadline.after_or_equal' => $isNew
                ? 'Hạn nộp hồ sơ phải từ hôm nay trở đi.'
                : 'Hạn nộp hồ sơ phải từ hôm nay trở đi. Tin đã hết hạn cần chọn hạn mới trước khi gửi duyệt lại.',
            'description.required' => 'Vui lòng nhập mô tả chi tiết công việc.',
            'description.min' => 'Mô tả công việc tối thiểu 30 ký tự.',
        ]);
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
        $locations = Location::orderedForSelect();
        $skills = Skill::all();

        return view('employer.jobs.create', compact('company', 'categories', 'locations', 'skills'));
    }

    /**
     * Lưu tin tuyển dụng mới
     */
    public function storeJob(Request $request)
    {
        $company = $this->getEmployerCompany();

        $validated = $this->validateJob($request, true);

        $slug = $this->uniqueSlug(JobPost::class, $validated['title'], null, 'tin-tuyen-dung');

        $job = JobPost::create([
            'company_id' => $company->id,
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'experience_level' => $validated['experience_level'],
            'salary_min' => ($validated['salary_min'] ?? null) !== null ? $validated['salary_min'] * 1000000 : null,
            'salary_max' => ($validated['salary_max'] ?? null) !== null ? $validated['salary_max'] * 1000000 : null,
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'deadline' => $validated['deadline'],
            'description' => $validated['description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'status' => 'pending',
            'is_featured' => false,
        ]);

        if (! empty($validated['skills'])) {
            $job->skills()->sync($validated['skills']);
        }

        return redirect()->route('employer.jobs.index')->with('success', 'Đã gửi tin tuyển dụng. Tin sẽ hiển thị sau khi Admin phê duyệt.');
    }

    /**
     * Form chỉnh sửa tin tuyển dụng
     */
    public function editJob($id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);
        $categories = Category::all();
        $locations = Location::orderedForSelect();
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

        $validated = $this->validateJob($request, false);

        $job->update([
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'experience_level' => $validated['experience_level'],
            'salary_min' => ($validated['salary_min'] ?? null) !== null ? $validated['salary_min'] * 1000000 : null,
            'salary_max' => ($validated['salary_max'] ?? null) !== null ? $validated['salary_max'] * 1000000 : null,
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'deadline' => $validated['deadline'],
            'description' => $validated['description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'status' => 'pending',
            'rejection_note' => null,
        ]);

        if (isset($validated['skills'])) {
            $job->skills()->sync($validated['skills']);
        } else {
            $job->skills()->detach();
        }

        return redirect()->route('employer.jobs.index')->with('success', 'Đã cập nhật và gửi tin để Admin duyệt lại.');
    }

    /**
     * Đóng hoặc Mở lại tin tuyển dụng
     */
    public function toggleJobStatus($id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        if (! in_array($job->status, ['approved', 'closed'], true)) {
            return back()->with('error', 'Tin chưa được phê duyệt. Bạn có thể chỉnh sửa và gửi Admin duyệt lại.');
        }
        if ($job->status === 'closed' && $job->is_expired) {
            return back()->with('error', 'Tin đã quá hạn nộp hồ sơ. Hãy bấm Gia hạn trước, sau đó mới mở lại tin.');
        }

        $newStatus = $job->status === 'closed' ? 'pending' : 'closed';
        $job->update(['status' => $newStatus]);

        $msg = $newStatus === 'closed' ? 'Đã đóng tin tuyển dụng.' : 'Đã gửi yêu cầu mở lại. Vui lòng chờ Admin duyệt.';

        return back()->with('success', $msg);
    }

    /**
     * Gia hạn tin tuyển dụng (kéo dài thời gian hiển thị thêm số ngày quy định)
     */
    public function renewJob(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        $validated = $request->validate(['extend_days' => ['sometimes', 'integer', 'min:1', 'max:90']]);
        $days = (int) ($validated['extend_days'] ?? 30);
        $currentDeadline = Carbon::parse($job->deadline);
        $baseDate = $currentDeadline->isFuture() ? $currentDeadline : Carbon::now();
        $newDeadline = $baseDate->copy()->addDays($days)->format('Y-m-d');

        $job->update([
            'deadline' => $newDeadline,
        ]);

        return back()->with('success', "Đã gia hạn tin tuyển dụng '{$job->title}' thêm {$days} ngày (đến ngày {$newDeadline}).");
    }

    /**
     * Xóa tin tuyển dụng (chỉ cho phép khi tin chưa có hồ sơ ứng tuyển)
     */
    public function destroyJob($id)
    {
        $company = $this->getEmployerCompany();
        $job = JobPost::where('company_id', $company->id)->findOrFail($id);

        if ($job->applications()->exists()) {
            return back()->with('error', 'Không thể xóa tin đã có ứng viên nộp hồ sơ. Bạn có thể đóng tin để ngừng nhận hồ sơ.');
        }

        $job->skills()->detach();
        $job->delete();

        return redirect()->route('employer.jobs.index')->with('success', 'Đã xóa tin tuyển dụng thành công.');
    }

    /**
     * Danh sách ứng viên đã nộp hồ sơ
     */
    public function applications(Request $request, JobMatchingService $matcher, $job_id = null)
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $job_id = $job_id ?: ($request->filled('job_id') ? $request->integer('job_id') : null);

        $query = Application::whereIn('job_post_id', $jobIds)->with(['jobPost.skills', 'user.skills', 'cv', 'interview']);

        if ($job_id) {
            $query->where('job_post_id', $job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(12)->withQueryString();
        $applications->getCollection()->transform(function (Application $app) use ($matcher) {
            $app->setAttribute('match_analysis', $matcher->analyze($app->user, $app->jobPost));

            return $app;
        });

        $companyJobs = JobPost::where('company_id', $company->id)->get();

        return view('employer.applications.index', compact('company', 'applications', 'companyJobs', 'job_id'));
    }

    /**
     * Xuất danh sách ứng viên ra CSV (tôn trọng quy tắc ẩn thông tin liên hệ)
     */
    public function exportApplications(Request $request, JobMatchingService $matcher): StreamedResponse
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $query = Application::whereIn('job_post_id', $jobIds)
            ->with(['jobPost.skills', 'user.skills', 'interview'])
            ->latest();

        if ($request->filled('job_id')) {
            $query->where('job_post_id', $request->integer('job_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $applications = $query->get();
        $filename = 'ung-vien-'.$company->slug.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($applications, $matcher): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Ứng viên',
                'Vị trí ứng tuyển',
                'Độ phù hợp kỹ năng',
                'Trạng thái',
                'Email',
                'Số điện thoại',
                'Ngày nộp',
                'Lịch phỏng vấn',
            ]);

            foreach ($applications as $app) {
                $match = $matcher->analyze($app->user, $app->jobPost);
                $email = $app->isContactVisible() ? $app->user->email : Str::mask($app->user->email, '*', 3, 6);
                $phone = $app->isContactVisible() ? ($app->user->phone ?? '') : 'Ẩn (Chờ duyệt)';
                $interviewText = $app->interview?->interview_date?->format('H:i d/m/Y') ?? '';

                fputcsv($handle, [
                    $app->user->name,
                    $app->jobPost->title,
                    $match['score'].'%',
                    $app->status_label,
                    $email,
                    $phone,
                    $app->applied_at?->format('d/m/Y H:i') ?? '',
                    $interviewText,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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

        if ($application->status === 'withdrawn') {
            return back()->with('error', 'Ứng viên đã rút hồ sơ này.');
        }

        $request->validate([
            'status' => ['required', 'in:submitted,viewed,interview,accepted,rejected'],
        ]);

        $newStatus = $request->string('status')->toString();

        if ($newStatus === $application->status) {
            return back()->with('success', 'Trạng thái hồ sơ không thay đổi.');
        }

        // Hồ sơ đã được xử lý thì không quay lại "Chưa duyệt" (tránh ẩn lại liên hệ và mở lại quyền rút hồ sơ).
        if ($newStatus === 'submitted') {
            return back()->with('error', 'Hồ sơ đã được xử lý, không thể chuyển về trạng thái chưa duyệt.');
        }

        // "Mời phỏng vấn" phải đi kèm lịch cụ thể để ứng viên biết thời gian, địa điểm.
        if ($newStatus === 'interview' && ! $application->interview()->exists()) {
            return back()->with('error', 'Hãy dùng nút "Lên lịch phỏng vấn" để mời ứng viên kèm thời gian và địa điểm.');
        }

        $application->update([
            'status' => $newStatus,
        ]);

        $application->loadMissing('jobPost');
        UserNotification::notifyUser(
            $application->user_id,
            'application_status',
            'Cập nhật trạng thái hồ sơ',
            "Hồ sơ vị trí \"{$application->jobPost->title}\" vừa chuyển sang trạng thái: {$application->status_label}.",
            route('candidate.applications')
        );

        return back()->with('success', 'Đã cập nhật trạng thái hồ sơ ứng viên thành: '.$application->status_label);
    }

    /**
     * Gửi lời mời phỏng vấn cho ứng viên
     */
    public function inviteInterview(Request $request, $id)
    {
        $company = $this->getEmployerCompany();
        $jobIds = JobPost::where('company_id', $company->id)->pluck('id');

        $application = Application::whereIn('job_post_id', $jobIds)->findOrFail($id);

        $permission = $application->interview()->exists() ? 'interviews.update' : 'interviews.create';
        abort_unless(RolePermission::allows($request->user()->role, $permission), 403, 'Bạn chưa được cấp quyền thực hiện thao tác này với lịch phỏng vấn.');

        if ($application->status === 'withdrawn') {
            return back()->with('error', 'Ứng viên đã rút hồ sơ này.');
        }

        if (in_array($application->status, ['accepted', 'rejected'], true)) {
            return back()->with('error', 'Hồ sơ đã có kết quả ('.$application->status_label.'), không thể xếp lịch phỏng vấn.');
        }

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
                'note' => $validated['note'] ?? null,
                'status' => 'scheduled',
            ]
        );

        // Chuyển trạng thái hồ sơ sang 'interview'
        $application->update(['status' => 'interview']);

        $application->loadMissing('jobPost');
        $dateText = Carbon::parse($validated['interview_date'])->format('H:i, d/m/Y');
        UserNotification::notifyUser(
            $application->user_id,
            'interview_invited',
            'Lời mời phỏng vấn mới',
            "Bạn được mời phỏng vấn vị trí \"{$application->jobPost->title}\" lúc {$dateText} ({$validated['location_or_link']}).",
            route('candidate.applications')
        );

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
            // Cho phép giữ tỉnh cũ (dữ liệu trước khi có danh sách 34 tỉnh) để không bắt NTD sửa lại.
            'city' => ['required', 'string', 'max:100', Rule::in(array_filter([...config('provinces'), $company->city]))],
            'address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'size' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:3000'],
        ], [
            'name.required' => 'Tên công ty không được để trống.',
            'city.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'city.in' => 'Tỉnh/thành phố không có trong danh sách.',
            'website.url' => 'Địa chỉ website không hợp lệ.',
        ]);

        $company->update($validated);

        return back()->with('success', 'Cập nhật thông tin công ty thành công!');
    }
}
