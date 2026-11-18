<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoCvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class JobConnectTest extends TestCase
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

    private function jobData(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Lập trình viên kiểm thử quy trình',
            'category_id' => Category::firstOrFail()->id,
            'location_id' => $this->job->location_id,
            'type' => 'full-time', 'experience_level' => 'junior',
            'salary_min' => 12, 'salary_max' => 20,
            'description' => 'Công việc phát triển website PHP và kiểm thử các chức năng tuyển dụng.',
            'requirements' => 'PHP, MySQL và Git', 'benefits' => 'Đào tạo và hỗ trợ học tập.',
            'deadline' => now()->addDays(20)->toDateString(),
            'skills' => Skill::take(2)->pluck('id')->all(),
        ], $overrides);
    }

    private function pdfUpload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('ho-so.pdf', file_get_contents(database_path('seeders/fixtures/sample_cv_nguyen_van_an.pdf')));
    }

    private function applyToJob(): Application
    {
        $this->actingAs($this->candidate)->post(route('jobs.apply', $this->job->id), [
            'cv_option' => 'existing', 'existing_cv_id' => $this->candidate->primaryCv->id,
            'cover_letter' => 'Tôi muốn ứng tuyển công việc này.',
        ])->assertRedirect()->assertSessionHas('success');

        return Application::where('user_id', $this->candidate->id)->where('job_post_id', $this->job->id)->latest('id')->firstOrFail();
    }

    public static function pages(): array
    {
        return [
            'home' => [null, '/'], 'search' => [null, '/jobs'],
            'detail' => [null, '/jobs/{job}'], 'login' => [null, '/login'], 'register' => [null, '/register'],
            'candidate dashboard' => ['candidate', '/candidate/dashboard'],
            'candidate profile' => ['candidate', '/candidate/profile'],
            'candidate cvs' => ['candidate', '/candidate/cvs'],
            'candidate saved' => ['candidate', '/candidate/saved-jobs'],
            'candidate applications' => ['candidate', '/candidate/applications'],
            'employer dashboard' => ['employer', '/employer/dashboard'],
            'employer jobs' => ['employer', '/employer/jobs'],
            'employer create' => ['employer', '/employer/jobs/create'],
            'employer edit' => ['employer', '/employer/jobs/{job}/edit'],
            'employer company' => ['employer', '/employer/company'],
            'employer applications' => ['employer', '/employer/applications'],
            'admin dashboard' => ['admin', '/admin/dashboard'],
            'admin jobs' => ['admin', '/admin/jobs'], 'admin users' => ['admin', '/admin/users'],
            'admin categories' => ['admin', '/admin/categories'],
        ];
    }

    #[DataProvider('pages')]
    public function test_pages_render_for_the_correct_role(?string $role, string $path): void
    {
        if ($role !== null) {
            $this->actingAs($this->{$role});
        }
        $this->get(str_replace('{job}', (string) $this->job->id, $path))->assertOk();
    }

    public function test_search_filters_sorting_and_empty_results(): void
    {
        $this->get('/jobs?q='.urlencode($this->job->title))->assertOk()->assertSee($this->job->title);
        $response = $this->get('/jobs?category='.$this->job->category->slug.'&location='.$this->job->location->slug.'&type=full-time&sort=salary_high');
        $response->assertOk();
        foreach ($response->viewData('jobs') as $job) {
            $this->assertSame($this->job->category_id, $job->category_id);
            $this->assertSame($this->job->location_id, $job->location_id);
            $this->assertSame('full-time', $job->type);
        }
        $this->get('/jobs?q=khong-co-cong-viec-xyz123')->assertOk()->assertSee('Không tìm thấy');
        $this->get('/jobs?salary_range=15_25&sort=deadline')->assertOk();
    }

    public function test_pagination_keeps_search_parameters(): void
    {
        for ($i = 0; $i < 12; $i++) {
            $this->actingAs($this->employer)->post('/employer/jobs', $this->jobData(['title' => 'Pagination PHP '.$i]))->assertSessionHas('success');
            $created = JobPost::latest('id')->firstOrFail();
            $this->actingAs($this->admin)->post('/admin/jobs/'.$created->id.'/approve')->assertSessionHas('success');
        }
        $response = $this->get('/jobs?q=Pagination&sort=deadline&page=2')->assertOk();
        $this->assertSame(12, $response->viewData('jobs')->total());
        $this->assertSame(3, $response->viewData('jobs')->count());
        $this->assertStringContainsString('q=Pagination', $response->viewData('jobs')->url(1));
    }

    public function test_register_candidate_and_password_hashing(): void
    {
        $email = fake()->unique()->safeEmail();
        $this->post('/register', ['name' => 'Ứng viên mới', 'email' => $email, 'role' => 'candidate', 'password' => 'secret123', 'password_confirmation' => 'secret123'])
            ->assertRedirect(route('candidate.dashboard'));
        $user = User::where('email', $email)->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_register_employer_creates_company(): void
    {
        $this->post('/register', ['name' => 'Nhà tuyển dụng mới', 'email' => 'new-employer@example.com', 'role' => 'employer', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'company_name' => 'Công ty kiểm thử', 'city' => 'TP. Hồ Chí Minh'])
            ->assertRedirect(route('employer.dashboard'));
        $this->assertDatabaseHas('companies', ['name' => 'Công ty kiểm thử']);
    }

    public function test_registration_rejects_admin_role_duplicate_email_and_bad_confirmation(): void
    {
        $this->post('/register', ['name' => 'Không hợp lệ', 'email' => $this->admin->email, 'role' => 'admin', 'password' => 'abc123', 'password_confirmation' => 'different'])
            ->assertSessionHasErrors(['email', 'role', 'password']);
        $this->assertGuest();
    }

    public function test_all_three_demo_accounts_can_login_and_logout(): void
    {
        foreach ([$this->candidate, $this->employer, $this->admin] as $user) {
            $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route($user->role.'.dashboard'));
            $this->assertAuthenticatedAs($user);
            $this->post('/logout')->assertRedirect(route('home'));
            $this->assertGuest();
        }
    }

    public function test_wrong_password_and_locked_account_cannot_login(): void
    {
        $this->post('/login', ['email' => $this->candidate->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->candidate->update(['is_active' => false]);
        $this->post('/login', ['email' => $this->candidate->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guests_and_other_roles_cannot_enter_private_sections(): void
    {
        $this->get('/candidate/dashboard')->assertRedirect(route('login'));
        $this->actingAs($this->candidate)->get('/admin/dashboard')->assertForbidden();
        $this->get('/employer/dashboard')->assertForbidden();
        $this->actingAs($this->employer)->get('/candidate/saved-jobs')->assertForbidden();
    }

    public function test_locking_an_existing_session_blocks_application_submission(): void
    {
        $this->candidate->update(['is_active' => false]);
        $this->actingAs($this->candidate)->post(route('jobs.apply', $this->job->id), [])->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_profile_and_skills_update_and_can_be_cleared(): void
    {
        $skills = Skill::take(2)->pluck('id')->all();
        $this->actingAs($this->candidate)->put('/candidate/profile', ['name' => 'Tên đã sửa', 'phone' => '0901234567', 'bio' => 'Hồ sơ mới', 'skills' => $skills])->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $this->candidate->id, 'name' => 'Tên đã sửa']);
        $this->assertCount(2, $this->candidate->fresh()->skills);
        $this->put('/candidate/profile', ['name' => 'Tên đã sửa'])->assertSessionHas('success');
        $this->assertCount(0, $this->candidate->fresh()->skills);
        $this->put('/candidate/profile', ['name' => '', 'skills' => [99999]])->assertSessionHasErrors(['name', 'skills.0']);
    }

    public function test_cv_upload_rename_primary_and_delete_preserve_files_and_default(): void
    {
        $this->actingAs($this->candidate)->post('/candidate/cvs', ['title' => 'CV mới', 'cv_file' => $this->pdfUpload()])->assertSessionHas('success');
        $cv = $this->candidate->cvs()->latest('id')->firstOrFail();
        Storage::disk('public')->assertExists($cv->file_path);
        $this->put('/candidate/cvs/'.$cv->id, ['title' => 'CV đã đổi tên'])->assertSessionHas('success');
        $this->post('/candidate/cvs/'.$cv->id.'/primary')->assertSessionHas('success');
        $this->assertSame($cv->id, $this->candidate->fresh()->primaryCv->id);
        $this->delete('/candidate/cvs/'.$cv->id)->assertSessionHas('success');
        Storage::disk('public')->assertMissing($cv->file_path);
        $this->assertNotNull($this->candidate->fresh()->primaryCv);
    }

    public function test_cv_rejects_invalid_type_and_oversized_files(): void
    {
        $this->actingAs($this->candidate)->post('/candidate/cvs', ['title' => 'Sai định dạng', 'cv_file' => UploadedFile::fake()->create('bad.exe', 5, 'application/octet-stream')])->assertSessionHasErrors('cv_file');
        $this->post('/candidate/cvs', ['title' => 'Quá lớn', 'cv_file' => UploadedFile::fake()->create('large.pdf', 6000, 'application/pdf')])->assertSessionHasErrors('cv_file');
    }

    public function test_first_cv_is_primary_and_consecutive_uploads_have_unique_files(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);
        $this->actingAs($user)->post('/candidate/cvs', ['title' => 'CV đầu tiên', 'cv_file' => $this->pdfUpload()])->assertSessionHas('success');
        $first = $user->cvs()->firstOrFail();
        $this->assertTrue($first->is_primary);
        $this->post('/candidate/cvs', ['title' => 'CV thứ hai', 'cv_file' => $this->pdfUpload()])->assertSessionHas('success');
        $second = $user->cvs()->latest('id')->firstOrFail();
        $this->assertNotSame($first->file_path, $second->file_path);
        Storage::disk('public')->assertExists([$first->file_path, $second->file_path]);
    }

    public function test_cv_ownership_is_checked_before_changing_primary(): void
    {
        $foreign = Cv::where('user_id', '!=', $this->candidate->id)->firstOrFail();
        $primary = $this->candidate->primaryCv->id;
        $this->actingAs($this->candidate)->post('/candidate/cvs/'.$foreign->id.'/primary')->assertNotFound();
        $this->put('/candidate/cvs/'.$foreign->id, ['title' => 'Không được sửa'])->assertNotFound();
        $this->delete('/candidate/cvs/'.$foreign->id)->assertNotFound();
        $this->assertSame($primary, $this->candidate->fresh()->primaryCv->id);
    }

    public function test_cv_attached_to_an_application_cannot_be_deleted(): void
    {
        $cv = $this->candidate->primaryCv;
        $this->actingAs($this->candidate)->delete('/candidate/cvs/'.$cv->id)->assertSessionHas('error');
        $this->assertDatabaseHas('cvs', ['id' => $cv->id]);
        Storage::disk('public')->assertExists($cv->file_path);
    }

    public function test_application_uses_owned_cv_and_blocks_duplicate_within_six_months(): void
    {
        $application = $this->applyToJob();
        $this->assertSame('submitted', $application->status);
        $this->post(route('jobs.apply', $this->job->id), ['cv_option' => 'existing', 'existing_cv_id' => $application->cv_id])->assertSessionHas('error');
        $this->assertSame(1, Application::where('job_post_id', $this->job->id)->where('user_id', $this->candidate->id)->count());
    }

    public function test_candidate_can_apply_again_after_six_months(): void
    {
        $application = $this->applyToJob();
        $application->update(['applied_at' => now()->subMonths(7)]);
        $this->post(route('jobs.apply', $this->job->id), ['cv_option' => 'existing', 'existing_cv_id' => $application->cv_id])->assertSessionHas('success');
        $this->assertSame(2, Application::where('job_post_id', $this->job->id)->where('user_id', $this->candidate->id)->count());
    }

    public function test_application_can_upload_new_cv_and_cannot_use_foreign_cv(): void
    {
        $foreign = Cv::where('user_id', '!=', $this->candidate->id)->firstOrFail();
        $this->actingAs($this->candidate)->post(route('jobs.apply', $this->job->id), ['cv_option' => 'existing', 'existing_cv_id' => $foreign->id])->assertSessionHasErrors('existing_cv_id');
        $this->post(route('jobs.apply', $this->job->id), ['cv_option' => 'new', 'cv_file' => $this->pdfUpload()])->assertSessionHas('success');
        $cv = $this->candidate->cvs()->latest('id')->firstOrFail();
        Storage::disk('public')->assertExists($cv->file_path);
        $this->assertDatabaseHas('applications', ['cv_id' => $cv->id, 'job_post_id' => $this->job->id]);
    }

    public function test_only_submitted_applications_can_be_withdrawn(): void
    {
        $application = $this->applyToJob();
        $this->post('/candidate/applications/'.$application->id.'/withdraw')->assertSessionHas('success');
        $this->assertSame('withdrawn', $application->fresh()->status);
        $reviewed = $this->candidate->applications()->where('status', 'viewed')->firstOrFail();
        $this->post('/candidate/applications/'.$reviewed->id.'/withdraw')->assertSessionHas('error');
        $foreign = Application::where('user_id', '!=', $this->candidate->id)->firstOrFail();
        $this->post('/candidate/applications/'.$foreign->id.'/withdraw')->assertNotFound();
    }

    public function test_unapproved_closed_and_expired_jobs_are_not_public_or_applicable(): void
    {
        foreach (['pending', 'closed', 'rejected'] as $status) {
            $this->job->update(['status' => $status]);
            $this->get(route('jobs.show', $this->job->id))->assertNotFound();
            $this->get('/jobs')->assertDontSee($this->job->title);
            $this->actingAs($this->candidate)->post(route('jobs.apply', $this->job->id), [])->assertNotFound();
            $this->actingAs($this->employer)->get(route('jobs.show', $this->job->id))->assertOk();
            $this->post('/logout');
        }
        $this->job->update(['status' => 'approved', 'deadline' => now()->subDay()]);
        $this->get(route('jobs.show', $this->job->id))->assertNotFound();
    }

    public function test_employer_creates_pending_job_and_admin_publishes_it(): void
    {
        $this->actingAs($this->employer)->post('/employer/jobs', $this->jobData())->assertRedirect(route('employer.jobs.index'))->assertSessionHas('success');
        $created = JobPost::where('title', 'Lập trình viên kiểm thử quy trình')->firstOrFail();
        $this->assertSame('pending', $created->status);
        $this->assertSame(12000000, $created->salary_min);
        $this->assertCount(2, $created->skills);
        $this->actingAs($this->candidate)->get(route('jobs.show', $created->id))->assertNotFound();
        $this->actingAs($this->admin)->post('/admin/jobs/'.$created->id.'/approve')->assertSessionHas('success');
        $this->actingAs($this->candidate)->get(route('jobs.show', $created->id))->assertOk();
        $this->actingAs($this->admin)->post('/admin/jobs/'.$created->id.'/reject')->assertSessionHas('success');
        $this->actingAs($this->candidate)->get(route('jobs.show', $created->id))->assertNotFound();
    }

    public function test_job_edits_require_admin_review_again(): void
    {
        $this->actingAs($this->employer)->put('/employer/jobs/'.$this->job->id, $this->jobData(['title' => 'Tin đã chỉnh sửa']))->assertSessionHas('success');
        $this->assertSame('pending', $this->job->fresh()->status);
        $this->assertSame('Tin đã chỉnh sửa', $this->job->fresh()->title);
    }

    public function test_invalid_job_input_is_rejected(): void
    {
        $this->actingAs($this->employer)->post('/employer/jobs', $this->jobData(['title' => '', 'deadline' => now()->subDay()->toDateString(), 'salary_max' => 5]))->assertSessionHasErrors(['title', 'deadline', 'salary_max']);
    }

    public function test_closing_reopening_and_renewing_cannot_bypass_moderation(): void
    {
        $this->actingAs($this->employer)->post('/employer/jobs/'.$this->job->id.'/toggle')->assertSessionHas('success');
        $this->assertSame('closed', $this->job->fresh()->status);
        $this->post('/employer/jobs/'.$this->job->id.'/toggle')->assertSessionHas('success');
        $this->assertSame('pending', $this->job->fresh()->status);
        $this->post('/employer/jobs/'.$this->job->id.'/toggle')->assertSessionHas('error');
        $oldDeadline = $this->job->fresh()->deadline;
        $this->post('/employer/jobs/'.$this->job->id.'/renew', ['extend_days' => 30])->assertSessionHas('success');
        $this->assertSame('pending', $this->job->fresh()->status);
        $this->assertTrue($this->job->fresh()->deadline->greaterThan($oldDeadline));
        $this->post('/employer/jobs/'.$this->job->id.'/renew', ['extend_days' => -30])->assertSessionHasErrors('extend_days');
    }

    public function test_employer_cannot_change_another_company_job_or_application(): void
    {
        $foreign = JobPost::where('company_id', '!=', $this->employer->company->id)->firstOrFail();
        $this->actingAs($this->employer)->put('/employer/jobs/'.$foreign->id, $this->jobData())->assertNotFound();
        $this->post('/employer/jobs/'.$foreign->id.'/toggle')->assertNotFound();
        $this->post('/employer/jobs/'.$foreign->id.'/renew', ['extend_days' => 30])->assertNotFound();
        $application = Application::where('job_post_id', $foreign->id)->firstOrFail();
        $this->post('/employer/applications/'.$application->id.'/status', ['status' => 'accepted'])->assertNotFound();
        $this->post('/employer/applications/'.$application->id.'/invite', [])->assertNotFound();
    }

    public function test_company_details_can_be_updated(): void
    {
        $this->actingAs($this->employer)->put('/employer/company', ['name' => 'FPT Updated', 'city' => 'Đà Nẵng', 'address' => '123 Demo', 'website' => 'https://example.com', 'size' => '100-500', 'description' => 'Mô tả mới'])->assertSessionHas('success');
        $this->assertDatabaseHas('companies', ['user_id' => $this->employer->id, 'name' => 'FPT Updated']);
    }

    public function test_application_review_reveals_contact_and_can_schedule_interview(): void
    {
        $application = $this->applyToJob();
        $this->actingAs($this->employer)->get('/employer/applications/'.$this->job->id)->assertOk()->assertDontSee($this->candidate->email);
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/status', ['status' => 'viewed'])->assertSessionHas('success');
        $this->get('/employer/applications/'.$this->job->id)->assertOk()->assertSee($this->candidate->email);
        $details = ['interview_date' => now()->addDays(3)->format('Y-m-d H:i:s'), 'interview_type' => 'online', 'location_or_link' => 'https://meet.example.com/demo', 'note' => 'Trao đổi về PHP'];
        $this->post('/employer/applications/'.$application->id.'/invite', $details)->assertSessionHas('success');
        $this->post('/employer/applications/'.$application->id.'/invite', array_replace($details, ['note' => 'Đã cập nhật']))->assertSessionHas('success');
        $this->assertSame(1, Interview::where('application_id', $application->id)->count());
        $this->assertSame('interview', $application->fresh()->status);
        $this->actingAs($this->candidate)->get('/candidate/applications')->assertSee('https://meet.example.com/demo');
    }

    public function test_interview_validation_and_withdrawn_application_protection(): void
    {
        $application = $this->applyToJob();
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/invite', ['interview_date' => now()->subDay()->toDateString(), 'interview_type' => 'invalid'])->assertSessionHasErrors(['interview_date', 'interview_type', 'location_or_link']);
        $application->update(['status' => 'withdrawn']);
        $this->post('/employer/applications/'.$application->id.'/status', ['status' => 'viewed'])->assertSessionHas('error');
        $this->post('/employer/applications/'.$application->id.'/invite', [])->assertSessionHas('error');
        $this->assertSame('withdrawn', $application->fresh()->status);
    }

    public function test_admin_manages_accounts_and_cannot_lock_self(): void
    {
        $this->actingAs($this->admin)->post('/admin/users/'.$this->candidate->id.'/toggle')->assertSessionHas('success');
        $this->assertFalse($this->candidate->fresh()->is_active);
        $this->post('/admin/users/'.$this->candidate->id.'/toggle')->assertSessionHas('success');
        $this->assertTrue($this->candidate->fresh()->is_active);
        $this->post('/admin/users/'.$this->admin->id.'/toggle')->assertSessionHas('error');
        $this->assertTrue($this->admin->fresh()->is_active);
        $this->get('/admin/users?role=candidate&q=ungvien')->assertOk()->assertSee($this->candidate->email)->assertDontSee('fpt@jobconnect.vn');
    }

    public function test_admin_creates_categories_and_rejects_duplicate_name(): void
    {
        $this->actingAs($this->admin)->post('/admin/categories', ['name' => 'Ngành kiểm thử mới'])->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Ngành kiểm thử mới', 'slug' => 'nganh-kiem-thu-moi']);
        $this->post('/admin/categories', ['name' => 'Ngành kiểm thử mới'])->assertSessionHasErrors('name');
    }

    public function test_saved_jobs_persist_are_idempotent_and_can_be_removed(): void
    {
        $url = '/candidate/saved-jobs/'.$this->job->id;
        $this->actingAs($this->candidate)->putJson($url)->assertOk()->assertJson(['saved' => true]);
        $this->putJson($url)->assertOk();
        $this->assertDatabaseCount('saved_jobs', 1);
        $this->get('/candidate/saved-jobs')->assertSee($this->job->title);
        $this->get('/jobs')->assertSee('data-saved="true"', false);
        $this->deleteJson($url)->assertOk()->assertJson(['saved' => false]);
        $this->assertDatabaseCount('saved_jobs', 0);
        $this->get('/candidate/saved-jobs')->assertSee('Lưu lại cơ hội');
    }

    public function test_saved_jobs_are_private_and_can_remove_expired_job(): void
    {
        $other = User::factory()->create(['role' => 'candidate']);
        $other->savedJobs()->attach($this->job->id);
        $this->actingAs($this->candidate)->deleteJson('/candidate/saved-jobs/'.$this->job->id)->assertOk();
        $this->assertDatabaseHas('saved_jobs', ['user_id' => $other->id]);
        $this->candidate->savedJobs()->attach($this->job->id);
        $this->job->update(['status' => 'closed']);
        $this->get('/candidate/saved-jobs')->assertOk()->assertSee('Tin đã ngừng tuyển');
        $this->deleteJson('/candidate/saved-jobs/'.$this->job->id)->assertOk();
        $this->putJson('/candidate/saved-jobs/'.$this->job->id)->assertNotFound();
    }

    public function test_demo_cv_files_are_real_pdfs_and_repair_does_not_overwrite_files(): void
    {
        foreach (Cv::all() as $cv) {
            Storage::disk('public')->assertExists($cv->file_path);
            $this->assertStringStartsWith('%PDF-', Storage::disk('public')->get($cv->file_path));
        }
        $path = $this->candidate->primaryCv->file_path;
        Storage::disk('public')->put($path, 'user replacement');
        $this->seed(DemoCvSeeder::class);
        $this->assertSame('user replacement', Storage::disk('public')->get($path));
        Storage::disk('public')->delete($path);
        $this->seed(DemoCvSeeder::class);
        $this->assertStringStartsWith('%PDF-', Storage::disk('public')->get($path));
    }

    public function test_complete_recruitment_workflow(): void
    {
        $this->actingAs($this->employer)->post('/employer/jobs', $this->jobData(['title' => 'Quy trình đầu cuối']))->assertSessionHas('success');
        $job = JobPost::where('title', 'Quy trình đầu cuối')->firstOrFail();
        $this->assertSame('pending', $job->status);
        $this->actingAs($this->admin)->post('/admin/jobs/'.$job->id.'/approve')->assertSessionHas('success');
        $this->actingAs($this->candidate)->putJson('/candidate/saved-jobs/'.$job->id)->assertOk();
        $this->post('/jobs/'.$job->id.'/apply', ['cv_option' => 'new', 'cv_file' => $this->pdfUpload(), 'cover_letter' => 'Ứng tuyển đầu cuối'])->assertSessionHas('success');
        $application = Application::where('job_post_id', $job->id)->firstOrFail();
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/invite', ['interview_date' => now()->addDays(2)->format('Y-m-d H:i:s'), 'interview_type' => 'offline', 'location_or_link' => 'Văn phòng kiểm thử'])->assertSessionHas('success');
        $this->actingAs($this->candidate)->get('/candidate/applications')->assertOk()->assertSee('Văn phòng kiểm thử');
        $this->actingAs($this->employer)->post('/employer/applications/'.$application->id.'/status', ['status' => 'accepted'])->assertSessionHas('success');
        $this->actingAs($this->candidate)->get('/candidate/applications')->assertSee('Đã trúng tuyển');
    }
}
