<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedJobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = $request->user()->savedJobs()
            ->with(['company', 'category', 'location', 'skills'])
            ->orderByPivot('created_at', 'desc')->paginate(9);

        return view('candidate.saved', compact('jobs'));
    }

    public function store(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $job = JobPost::active()->findOrFail($id);
        $request->user()->savedJobs()->syncWithoutDetaching([$job->id]);

        return $request->expectsJson()
            ? response()->json(['saved' => true, 'message' => 'Đã lưu việc làm vào danh sách của bạn.'])
            : back()->with('success', 'Đã lưu việc làm.');
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->user()->savedJobs()->detach($id);

        return $request->expectsJson()
            ? response()->json(['saved' => false, 'message' => 'Đã bỏ lưu việc làm.'])
            : back()->with('success', 'Đã bỏ lưu việc làm.');
    }
}
