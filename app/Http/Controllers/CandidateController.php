<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Cv;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Dashboard tổng quan của Ứng viên
     */
    public function dashboard()
    {
        $user = Auth::user();
        $applications = Application::with(['jobPost.company', 'interview'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_applications' => Application::where('user_id', $user->id)->count(),
            'interview_invites' => Application::where('user_id', $user->id)->where('status', 'interview')->count(),
            'accepted_count' => Application::where('user_id', $user->id)->where('status', 'accepted')->count(),
            'cv_count' => Cv::where('user_id', $user->id)->count(),
        ];

        $userSkills = $user->skills;
        $primaryCv = $user->primaryCv;

        return view('candidate.dashboard', compact('user', 'applications', 'stats', 'userSkills', 'primaryCv'));
    }

    /**
     * Danh sách lịch sử nộp hồ sơ
     */
    public function applications()
    {
        $user = Auth::user();
        $applications = Application::with(['jobPost.company', 'jobPost.location', 'interview', 'cv'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('candidate.applications', compact('applications'));
    }

    /**
     * Rút hồ sơ ứng tuyển
     * Quy tắc nghiệp vụ: Chỉ được rút khi hồ sơ ở trạng thái 'submitted' (chưa được NTD duyệt)
     */
    public function withdraw($id)
    {
        $application = Application::where('user_id', Auth::id())->findOrFail($id);

        if (!$application->canWithdraw()) {
            return back()->with('error', 'Không thể rút hồ sơ: Nhà tuyển dụng đã tiếp nhận xử lý hồ sơ này.');
        }

        $application->update([
            'status' => 'withdrawn',
        ]);

        return back()->with('success', 'Đã rút hồ sơ ứng tuyển thành công.');
    }

    /**
     * Quản lý CV
     */
    public function cvs()
    {
        $user = Auth::user();
        $cvs = Cv::where('user_id', $user->id)->latest()->get();
        return view('candidate.cvs', compact('cvs'));
    }

    /**
     * Tải lên CV mới
     */
    public function storeCv(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'title.required' => 'Vui lòng đặt tên cho hồ sơ CV.',
            'cv_file.required' => 'Vui lòng chọn tệp tin CV từ máy tính.',
            'cv_file.mimes' => 'Hồ sơ phải có định dạng .pdf, .doc hoặc .docx.',
            'cv_file.max' => 'Dung lượng tệp không được vượt quá 5MB.',
        ]);

        $file = $request->file('cv_file');
        $filename = 'cv_' . Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('cvs', $filename, 'public');

        $isFirst = !Cv::where('user_id', Auth::id())->exists();

        Cv::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'is_primary' => $isFirst,
        ]);

        return back()->with('success', 'Tải lên CV mới thành công!');
    }

    /**
     * Chỉnh sửa tên hồ sơ CV
     */
    public function updateCv(Request $request, $id)
    {
        $cv = Cv::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Vui lòng nhập tên hồ sơ CV.',
        ]);

        $cv->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Đã cập nhật tiêu đề CV thành công.');
    }

    /**
     * Xóa CV
     */
    public function deleteCv($id)
    {
        $cv = Cv::where('user_id', Auth::id())->findOrFail($id);

        if ($cv->file_path && Storage::disk('public')->exists($cv->file_path)) {
            Storage::disk('public')->delete($cv->file_path);
        }

        $cv->delete();

        return back()->with('success', 'Đã xóa CV thành công.');
    }

    /**
     * Đặt CV làm mặc định
     */
    public function setPrimaryCv($id)
    {
        Cv::where('user_id', Auth::id())->update(['is_primary' => false]);
        Cv::where('user_id', Auth::id())->where('id', $id)->update(['is_primary' => true]);

        return back()->with('success', 'Đã đặt hồ sơ này làm CV mặc định.');
    }

    /**
     * Trang cập nhật hồ sơ cá nhân & kỹ năng
     */
    public function profile()
    {
        $user = Auth::user();
        $skills = Skill::all();
        $userSkillIds = $user->skills->pluck('id')->toArray();

        return view('candidate.profile', compact('user', 'skills', 'userSkillIds'));
    }

    /**
     * Lưu cập nhật hồ sơ cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
        ]);

        // Cập nhật kỹ năng ứng viên
        if (isset($validated['skills'])) {
            $user->skills()->sync($validated['skills']);
        } else {
            $user->skills()->detach();
        }

        return back()->with('success', 'Cập nhật thông tin hồ sơ và kỹ năng thành công!');
    }
}
