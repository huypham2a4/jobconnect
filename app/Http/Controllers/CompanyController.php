<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Company::withCount(['jobPosts as active_jobs_count' => function ($q): void {
            $q->active();
        }]);

        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city')->toString());
        }

        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        $companies = $query->orderByDesc('is_verified')
            ->orderByDesc('active_jobs_count')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Đủ 34 tỉnh thành theo thứ tự chuẩn, cộng thêm tỉnh cũ còn lưu ở công ty (nếu có).
        $cities = collect(config('provinces'))
            ->merge(Company::whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city'))
            ->unique()
            ->values();

        $locations = Location::orderedForSelect();

        return view('companies.index', compact('companies', 'cities', 'locations'));
    }

    public function show(string $slug): View
    {
        $company = Company::where('slug', $slug)
            ->when(ctype_digit($slug), fn ($q) => $q->orWhere('id', (int) $slug))
            ->firstOrFail();

        $jobs = $company->jobPosts()
            ->with(['company', 'category', 'location', 'skills'])
            ->active()
            ->latest()
            ->paginate(9);

        $totalViews = (int) $company->jobPosts()->sum('views_count');

        return view('companies.show', compact('company', 'jobs', 'totalViews'));
    }
}
