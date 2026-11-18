<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\RolePermission;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpgradedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $candidate;

    private User $employer;

    private User $admin;

    private JobPost $job;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $this->candidate = User::where('email', 'ungvien@jobconnect.vn')->firstOrFail();
        $this->employer = User::where('email', 'fpt@jobconnect.vn')->firstOrFail();
        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->job = $this->employer->company->jobPosts()->latest('id')->firstOrFail();
    }

    public function test_skill_matching_renders_on_candidate_dashboard_job_detail_and_employer_applicants(): void
    {
        $fptSenior = JobPost::where('slug', 'senior-laravel-backend-engineer-fpt')->firstOrFail();

        $this->actingAs($this->candidate)
            ->get(route('candidate.dashboard'))
            ->assertOk()
            ->assertSee('Việc Làm Gợi Ý Theo Kỹ Năng')
            ->assertSee($fptSenior->title);

        $this->get(route('jobs.show', $fptSenior->id))
            ->assertOk()
            ->assertSee('Độ phù hợp kỹ năng')
            ->assertSee('80%');

        $this->actingAs($this->employer)
            ->get(route('employer.applications', ['job_id' => $fptSenior->id]))
            ->assertOk()
            ->assertSee('Khớp 80% kỹ năng');
    }

    public function test_job_search_filters_by_experience_level(): void
    {
        $response = $this->get('/jobs?experience_level=senior')->assertOk();
        $jobs = $response->viewData('jobs');
        $this->assertGreaterThan(0, $jobs->total());
        foreach ($jobs as $job) {
            $this->assertSame('senior', $job->experience_level);
        }
    }

    public function test_notifications_are_created_across_recruitment_lifecycle_and_can_be_read(): void
    {
        $this->actingAs($this->candidate)->post(route('jobs.apply', $this->job->id), [
            'cv_option' => 'existing',
            'existing_cv_id' => $this->candidate->primaryCv->id,
        ])->assertSessionHas('success');

        $employerNotif = UserNotification::where('user_id', $this->employer->id)->latest('id')->firstOrFail();
        $this->assertSame('application_submitted', $employerNotif->type);
        $this->assertNull($employerNotif->read_at);

        $application = Application::where('job_post_id', $this->job->id)->where('user_id', $this->candidate->id)->firstOrFail();

        $this->actingAs($this->employer)->post(route('employer.applications.invite', $application->id), [
            'interview_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'interview_type' => 'online',
            'location_or_link' => 'https://meet.google.com/test-notif',
        ])->assertSessionHas('success');

        $candidateNotif = UserNotification::where('user_id', $this->candidate->id)->latest('id')->firstOrFail();
        $this->assertSame('interview_invited', $candidateNotif->type);

        $this->actingAs($this->candidate)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee($candidateNotif->title);

        $this->post(route('notifications.read', $employerNotif->id))->assertNotFound();

        $this->post(route('notifications.read', $candidateNotif->id), ['go' => 1])
            ->assertRedirect(route('candidate.applications'));
        $this->assertNotNull($candidateNotif->fresh()->read_at);

        $this->actingAs($this->employer)
            ->post(route('notifications.readAll'))
            ->assertRedirect();
        $this->assertSame(0, UserNotification::where('user_id', $this->employer->id)->unread()->count());
    }

    public function test_admin_rejection_note_and_approval_notify_employer(): void
    {
        $this->job->update(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->post(route('admin.jobs.reject', $this->job->id), ['rejection_note' => 'Thiếu mô tả quyền lợi chi tiết'])
            ->assertSessionHas('success');

        $this->assertSame('rejected', $this->job->fresh()->status);
        $this->assertSame('Thiếu mô tả quyền lợi chi tiết', $this->job->fresh()->rejection_note);

        $this->actingAs($this->employer)
            ->get(route('employer.jobs.index'))
            ->assertOk()
            ->assertSee('Thiếu mô tả quyền lợi chi tiết');

        $this->actingAs($this->admin)
            ->post(route('admin.jobs.approve', $this->job->id))
            ->assertSessionHas('success');

        $this->assertNull($this->job->fresh()->rejection_note);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->employer->id,
            'type' => 'job_approved',
        ]);
    }

    public function test_public_company_directory_profile_and_admin_verification(): void
    {
        $company = $this->employer->company;
        $closedJob = $company->jobPosts()->where('id', '!=', $this->job->id)->firstOrFail();
        $closedJob->update(['status' => 'closed']);

        $this->get(route('companies.index', ['q' => 'FPT', 'verified' => 1]))
            ->assertOk()
            ->assertSee($company->name);

        $this->get(route('companies.show', $company->slug))
            ->assertOk()
            ->assertSee($company->name)
            ->assertSee($this->job->title)
            ->assertDontSee($closedJob->title);

        $this->actingAs($this->candidate)
            ->post(route('admin.users.verify-company', $company->id))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('admin.users.verify-company', $company->id))
            ->assertSessionHas('success');
        $this->assertFalse($company->fresh()->is_verified);

        $this->post(route('admin.users.verify-company', $company->id))
            ->assertSessionHas('success');
        $this->assertTrue($company->fresh()->is_verified);
    }

    public function test_forgot_reset_and_change_password_flows(): void
    {
        $response = $this->post(route('password.email'), ['email' => $this->candidate->email])
            ->assertRedirect()
            ->assertSessionHas('demo_reset_url');

        $resetUrl = $response->getSession()->get('demo_reset_url');
        $parsedPath = parse_url($resetUrl, PHP_URL_PATH);
        $token = basename($parsedPath);

        $this->get($resetUrl)->assertOk()->assertSee('Đặt Mật Khẩu Mới');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $this->candidate->email,
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertRedirect(route('login'))->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpass123', $this->candidate->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $this->candidate->email]);

        $this->actingAs($this->candidate->fresh())
            ->put(route('account.password.update'), [
                'current_password' => 'wrongpass',
                'password' => 'another123',
                'password_confirmation' => 'another123',
            ])->assertSessionHasErrors('current_password');

        $this->put(route('account.password.update'), [
            'current_password' => 'newpass123',
            'password' => 'another123',
            'password_confirmation' => 'another123',
        ])->assertSessionHas('success');

        $this->assertTrue(Hash::check('another123', $this->candidate->fresh()->password));
    }

    public function test_private_cv_download_enforces_ownership_and_applicant_access(): void
    {
        $cv = $this->candidate->primaryCv;
        $otherCandidate = User::where('email', 'bichngoc@jobconnect.vn')->firstOrFail();
        $otherEmployer = User::where('email', 'vng@jobconnect.vn')->firstOrFail();

        $this->get(route('cvs.download', $cv->id))->assertRedirect(route('login'));

        $this->actingAs($otherCandidate)->get(route('cvs.download', $cv->id))->assertForbidden();

        // FPT has an application from $this->candidate using $cv
        $this->actingAs($this->employer)->get(route('cvs.download', $cv->id))->assertOk();

        // Shopee has NO application from $this->candidate
        $shopee = User::where('email', 'shopee@jobconnect.vn')->firstOrFail();
        $this->actingAs($shopee)->get(route('cvs.download', $cv->id))->assertForbidden();

        // VNG has an application from $this->candidate using $cv
        $this->actingAs($otherEmployer)->get(route('cvs.download', $cv->id))->assertOk();

        $this->actingAs($this->candidate)->get(route('cvs.download', $cv->id))->assertOk();
        $this->actingAs($this->admin)->get(route('cvs.download', $cv->id))->assertOk();
    }

    public function test_employer_can_delete_unused_job_and_export_applicants_csv(): void
    {
        // Job with applications cannot be deleted
        $jobWithApp = JobPost::where('slug', 'senior-laravel-backend-engineer-fpt')->firstOrFail();
        $this->actingAs($this->employer)->delete(route('employer.jobs.destroy', $jobWithApp->id))->assertSessionHas('error');
        $this->assertDatabaseHas('job_posts', ['id' => $jobWithApp->id]);

        // Job without applications can be deleted
        $this->actingAs($this->employer)->delete(route('employer.jobs.destroy', $this->job->id))->assertRedirect(route('employer.jobs.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('job_posts', ['id' => $this->job->id]);

        // Export CSV works and respects contact privacy
        $csvResponse = $this->actingAs($this->employer)->get(route('employer.applications.export'))->assertOk();
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
    }

    public function test_admin_can_update_and_delete_empty_category(): void
    {
        $this->actingAs($this->admin)->post('/admin/categories', ['name' => 'Ngành Tạm Thời'])->assertSessionHas('success');
        $cat = Category::where('name', 'Ngành Tạm Thời')->firstOrFail();

        $this->put(route('admin.categories.update', $cat->id), ['name' => 'Ngành Đã Cập Nhật'])->assertSessionHas('success');
        $this->assertSame('nganh-da-cap-nhat', $cat->fresh()->slug);

        $this->delete(route('admin.categories.destroy', $cat->id))->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);

        $usedCategory = Category::whereHas('jobPosts')->firstOrFail();
        $this->delete(route('admin.categories.destroy', $usedCategory->id))->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $usedCategory->id]);
    }

    public function test_employer_filter_by_query_job_id_and_withdrawn_badge_display(): void
    {
        $fptSenior = JobPost::where('slug', 'senior-laravel-backend-engineer-fpt')->firstOrFail();
        $otherCompanyJob = $this->job;

        // Filter using GET query ?job_id=... (as submitted by the filter select form)
        $response = $this->actingAs($this->employer)
            ->get('/employer/applications?job_id='.$otherCompanyJob->id)
            ->assertOk();
        $this->assertSame(0, $response->viewData('applications')->total());

        $responseWithApp = $this->actingAs($this->employer)
            ->get('/employer/applications?job_id='.$fptSenior->id)
            ->assertOk();
        $this->assertSame(1, $responseWithApp->viewData('applications')->total());

        // When application is withdrawn, employer sees "Đã rút hồ sơ" badge instead of invite button
        $app = Application::where('job_post_id', $fptSenior->id)->firstOrFail();
        $app->update(['status' => 'withdrawn']);

        $this->actingAs($this->employer)
            ->get('/employer/applications?job_id='.$fptSenior->id)
            ->assertOk()
            ->assertSee('Đã rút hồ sơ')
            ->assertDontSee('Lên lịch phỏng vấn');
    }

    public function test_saved_jobs_reflect_on_company_show_and_register_role_query_preselects_employer(): void
    {
        $this->actingAs($this->candidate)->putJson('/candidate/saved-jobs/'.$this->job->id)->assertOk();

        $this->get(route('companies.show', $this->employer->company->slug))
            ->assertOk()
            ->assertSee('data-saved="true"', false);

        $this->post('/logout');

        $this->get('/register?role=employer')
            ->assertOk()
            ->assertSee('value="employer" checked', false);

        // Salary range overlap: 28 - 45 triệu job appears in both 25_40 and above_40 filters
        $fptSenior = JobPost::where('slug', 'senior-laravel-backend-engineer-fpt')->firstOrFail();
        $this->get('/jobs?salary_range=25_40')->assertOk()->assertSee($fptSenior->title);
        $this->get('/jobs?salary_range=above_40')->assertOk()->assertSee($fptSenior->title);
    }
}
