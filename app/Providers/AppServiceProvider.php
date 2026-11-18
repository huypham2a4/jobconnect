<?php

namespace App\Providers;

use App\Models\RolePermission;
use App\Models\UserNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('components.pagination');

        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();
            $accessPermissions = $user && $user->is_active ? RolePermission::forRole($user->role) : [];
            $unreadNotificationsCount = 0;
            $recentNotifications = collect();

            if ($user && $user->is_active && Schema::hasTable('user_notifications')) {
                $unreadNotificationsCount = UserNotification::where('user_id', $user->id)->unread()->count();
                $recentNotifications = UserNotification::where('user_id', $user->id)->latest()->take(5)->get();
            }

            $view->with(compact('accessPermissions', 'unreadNotificationsCount', 'recentNotifications'));
        });

        View::composer(['home', 'jobs.index', 'jobs.show', 'companies.show', 'candidate.saved'], function ($view): void {
            $savedJobIds = Auth::check() && Auth::user()->isCandidate()
                ? Auth::user()->savedJobs()->pluck('job_posts.id')->all()
                : [];
            $view->with('savedJobIds', $savedJobIds);
        });
    }
}
