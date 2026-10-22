<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
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

        return view('admin.dashboard', compact('stats', 'categoryStats', 'applicationStats', 'pendingJobs', 'recentUsers'));
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
        $job = JobPost::findOrFail($id);
        $job->update(['status' => 'approved']);

        return back()->with('success', 'Đã phê duyệt tin tuyển dụng: ' . $job->title);
    }

    /**
     * Từ chối tin tuyển dụng
     */
    public function rejectJob($id)
    {
        $job = JobPost::findOrFail($id);
        $job->update(['status' => 'rejected']);

        return back()->with('success', 'Đã từ chối tin tuyển dụng: ' . $job->title);
    }

    /**
     * Quản lý người dùng
     */
    public function users(Request $request)
    {
        $query = User::withCount(['applications', 'skills']);

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

        $user->update(['is_active' => !$user->is_active]);

        $statusText = $user->is_active ? 'Mở khóa' : 'Khóa';
        return back()->with('success', "Đã {$statusText} tài khoản của {$user->name} thành công.");
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
            'icon' => ['nullable', 'string', 'max:100'],
            'bg_class' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập tên ngành nghề.',
            'name.unique' => 'Tên ngành nghề này đã tồn tại.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? 'fa-solid fa-briefcase',
            'bg_class' => $validated['bg_class'] ?? 'icon-blue',
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Đã thêm mới danh mục thành công!');
    }
}
