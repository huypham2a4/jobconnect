<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Các trường hợp được bổ sung sau đợt rà soát ngày 18/11/2026.
 */
class ReviewFixesTest extends TestCase
{
    use RefreshDatabase;

    private User $candidate;

    private User $employer;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $this->candidate = User::where('email', 'ungvien@jobconnect.vn')->firstOrFail();
        $this->employer = User::where('email', 'fpt@jobconnect.vn')->firstOrFail();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function jobData(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Tin rà soát',
            'category_id' => Category::firstOrFail()->id,
            'location_id' => JobPost::firstOrFail()->location_id,
            'type' => 'full-time',
            'experience_level' => 'junior',
            'salary_min' => 10,
            'salary_max' => 20,
            'description' => 'Mô tả công việc đủ dài để vượt qua kiểm tra tối thiểu ba mươi ký tự.',
            'deadline' => now()->addDays(10)->toDateString(),
        ], $overrides);
    }

    private function createApprovedJob(string $title): JobPost
    {
        $this->actingAs($this->employer)->post(route('employer.jobs.store'), $this->jobData(['title' => $title]))->assertSessionHas('success');
        $job = JobPost::where('title', $title)->latest('id')->firstOrFail();
        $this->actingAs($this->admin)->post(route('admin.jobs.approve', $job->id))->assertSessionHas('success');

        return $job->fresh();
    }

    private function applyTo(JobPost $job): Application
    {
        $this->actingAs($this->candidate)->post(route('jobs.apply', $job->id), [
            'cv_option' => 'existing',
            'existing_cv_id' => $this->candidate->primaryCv->id,
        ])->assertSessionHas('success');

        return Application::where('job_post_id', $job->id)->where('user_id', $this->candidate->id)->firstOrFail();
    }

    public function test_job_slugs_stay_unique_after_deleting_and_recreating_same_title(): void
    {
        foreach (['Kỹ sư PHP', 'Kỹ sư PHP'] as $title) {
            $this->actingAs($this->employer)->post(route('employer.jobs.store'), $this->jobData(['title' => $title]))->assertSessionHas('success');
        }
        $first = JobPost::where('title', 'Kỹ sư PHP')->oldest('id')->firstOrFail();
        $this->delete(route('employer.jobs.destroy', $first->id))->assertSessionHas('success');

        // Cách cũ đếm "ky-su-php%" = 1 rồi tạo lại "ky-su-php-2" -> trùng unique, lỗi 500.
        $this->post(route('employer.jobs.store'), $this->jobData(['title' => 'Kỹ sư PHP']))->assertSessionHas('success');
        $slugs = JobPost::where('title', 'Kỹ sư PHP')->pluck('slug');
        $this->assertCount(2, $slugs);
        $this->assertSame($slugs->count(), $slugs->unique()->count());
    }

    public function test_category_and_company_slugs_do_not_collide(): void
    {
        $this->actingAs($this->admin)->post(route('admin.categories.store'), ['name' => 'Kế toán rà soát'])->assertSessionHas('success');
        $this->post(route('admin.categories.store'), ['name' => 'Ke toan ra soat'])->assertSessionHas('success');
        $this->assertSame(2, Category::where('slug', 'like', 'ke-toan-ra-soat%')->count());

        auth()->logout();
        foreach (['a', 'b'] as $suffix) {
            $this->post(route('register.post'), [
                'name' => 'HR '.$suffix, 'email' => "hr-{$suffix}@example.test", 'password' => 'secret123', 'password_confirmation' => 'secret123',
                'role' => 'employer', 'company_name' => 'Công ty Trùng Tên', 'city' => 'Hà Nội',
            ])->assertRedirect();
            auth()->logout();
        }
        $this->assertSame(2, Company::where('name', 'Công ty Trùng Tên')->distinct()->count('slug'));
    }

    public function test_salary_can_be_max_only_and_edit_cannot_set_past_deadline(): void
    {
        $this->actingAs($this->employer)->post(route('employer.jobs.store'), $this->jobData(['title' => 'Chỉ có lương tối đa', 'salary_min' => null, 'salary_max' => 30]))
            ->assertSessionHasNoErrors()->assertSessionHas('success');
        $job = JobPost::where('title', 'Chỉ có lương tối đa')->firstOrFail();
        $this->assertSame('Lên đến 30 Triệu', $job->salary_text);

        $this->post(route('employer.jobs.store'), $this->jobData(['title' => 'Lương ngược', 'salary_min' => 30, 'salary_max' => 10]))->assertSessionHasErrors('salary_max');
        $this->put(route('employer.jobs.update', $job->id), $this->jobData(['deadline' => now()->subDay()->toDateString()]))->assertSessionHasErrors('deadline');
    }

    public function test_validation_messages_are_vietnamese(): void
    {
        $this->actingAs($this->employer)->put(route('employer.company.update'), ['name' => 'FPT', 'city' => 'Hà Nội', 'website' => 'khong-phai-url'])
            ->assertSessionHasErrors(['website' => 'Địa chỉ website không hợp lệ.']);
        $this->post(route('employer.jobs.store'), $this->jobData(['type' => 'sai']))
            ->assertSessionHasErrors(['type' => 'Hình thức làm việc đã chọn không hợp lệ.']);
    }

    public function test_admin_cannot_reopen_closed_job_or_reject_closed_job(): void
    {
        $job = $this->createApprovedJob('Tin sẽ đóng');
        $this->actingAs($this->employer)->post(route('employer.jobs.toggle', $job->id))->assertSessionHas('success');
        $this->assertSame('closed', $job->fresh()->status);

        $this->actingAs($this->admin)->post(route('admin.jobs.approve', $job->id))->assertSessionHas('error');
        $this->post(route('admin.jobs.reject', $job->id))->assertSessionHas('error');
        $this->assertSame('closed', $job->fresh()->status);
    }

    public function test_expired_closed_job_must_be_renewed_before_reopening(): void
    {
        $job = $this->createApprovedJob('Tin hết hạn');
        $job->update(['status' => 'closed', 'deadline' => now()->subDays(2)]);

        $this->actingAs($this->employer)->post(route('employer.jobs.toggle', $job->id))->assertSessionHas('error');
        $this->assertSame('closed', $job->fresh()->status);

        $this->post(route('employer.jobs.renew', $job->id))->assertSessionHas('success');
        $this->post(route('employer.jobs.toggle', $job->id))->assertSessionHas('success');
        $this->assertSame('pending', $job->fresh()->status);
    }

    public function test_application_status_transitions_follow_business_rules(): void
    {
        $application = $this->applyTo($this->createApprovedJob('Tin trạng thái'));

        $this->actingAs($this->employer)->post(route('employer.applications.status', $application->id), ['status' => 'interview'])->assertSessionHas('error');
        $this->post(route('employer.applications.status', $application->id), ['status' => 'viewed'])->assertSessionHas('success');
        $this->post(route('employer.applications.status', $application->id), ['status' => 'submitted'])->assertSessionHas('error');
        $this->assertSame('viewed', $application->fresh()->status);

        $this->post(route('employer.applications.status', $application->id), ['status' => 'rejected'])->assertSessionHas('success');
        $this->post(route('employer.applications.invite', $application->id), [
            'interview_date' => now()->addDays(2)->format('Y-m-d H:i'),
            'interview_type' => 'offline',
            'location_or_link' => 'Văn phòng',
        ])->assertSessionHas('error');
        $this->assertSame('rejected', $application->fresh()->status);
        $this->assertSame(0, Interview::where('application_id', $application->id)->count());

        // Cập nhật trùng trạng thái không tạo thêm thông báo cho ứng viên.
        $before = $this->candidate->notifications()->count();
        $this->post(route('employer.applications.status', $application->id), ['status' => 'rejected'])->assertSessionHas('success');
        $this->assertSame($before, $this->candidate->notifications()->count());
    }

    public function test_employer_view_masks_real_phone_and_cannot_open_withdrawn_cv(): void
    {
        $this->candidate->update(['phone' => '0912345678']);
        $job = $this->createApprovedJob('Tin rút hồ sơ');
        $this->actingAs($this->candidate)->post(route('jobs.apply', $job->id), [
            'cv_option' => 'new',
            'cv_file' => UploadedFile::fake()->create('cv-rieng.pdf', 20, 'application/pdf'),
        ])->assertSessionHas('success');
        $application = Application::where('job_post_id', $job->id)->where('user_id', $this->candidate->id)->firstOrFail();
        $this->actingAs($this->employer)->get(route('cvs.download', $application->cv_id))->assertOk();

        $this->actingAs($this->employer)->get(route('employer.applications', ['job_id' => $job->id]))
            ->assertOk()->assertSee('091*******')->assertDontSee('0987***')->assertDontSee('0912345678');

        $this->actingAs($this->candidate)->post(route('candidate.applications.withdraw', $application->id))->assertSessionHas('success');
        $this->actingAs($this->employer)->get(route('cvs.download', $application->cv_id))->assertForbidden();
        $this->actingAs($this->employer)->get(route('employer.applications', ['job_id' => $job->id]))->assertSee('Ứng viên đã rút hồ sơ');
    }

    public function test_guest_returns_to_job_page_after_login(): void
    {
        $job = JobPost::active()->firstOrFail();

        $this->get(route('jobs.show', $job->id))->assertOk()->assertSee('redirect=%2Fjobs%2F'.$job->id, false);
        $this->get(route('login', ['redirect' => '/jobs/'.$job->id]))->assertOk();
        $this->post(route('login.post'), ['email' => $this->candidate->email, 'password' => 'password'])
            ->assertRedirect('/jobs/'.$job->id);

        auth()->logout();
        // Không cho chuyển hướng ra trang ngoài.
        $this->get(route('login', ['redirect' => '//evil.example/jobs']))->assertOk();
        $this->post(route('login.post'), ['email' => $this->candidate->email, 'password' => 'password'])
            ->assertRedirect(route('candidate.dashboard'));
    }

    public function test_forgot_password_does_not_reveal_whether_email_exists(): void
    {
        $unknown = $this->post(route('password.email'), ['email' => 'khong-ton-tai@example.test'])->assertSessionHasNoErrors();
        $known = $this->post(route('password.email'), ['email' => $this->candidate->email])->assertSessionHasNoErrors();

        $this->assertSame($unknown->getSession()->get('success'), $known->getSession()->get('success'));
        $this->assertStringNotContainsString($this->candidate->email, $known->getSession()->get('success'));
    }

    public function test_owner_preview_does_not_increase_view_count(): void
    {
        $job = $this->createApprovedJob('Tin đếm lượt xem');
        $this->actingAs($this->employer)->get(route('jobs.show', $job->id))->assertOk();
        $this->assertSame(0, $job->fresh()->views_count);

        $this->actingAs($this->candidate)->get(route('jobs.show', $job->id))->assertOk();
        $this->assertSame(1, $job->fresh()->views_count);
    }

    public function test_unsafe_online_interview_link_is_not_rendered_as_link(): void
    {
        $application = $this->applyTo($this->createApprovedJob('Tin link phỏng vấn'));
        $this->actingAs($this->employer)->post(route('employer.applications.invite', $application->id), [
            'interview_date' => now()->addDays(2)->format('Y-m-d H:i'),
            'interview_type' => 'online',
            'location_or_link' => 'javascript:alert(1)',
        ])->assertSessionHas('success');

        $this->actingAs($this->candidate)->get(route('candidate.applications'))
            ->assertOk()->assertDontSee('href="javascript:alert(1)"', false)->assertSee('javascript:alert(1)');
    }

    public function test_register_offers_34_provinces_and_rejects_unknown_city(): void
    {
        $this->assertCount(34, config('provinces'));
        $this->get(route('register'))->assertOk()->assertSee('Lâm Đồng')->assertSee('Tuyên Quang')->assertDontSee('#f8fafc', false);

        $payload = [
            'name' => 'HR Tỉnh', 'email' => 'hr-tinh@example.test', 'password' => 'secret123', 'password_confirmation' => 'secret123',
            'role' => 'employer', 'company_name' => 'Công ty Tỉnh', 'city' => 'Sao Hỏa',
        ];
        $this->post(route('register.post'), $payload)->assertSessionHasErrors('city');
        $this->post(route('register.post'), ['city' => 'Lâm Đồng'] + $payload)->assertRedirect();
        $this->assertSame('Lâm Đồng', Company::where('name', 'Công ty Tỉnh')->value('city'));
    }

    public function test_company_profile_keeps_legacy_city_but_rejects_unknown_city(): void
    {
        $company = $this->employer->company;
        $company->update(['city' => 'Khác']);
        $data = ['name' => $company->name, 'website' => 'https://example.com'];

        $this->actingAs($this->employer)->get(route('employer.company'))->assertOk()->assertSee('Khánh Hòa');
        $this->put(route('employer.company.update'), ['city' => 'Khác'] + $data)->assertSessionHasNoErrors();
        $this->put(route('employer.company.update'), ['city' => 'Sao Hỏa'] + $data)->assertSessionHasErrors('city');
        $this->put(route('employer.company.update'), ['city' => 'Quảng Ninh'] + $data)->assertSessionHasNoErrors();
        $this->assertSame('Quảng Ninh', $company->fresh()->city);
    }

    public function test_category_icon_must_come_from_font_awesome_class(): void
    {
        $this->actingAs($this->admin)->get(route('admin.categories.index'))->assertOk()->assertSee('icon-picker', false)->assertSee('fa-solid fa-truck-fast', false);

        $this->post(route('admin.categories.store'), ['name' => 'Logistics rà soát', 'icon' => 'fa-solid fa-truck-fast'])->assertSessionHas('success');
        $this->assertSame('fa-solid fa-truck-fast', Category::where('name', 'Logistics rà soát')->value('icon'));

        $this->post(route('admin.categories.store'), ['name' => 'Icon lạ', 'icon' => 'x" onmouseover="alert(1)'])
            ->assertSessionHasErrors(['icon' => 'Biểu tượng không hợp lệ.']);
    }

    public function test_employer_job_list_shows_vietnamese_job_type(): void
    {
        $this->actingAs($this->employer)->get(route('employer.jobs.index'))->assertOk()->assertSee('Hình thức: Toàn thời gian')->assertDontSee('Hình thức: Full-time');
    }

    public function test_locations_cover_34_provinces_in_every_city_select(): void
    {
        $this->assertSame(35, Location::count());
        $this->assertSame(['TP. Hồ Chí Minh', 'Hà Nội'], Location::orderedForSelect()->take(2)->pluck('name')->all());
        $this->assertSame(Location::REMOTE_NAME, Location::orderedForSelect()->last()->name);

        $this->actingAs($this->employer)->get(route('employer.jobs.create'))->assertOk()->assertSee('Khánh Hòa')->assertSee('Điện Biên');
        $this->get(route('jobs.index'))->assertOk()->assertSee('Lâm Đồng');
        $this->get(route('companies.index'))->assertOk()->assertSee('Tuyên Quang');
        $this->get(route('jobs.index', ['location' => 'hcm']))->assertOk();
    }

    public function test_sync_moves_jobs_and_companies_from_merged_places(): void
    {
        $oldPlace = Location::create(['name' => 'Bình Dương', 'slug' => 'binh-duong']);
        $job = JobPost::firstOrFail();
        $job->update(['location_id' => $oldPlace->id]);
        $this->employer->company->update(['city' => 'Nha Trang']);

        Location::syncProvinces();
        Location::syncProvinces();

        $this->assertDatabaseMissing('locations', ['name' => 'Bình Dương']);
        $this->assertSame('TP. Hồ Chí Minh', $job->fresh()->location->name);
        $this->assertSame('Khánh Hòa', $this->employer->company->fresh()->city);
        $this->assertSame(35, Location::count());
    }
}
