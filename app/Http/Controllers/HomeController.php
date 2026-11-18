<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $colors = ['#2563eb', '#f97316', '#ea580c', '#d946ef', '#059669', '#0891b2', '#7c3aed'];

        // Thống kê thật từ Database
        $stats = [
            'jobs_count' => number_format(JobPost::active()->count()),
            'companies_count' => number_format(Company::count()),
            'resumes_count' => number_format(User::where('role', 'candidate')->count()),
        ];

        // Danh mục ngành nghề kèm số lượng việc làm
        $categoriesFromDb = Category::withCount(['jobPosts' => function ($q) {
            $q->active();
        }])->get();

        $categories = $categoriesFromDb->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'jobs_count' => number_format($cat->job_posts_count),
                'icon' => $cat->icon ?? 'fa-solid fa-briefcase',
                'bg_class' => $cat->bg_class ?? 'icon-blue',
                'slug' => $cat->slug,
            ];
        });

        // Việc làm nổi bật (kèm quan hệ company, category, location, skills)
        $jobsFromDb = JobPost::with(['company', 'category', 'location', 'skills'])
            ->active()
            ->latest()
            ->take(6)
            ->get();

        $featuredJobs = $jobsFromDb->map(function ($job, $index) use ($colors) {
            $compName = $job->company ? $job->company->name : 'Doanh nghiệp';
            $words = explode(' ', $compName);
            $shortName = count($words) > 1
                ? mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1))
                : mb_strtoupper(mb_substr($compName, 0, 2));

            return [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'company' => $compName,
                'company_short' => $shortName,
                'color' => $colors[$index % count($colors)],
                'location' => $job->location ? $job->location->name : 'Toàn quốc',
                'salary' => $job->salary_text,
                'type' => ucfirst($job->type),
                'tags' => $job->skills->pluck('name')->toArray(),
                'deadline' => $job->days_left_text,
                'category' => $job->category ? $job->category->slug : 'all',
                'is_urgent' => $job->is_featured,
            ];
        });

        // Top nhà tuyển dụng hàng đầu
        $companiesFromDb = Company::withCount(['jobPosts' => function ($q) {
            $q->active();
        }])
            ->orderByDesc('job_posts_count')
            ->take(4)
            ->get();

        $topCompanies = $companiesFromDb->map(function ($comp, $index) use ($colors) {
            $words = explode(' ', $comp->name);
            $shortName = count($words) > 1
                ? mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1))
                : mb_strtoupper(mb_substr($comp->name, 0, 2));

            return [
                'name' => $comp->name,
                'slug' => $comp->slug,
                'is_verified' => (bool) $comp->is_verified,
                'avatar' => $shortName,
                'color' => $colors[$index % count($colors)],
                'location' => $comp->city ?? $comp->address ?? 'Toàn quốc',
                'jobs_count' => $comp->job_posts_count,
            ];
        });

        $locations = Location::orderedForSelect();

        return view('home', compact('stats', 'categories', 'featuredJobs', 'topCompanies', 'locations'));
    }
}
