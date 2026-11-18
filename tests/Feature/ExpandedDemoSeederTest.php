<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ExpandedDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpandedDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    public function test_expanded_data_has_varied_jobs_and_valid_linked_candidate_documents(): void
    {
        $this->seed(ExpandedDemoSeeder::class);

        $this->assertDatabaseCount('users', 79);
        $this->assertDatabaseCount('companies', 25);
        $this->assertDatabaseCount('job_posts', 127);
        $this->assertDatabaseCount('cvs', 52);
        $this->assertDatabaseCount('applications', 165);
        $this->assertDatabaseCount('interviews', 28);
        $this->assertDatabaseCount('saved_jobs', 104);
        $this->assertSame(103, JobPost::active()->count());
        $this->assertSame(5, JobPost::distinct()->count('type'));
        $this->assertSame(7, JobPost::distinct()->count('experience_level'));
        $this->assertSame(6, Application::distinct()->count('status'));
        foreach (Category::all() as $category) {
            $this->assertGreaterThan(0, $category->jobPosts()->active()->count(), $category->name);
        }

        $this->assertSame(0, DB::table('applications')->join('cvs', 'cvs.id', '=', 'applications.cv_id')->whereColumn('applications.user_id', '!=', 'cvs.user_id')->count());
        foreach (Cv::where('file_path', 'like', 'cvs/demo-expanded/%')->get() as $cv) {
            Storage::disk('public')->assertExists($cv->file_path);
            $this->assertStringStartsWith('%PDF-', Storage::disk('public')->get($cv->file_path));
        }
        $this->assertTrue(Hash::check('password', User::where('email', 'ungvien01@demo.jobconnect.test')->firstOrFail()->password));
        $this->assertSame(0, Application::join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')->where('job_posts.slug', 'like', 'demo-extended-%')->whereColumn('applications.applied_at', '<', 'job_posts.created_at')->count());
        $this->assertTrue(Interview::whereHas('application.user', fn ($query) => $query->where('email', 'like', '%@demo.jobconnect.test'))->get()->every(fn (Interview $interview): bool => $interview->interview_date->isFuture()));
    }

    public function test_repeat_seeding_preserves_existing_data_manual_edits_and_uploads(): void
    {
        $before = $this->tableSnapshot();
        $this->seed(ExpandedDemoSeeder::class);

        $job = JobPost::where('slug', 'demo-extended-001')->firstOrFail();
        $job->update(['title' => 'Nội dung đã sửa thủ công', 'status' => 'closed', 'deadline' => now()->subDay()->toDateString()]);
        $job->skills()->detach();
        $candidate = User::where('email', 'ungvien01@demo.jobconnect.test')->firstOrFail();
        $candidate->update(['bio' => 'Giới thiệu tự sửa', 'password' => Hash::make('changed-password'), 'is_active' => false]);
        $candidate->skills()->detach();
        $cv = $candidate->primaryCv;
        $cv->update(['title' => 'CV đã đổi tên']);
        $customFile = Storage::disk('public')->get($cv->file_path)."\n% USER UPLOAD";
        Storage::disk('public')->put($cv->file_path, $customFile);
        $missingCv = Cv::where('file_path', 'cvs/demo-expanded/candidate-02.pdf')->firstOrFail();
        Storage::disk('public')->delete($missingCv->file_path);
        $application = Application::where('user_id', $candidate->id)->firstOrFail();
        $application->update(['status' => 'accepted']);
        $interview = Interview::whereHas('application.user', fn ($query) => $query->where('email', 'like', '%@demo.jobconnect.test'))->firstOrFail();
        $interview->update(['note' => 'Lịch đã được cập nhật thủ công']);
        $candidate->savedJobs()->detach();
        $expectedCounts = array_map('count', $this->tableSnapshot());

        $this->seed(ExpandedDemoSeeder::class);
        $after = $this->tableSnapshot();
        $this->assertSame($expectedCounts, array_map('count', $after));
        $this->assertSame('Nội dung đã sửa thủ công', $job->fresh()->title);
        $this->assertSame('closed', $job->fresh()->status);
        $this->assertSame(0, $job->skills()->count());
        $this->assertSame(0, $candidate->skills()->count());
        $this->assertSame(0, $candidate->savedJobs()->count());
        $this->assertSame('Giới thiệu tự sửa', $candidate->fresh()->bio);
        $this->assertFalse($candidate->fresh()->is_active);
        $this->assertTrue(Hash::check('changed-password', $candidate->fresh()->password));
        $this->assertSame('CV đã đổi tên', $cv->fresh()->title);
        $this->assertSame($customFile, Storage::disk('public')->get($cv->file_path));
        Storage::disk('public')->assertExists($missingCv->file_path);
        $this->assertSame('accepted', $application->fresh()->status);
        $this->assertSame('Lịch đã được cập nhật thủ công', $interview->fresh()->note);
        foreach ($before as $table => $hashes) {
            $this->assertSame([], array_values(array_diff($hashes, $after[$table])), 'Original rows changed in '.$table);
        }
    }

    public function test_expanded_data_renders_search_pagination_and_role_dashboards(): void
    {
        $this->seed(ExpandedDemoSeeder::class);
        $this->get('/')->assertOk()->assertSee('103');
        $this->get('/jobs?page=2')->assertOk()->assertViewHas('jobs', fn ($jobs): bool => $jobs->currentPage() === 2 && $jobs->count() === 9);
        foreach (Category::all() as $category) {
            $this->get('/jobs?category='.$category->slug)->assertOk()->assertViewHas('jobs', fn ($jobs): bool => $jobs->total() > 0);
        }
        $hiddenJob = JobPost::where('slug', 'demo-extended-101')->firstOrFail();
        $this->get('/jobs/'.$hiddenJob->id)->assertNotFound();
        $expiredJob = JobPost::where('slug', 'demo-extended-097')->firstOrFail();
        $this->get('/jobs/'.$expiredJob->id)->assertNotFound();

        foreach ([
            ['ungvien@jobconnect.vn', '/candidate/dashboard'],
            ['ungvien@jobconnect.vn', '/candidate/applications'],
            ['fpt@jobconnect.vn', '/employer/dashboard'],
            ['fpt@jobconnect.vn', '/employer/applications'],
            ['admin@jobconnect.vn', '/admin/dashboard'],
            ['admin@jobconnect.vn', '/admin/jobs'],
        ] as [$email, $url]) {
            $this->actingAs(User::where('email', $email)->firstOrFail())->get($url)->assertOk();
        }
    }

    /** @return array<string, array<int, string>> */
    private function tableSnapshot(): array
    {
        $result = [];
        foreach (['users', 'companies', 'categories', 'locations', 'skills', 'job_posts', 'cvs', 'applications', 'interviews', 'job_post_skill', 'user_skill', 'saved_jobs'] as $table) {
            $result[$table] = DB::table($table)->get()->map(fn ($row): string => hash('sha256', json_encode((array) $row)))->all();
        }

        return $result;
    }
}
