<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\RolePermission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $candidate;

    private User $employer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->candidate = User::where('email', 'ungvien@jobconnect.vn')->firstOrFail();
        $this->employer = User::where('email', 'fpt@jobconnect.vn')->firstOrFail();
    }

    private function saveRole(string $role, array $permissions): void
    {
        $this->actingAs($this->admin)->post(route('admin.permissions.update'), ['role' => $role, 'permissions' => $permissions])
            ->assertRedirect(route('admin.permissions.index', ['role' => $role]))->assertSessionHas('success');
    }

    public function test_only_active_admin_can_view_or_update_permissions(): void
    {
        $this->get('/admin/permissions')->assertRedirect(route('login'));
        foreach ([$this->candidate, $this->employer] as $user) {
            $this->actingAs($user)->get('/admin/permissions')->assertForbidden();
            $this->post('/admin/permissions', ['role' => 'admin', 'permissions' => []])->assertForbidden();
        }
        $this->actingAs($this->admin)->get('/admin/permissions')->assertOk()->assertSee('Quyền theo người dùng');
        $this->admin->update(['is_active' => false]);
        $this->get('/admin/permissions')->assertRedirect(route('login'));
    }

    public function test_admin_can_revoke_and_restore_candidate_updates_without_disabling_other_actions(): void
    {
        $keys = RolePermission::availableKeys('candidate');
        $this->saveRole('candidate', array_values(array_diff($keys, ['profile.update'])));
        $original = $this->candidate->name;
        $this->actingAs($this->candidate)->get('/candidate/profile')->assertOk();
        $this->put('/candidate/profile', ['name' => 'Tên không được lưu'])->assertForbidden();
        $this->assertSame($original, $this->candidate->fresh()->name);
        $this->get('/candidate/cvs')->assertOk();
        $this->saveRole('candidate', $keys);
        $this->actingAs($this->candidate)->put('/candidate/profile', ['name' => 'Tên đã được cập nhật'])->assertRedirect()->assertSessionHas('success');
        $this->assertSame('Tên đã được cập nhật', $this->candidate->fresh()->name);
    }

    public function test_cv_update_and_delete_are_independent_and_seeding_keeps_revoked_permission(): void
    {
        RolePermission::where('role', 'candidate')->where('permission', 'cvs.delete')->delete();
        RolePermission::factory()->create(['role' => 'candidate', 'permission' => 'cvs.delete', 'is_allowed' => false]);
        $this->seed(RolePermissionSeeder::class);
        $cv = $this->candidate->primaryCv;
        $this->actingAs($this->candidate)->put('/candidate/cvs/'.$cv->id, ['title' => 'CV có tên mới'])->assertRedirect()->assertSessionHas('success');
        $this->delete('/candidate/cvs/'.$cv->id)->assertForbidden();
        $this->assertSame('CV có tên mới', $cv->fresh()->title);
        $this->assertFalse(RolePermission::allows('candidate', 'cvs.delete'));
    }

    public function test_a_missing_permission_record_does_not_grant_access(): void
    {
        RolePermission::where('role', 'candidate')->where('permission', 'profile.update')->delete();
        $this->assertFalse(RolePermission::allows('candidate', 'profile.update'));
        $this->actingAs($this->candidate)->put('/candidate/profile', ['name' => 'Không được lưu'])->assertForbidden();
        $this->assertSame('Nguyễn Văn An', $this->candidate->fresh()->name);
    }

    public function test_employer_job_updates_are_checked_even_with_direct_urls(): void
    {
        $this->saveRole('employer', array_values(array_diff(RolePermission::availableKeys('employer'), ['jobs.update'])));
        $job = $this->employer->company->jobPosts()->firstOrFail();
        $this->actingAs($this->employer)->get('/employer/jobs')->assertOk();
        $this->get('/employer/jobs/'.$job->id.'/edit')->assertForbidden();
        $this->put('/employer/jobs/'.$job->id, ['title' => 'Không được đổi'])->assertForbidden();
        $this->post('/employer/jobs/'.$job->id.'/toggle')->assertForbidden();
        $this->post('/employer/jobs/'.$job->id.'/renew')->assertForbidden();
        $this->assertSame('approved', $job->fresh()->status);
    }

    public function test_interview_creation_and_updating_have_separate_permissions(): void
    {
        $application = Application::whereHas('jobPost', fn ($query) => $query->where('company_id', $this->employer->company->id))->firstOrFail();
        $keys = RolePermission::availableKeys('employer');
        $this->saveRole('employer', array_values(array_diff($keys, ['interviews.update'])));
        $data = ['interview_date' => now()->addDays(5)->format('Y-m-d H:i:s'), 'interview_type' => 'online', 'location_or_link' => 'https://meeting.example/first'];
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/invite', $data)->assertRedirect()->assertSessionHas('success');
        $this->post('/employer/applications/'.$application->id.'/invite', array_replace($data, ['location_or_link' => 'https://meeting.example/blocked']))->assertForbidden();
        $this->assertSame($data['location_or_link'], $application->interview->location_or_link);
        $this->saveRole('employer', array_values(array_diff($keys, ['interviews.create'])));
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/invite', array_replace($data, ['location_or_link' => 'https://meeting.example/updated']))->assertRedirect()->assertSessionHas('success');
        $otherJob = $this->employer->company->jobPosts()->where('id', '!=', $application->job_post_id)->firstOrFail();
        $other = Application::create(['user_id' => $this->candidate->id, 'job_post_id' => $otherJob->id, 'cv_id' => $this->candidate->primaryCv->id, 'status' => 'submitted', 'applied_at' => now()]);
        $this->post('/employer/applications/'.$other->id.'/invite', $data)->assertForbidden();
        $this->assertFalse($other->interview()->exists());
    }

    public function test_admin_cannot_remove_essential_access_or_grant_unrelated_role_capabilities(): void
    {
        $this->saveRole('admin', []);
        $this->get('/admin/permissions')->assertOk();
        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/jobs')->assertForbidden();
        foreach (['dashboard.view', 'permissions.view', 'permissions.update'] as $key) {
            $this->assertTrue(RolePermission::allows('admin', $key));
        }
        $this->post('/admin/permissions', ['role' => 'candidate', 'permissions' => ['moderation.update']])->assertSessionHasErrors('permissions.0');
        $this->post('/admin/permissions', ['role' => 'super-admin', 'permissions' => []])->assertSessionHasErrors('role');
        $this->assertFalse(RolePermission::allows('candidate', 'moderation.update'));
        $this->actingAs($this->candidate)->get('/candidate/cvs')->assertOk();
        $this->get('/admin/permissions')->assertForbidden();
    }

    public function test_login_uses_an_allowed_page_and_falls_back_to_home_when_all_optional_access_is_revoked(): void
    {
        RolePermission::where('role', 'candidate')->where('permission', 'dashboard.view')->update(['is_allowed' => false]);
        $this->post('/login', ['email' => $this->candidate->email, 'password' => 'password'])->assertRedirect(route('candidate.profile'));
        $this->post('/logout');
        RolePermission::where('role', 'candidate')->update(['is_allowed' => false]);
        $this->post('/login', ['email' => $this->candidate->email, 'password' => 'password'])->assertRedirect(route('home'));
        $this->get('/')->assertOk();
        $this->get('/candidate/dashboard')->assertForbidden();
    }

    public function test_users_can_be_searched_paginated_and_inspected_including_locked_accounts(): void
    {
        $people = User::factory()->count(12)->create(['name' => 'Ứng viên lọc quyền']);
        $locked = $people->first();
        $locked->update(['is_active' => false]);
        $this->actingAs($this->admin)->get('/admin/permissions?role=candidate&q='.urlencode('Ứng viên lọc quyền').'&page=2')
            ->assertOk()->assertViewHas('users', fn ($users): bool => $users->total() === 12 && $users->count() === 2);
        $this->get('/admin/permissions?role=candidate&status=locked&user_id='.$locked->id)->assertOk()
            ->assertSee('Các chức năng cần đăng nhập đang bị tạm ngưng')->assertViewHas('users', fn ($users): bool => $users->total() === 1);
    }

    public function test_every_role_protected_route_has_a_real_capability_mapping(): void
    {
        $map = config('role_permissions.routes');
        foreach (Route::getRoutes() as $route) {
            $roles = collect($route->gatherMiddleware())->filter(fn ($middleware): bool => is_string($middleware) && str_starts_with($middleware, 'role:'));
            if ($roles->isEmpty()) {
                continue;
            }
            $this->assertContains('permission', $route->gatherMiddleware(), $route->getName());
            $this->assertArrayHasKey($route->getName(), $map);
            foreach ($roles as $middleware) {
                foreach (explode(',', substr($middleware, 5)) as $role) {
                    foreach ((array) $map[$route->getName()] as $key) {
                        $this->assertContains($key, RolePermission::availableKeys($role), $route->getName());
                    }
                }
            }
        }
    }
}
