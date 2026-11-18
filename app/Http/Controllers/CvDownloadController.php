<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class CvDownloadController extends Controller
{
    public function show(int $id): Response
    {
        $cv = Cv::findOrFail($id);
        $user = Auth::user();

        // Nhà tuyển dụng chỉ xem được CV gắn với hồ sơ còn hiệu lực (chưa bị ứng viên rút) nộp vào công ty mình.
        $canAccess = $user->isAdmin()
            || $cv->user_id === $user->id
            || ($user->isEmployer() && $cv->applications()
                ->where('status', '!=', 'withdrawn')
                ->whereHas('jobPost.company', fn ($q) => $q->where('user_id', $user->id))
                ->exists());

        abort_unless($canAccess, 403, 'Bạn không có quyền truy cập tệp CV này.');

        if (! $cv->file_path || ! Storage::disk('public')->exists($cv->file_path)) {
            abort(404, 'Không tìm thấy tệp CV.');
        }

        $extension = $cv->file_type ?: pathinfo($cv->file_path, PATHINFO_EXTENSION) ?: 'pdf';
        $title = preg_replace('/\.(pdf|docx?|PDF|DOCX?)$/', '', $cv->title ?: 'cv');
        $safeName = trim(preg_replace('/[^a-zA-Z0-9_\-\p{L} ]/u', '_', $title)) ?: 'cv';
        $downloadName = $safeName.'.'.$extension;
        $asciiName = (Str::slug(Str::ascii($safeName)) ?: 'cv').'.'.$extension;

        if (strtolower($extension) === 'pdf') {
            return response(Storage::disk('public')->get($cv->file_path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, $downloadName, $asciiName),
            ]);
        }

        return Storage::disk('public')->download($cv->file_path, $downloadName);
    }
}
