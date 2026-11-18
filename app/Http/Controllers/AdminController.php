<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function permissions(Request $request): View
    {
        $roles = config('role_permissions.roles');
        $validated = $request->validate([
            'role' => ['nullable', Rule::in(array_keys($roles))],
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in(['active', 'locked'])],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $selectedUser = isset($validated['user_id']) ? User::with('company')->findOrFail($validated['user_id']) : null;
        $selectedRole = $validated['role'] ?? $selectedUser?->role ?? 'candidate';
        $resources = config('role_permissions.resources');
        $rolePermissions = [];
        foreach (array_keys($roles) as $role) {
            $rolePermissions[$role] = RolePermission::forRole($role);
        }
        $roleStats = User::select('role', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'))
            ->groupBy('role')->get()->keyBy('role');
        $query = User::with('company')->where('role', $selectedRole);
        if ($request->filled('q')) {
            $term = trim($validated['q']);
            $query->where(function ($query) use ($term): void {
                $query->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%');
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $validated['status'] === 'active');
        }
        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.permissions', compact('roles', 'resources', 'selectedRole', 'selectedUser', 'rolePermissions', 'roleStats', 'users'));
    }

    public function updatePermissions(Request $request): RedirectResponse
    {
        $role = $request->validate(['role' => ['required', Rule::in(array_keys(config('role_permissions.roles')))]])['role'];
        $keys = RolePermission::availableKeys($role);
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in($keys)],
        ]);
        $selected = $validated['permissions'] ?? [];
        $required = config('role_permissions.required.'.$role, []);
        DB::transaction(function () use ($role, $keys, $selected, $required): void {
            foreach ($keys as $permission) {
                RolePermission::updateOrCreate(['role' => $role, 'permission' => $permission], [
                    'is_allowed' => in_array($permission, $selected, true) || in_array($permission, $required, true),
                ]);
            }
        });

        return redirect()->route('admin.permissions.index', ['role' => $role])->with('success', 'Đã cập nhật quyền cho vai trò '.config('role_permissions.roles.'.$role.'.label').'. Thay đổi có hiệu lực với mọi tài khoản cùng vai trò.');
    }

    /**
     * Bảng điều khiển quản trị hệ thống
     * Áp dụng thống kê bằng join/group by theo đúng quy định Lab & Báo cáo
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'candidates_count' => User::where('role', 'candidate')->count(),
            'employers_count' => User::where('role', 'employer')->count(),
            'active_jobs' => JobPost::approved()->notExpired()->count(),
            'pending_jobs' => JobPost::where('status', 'pending')->count(),
            'total_applications' => Application::count(),
        ];

        // 1. Thống kê số tin tuyển dụng theo ngành nghề (Dùng JOIN và GROUP BY)
        $categoryStats = Category::join('job_posts', 'categories.id', '=', 'job_posts.category_id')
            ->select('categories.name', DB::raw('count(job_posts.id) as job_count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('job_count')
            ->get();

        // 2. Thống kê tỷ lệ ứng tuyển theo ngành nghề (Dùng JOIN nhiều bảng và GROUP BY)
        $applicationStats = Category::join('job_posts', 'categories.id', '=', 'job_posts.category_id')
            ->join('applications', 'job_posts.id', '=', 'applications.job_post_id')
            ->select('categories.name as category_name', DB::raw('count(applications.id) as total_applications'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_applications')
            ->take(5)
            ->get();

        // 3. Tin tuyển dụng chờ duyệt
        $pendingJobs = JobPost::with(['company', 'category', 'location'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // 4. Người dùng mới đăng ký
        $recentUsers = User::latest()->take(6)->get();

        // 5. Phân bổ trạng thái hồ sơ ứng tuyển (JOIN / GROUP BY)
        $applicationStatusStats = Application::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // 6. Top 5 tin có lượt xem cao nhất
        $topViewedJobs = JobPost::with('company')
            ->approved()
            ->notExpired()
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'categoryStats', 'applicationStats', 'pendingJobs', 'recentUsers', 'applicationStatusStats', 'topViewedJobs'));
    }

    /**
     * Quản lý danh sách tin tuyển dụng
     */
    public function jobs(Request $request)
    {
        $query = JobPost::with(['company', 'category', 'location'])->withCount('applications');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('company', function ($c) use ($keyword) {
                        $c->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Duyệt tin tuyển dụng
     */
    public function approveJob($id)
    {
        $job = JobPost::with('company')->findOrFail($id);

        // Tin đã đóng là do nhà tuyển dụng chủ động ngừng tuyển; Admin không tự mở lại.
        if (! in_array($job->status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Chỉ duyệt được tin đang chờ duyệt hoặc đã từ chối trước đó.');
        }

        $job->update([
            'status' => 'approved',
            'rejection_note' => null,
        ]);

        $visibility = $job->is_expired
            ? 'nhưng đã quá hạn nộp nên chưa hiển thị công khai. Hãy gia hạn tin để ứng viên nhìn thấy.'
            : 'và hiển thị công khai.';

        UserNotification::notifyUser(
            $job->company?->user_id,
            'job_approved',
            'Tin tuyển dụng đã được duyệt',
            "Tin \"{$job->title}\" đã được Admin phê duyệt {$visibility}",
            route('jobs.show', $job->id)
        );

        $message = 'Đã phê duyệt tin tuyển dụng: '.$job->title;
        if ($job->is_expired) {
            $message .= ' (lưu ý: tin đã quá hạn nộp nên chưa hiển thị công khai).';
        }

        return back()->with('success', $message);
    }

    /**
     * Từ chối tin tuyển dụng
     */
    public function rejectJob(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        $job = JobPost::with('company')->findOrFail($id);

        if (! in_array($job->status, ['pending', 'approved'], true)) {
            return back()->with('error', 'Chỉ từ chối được tin đang chờ duyệt hoặc đang hiển thị.');
        }

        $note = isset($validated['rejection_note']) ? trim($validated['rejection_note']) : null;
        $job->update([
            'status' => 'rejected',
            'rejection_note' => $note ?: null,
        ]);

        $msg = "Tin \"{$job->title}\" đã bị từ chối.";
        if ($note) {
            $msg .= " Lý do: {$note}";
        }

        UserNotification::notifyUser(
            $job->company?->user_id,
            'job_rejected',
            'Tin tuyển dụng cần chỉnh sửa',
            $msg,
            route('employer.jobs.edit', $job->id)
        );

        return back()->with('success', 'Đã từ chối tin tuyển dụng: '.$job->title);
    }

    /**
     * Quản lý người dùng
     */
    public function users(Request $request)
    {
        $query = User::with('company')->withCount(['applications', 'skills']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Khóa hoặc Mở khóa tài khoản người dùng
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() && $user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'Mở khóa' : 'Khóa';

        return back()->with('success', "Đã {$statusText} tài khoản của {$user->name} thành công.");
    }

    /**
     * Bật/tắt xác thực doanh nghiệp của nhà tuyển dụng
     */
    public function toggleCompanyVerify($id)
    {
        $company = Company::findOrFail($id);
        $company->update(['is_verified' => ! $company->is_verified]);

        $statusText = $company->is_verified ? 'Xác thực' : 'Hủy xác thực';

        return back()->with('success', "Đã {$statusText} doanh nghiệp {$company->name}.");
    }

    /**
     * Quản lý danh mục ngành nghề
     */
    public function categories()
    {
        $categories = Category::withCount('jobPosts')->latest()->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Thêm mới danh mục ngành nghề
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:100', 'regex:/^fa-(solid|regular|brands) fa-[a-z0-9-]+$/'],
            'bg_class' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập tên ngành nghề.',
            'name.unique' => 'Tên ngành nghề này đã tồn tại.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug(Category::class, $validated['name'], null, 'nganh-nghe'),
            'icon' => $validated['icon'] ?? 'fa-solid fa-briefcase',
            'bg_class' => $validated['bg_class'] ?? 'icon-blue',
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Đã thêm mới danh mục thành công!');
    }

    /**
     * Cập nhật danh mục ngành nghề
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'icon' => ['nullable', 'string', 'max:100', 'regex:/^fa-(solid|regular|brands) fa-[a-z0-9-]+$/'],
            'bg_class' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập tên ngành nghề.',
            'name.unique' => 'Tên ngành nghề này đã tồn tại.',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug(Category::class, $validated['name'], $category->id, 'nganh-nghe'),
            'icon' => ($validated['icon'] ?? null) ?: ($category->icon ?: 'fa-solid fa-briefcase'),
            'bg_class' => ($validated['bg_class'] ?? null) ?: ($category->bg_class ?? 'icon-blue'),
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Đã cập nhật ngành nghề: '.$category->name);
    }

    /**
     * Xóa danh mục ngành nghề (chỉ khi chưa có tin tuyển dụng gắn với ngành)
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->jobPosts()->exists()) {
            return back()->with('error', 'Không thể xóa ngành nghề đang có tin tuyển dụng.');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa ngành nghề thành công.');
    }
}
