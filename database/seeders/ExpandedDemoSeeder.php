<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Location;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;

class ExpandedDemoSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/fixtures/expanded_demo.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['candidates'] as $index => $name) {
            if (! is_file($this->fixturePath($index))) {
                throw new RuntimeException('Thiếu tệp CV mẫu: '.$this->fixturePath($index));
            }
        }

        DB::transaction(function () use ($data): void {
            $catalog = $this->createCatalog($data);
            $password = Hash::make('password');
            $companies = $this->createCompanies($data, $catalog, $password);
            $jobs = $this->createJobs($data['profiles'], $catalog, $companies);
            $candidates = $this->createCandidates($data, $catalog, $password);
            $this->createActivity($data['profiles'], $candidates, $jobs);
        });

        $this->command?->info('Đã bổ sung dữ liệu minh họa. Các bản ghi và tệp đang có được giữ nguyên.');
        $this->command?->table(['Công ty', 'Tin tuyển dụng', 'Tin công khai', 'Ứng viên', 'Hồ sơ ứng tuyển', 'Lịch phỏng vấn'], [[
            Company::count(), JobPost::count(), JobPost::active()->count(),
            User::where('role', 'candidate')->count(), Application::count(), Interview::count(),
        ]]);
    }

    /** @return array{categories: array<string, Category>, locations: array<string, Location>, skills: array<string, Skill>} */
    private function createCatalog(array $data): array
    {
        $catalog = ['categories' => [], 'locations' => [], 'skills' => []];

        foreach ($data['locations'] as [$name, $slug]) {
            $catalog['locations'][$slug] = Location::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        foreach ($data['profiles'] as $profile) {
            $catalog['categories'][$profile['slug']] = Category::firstOrCreate(['slug' => $profile['slug']], [
                'name' => $profile['name'], 'icon' => $profile['icon'],
                'bg_class' => $profile['bg_class'], 'description' => $profile['summary'],
            ]);

            foreach ($profile['skills'] as $name) {
                $catalog['skills'][$name] = Skill::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
            }
        }

        return $catalog;
    }

    /** @return array<int, Company> */
    private function createCompanies(array $data, array $catalog, string $password): array
    {
        $companies = Company::whereHas('user', function ($query): void {
            $query->where('role', 'employer')->whereIn('email', [
                'fpt@jobconnect.vn', 'vng@jobconnect.vn', 'momo@jobconnect.vn',
                'viettel@jobconnect.vn', 'shopee@jobconnect.vn',
            ]);
        })->orderBy('id')->get()->all();

        foreach ($data['companies'] as $index => [$name, $slug, $locationSlug]) {
            $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $employer = User::firstOrCreate(['email' => 'hr'.$number.'@demo.jobconnect.test'], [
                'name' => 'Tuyển dụng '.$name, 'password' => $password, 'role' => 'employer',
                'phone' => '028000000'.$number, 'is_active' => true,
                'bio' => 'Tài khoản nhà tuyển dụng minh họa của đồ án JobConnect.',
            ]);
            if (! $employer->isEmployer()) {
                throw new LogicException('Email mẫu đã được sử dụng bởi vai trò khác: '.$employer->email);
            }

            $location = $catalog['locations'][$locationSlug];
            $companies[] = Company::firstOrCreate(['user_id' => $employer->id], [
                'name' => $name, 'slug' => 'demo-'.$slug, 'website' => 'https://'.$slug.'.example',
                'address' => 'Văn phòng minh họa tại '.$location->name, 'city' => $location->name,
                'size' => ['10-50 nhân viên', '50-100 nhân viên', '100-500 nhân viên', '500-1000 nhân viên'][$index % 4],
                'description' => $name.' xây dựng sản phẩm và dịch vụ số, chú trọng đào tạo, phối hợp đội nhóm và phát triển nhân sự. Đây là doanh nghiệp giả lập phục vụ trải nghiệm đồ án JobConnect.',
                'is_verified' => $index % 4 !== 0,
            ]);
        }

        return $companies;
    }

    /** @return array<int, JobPost> */
    private function createJobs(array $profiles, array $catalog, array $companies): array
    {
        $levels = ['intern', 'fresher', 'junior', 'middle', 'senior', 'lead', 'all'];
        $labels = ['Thực tập', 'Mới tốt nghiệp', 'Junior', 'Middle', 'Senior', 'Trưởng nhóm', 'Nhiều cấp độ'];
        $salaries = [[3, 6], [8, 13], [12, 18], [18, 28], [28, 40], [42, 60], [10, 20]];
        $types = ['full-time', 'hybrid', 'remote', 'part-time'];
        $jobs = [];

        for ($index = 0; $index < 120; $index++) {
            $profile = $profiles[$index % count($profiles)];
            $batch = intdiv($index, count($profiles));
            $levelIndex = $batch % count($levels);
            $company = $companies[$index % count($companies)];
            $role = $profile['roles'][intdiv($batch, 3) % count($profile['roles'])];
            $type = $levels[$levelIndex] === 'intern' ? 'internship' : $types[($batch + $index % 3) % count($types)];
            $location = $type === 'remote'
                ? $catalog['locations']['remote']
                : collect($catalog['locations'])->firstWhere('name', $company->city);
            $location ??= $catalog['locations']['hcm'];
            $status = match (true) {
                $index < 100 => 'approved',
                $index < 110 => 'pending',
                $index < 116 => 'closed',
                default => 'rejected',
            };
            $negotiable = $index % 11 === 0;
            $job = JobPost::firstOrCreate(['slug' => 'demo-extended-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)], [
                'company_id' => $company->id, 'category_id' => $catalog['categories'][$profile['slug']]->id,
                'location_id' => $location->id, 'title' => $role.' ('.$labels[$levelIndex].')',
                'description' => $profile['summary']."\n\nCông việc chính:\n- ".implode("\n- ", $profile['tasks'])."\n\nTin tuyển dụng minh họa phục vụ đồ án JobConnect, không phải thông báo tuyển dụng thực tế.",
                'requirements' => implode("\n", $profile['requirements'])."\nCấp độ: ".$labels[$levelIndex].'. Công ty hướng dẫn theo năng lực của vị trí.',
                'benefits' => "Được hướng dẫn và đào tạo theo lộ trình công việc.\nThưởng theo kết quả, hỗ trợ thiết bị làm việc và hoạt động đội nhóm.\nLịch làm việc thống nhất theo hình thức tuyển dụng và nhu cầu dự án.",
                'salary_min' => $negotiable ? null : $salaries[$levelIndex][0] * 1000000,
                'salary_max' => $negotiable ? null : $salaries[$levelIndex][1] * 1000000,
                'salary_negotiable' => $negotiable, 'type' => $type, 'experience_level' => $levels[$levelIndex],
                'status' => $status, 'is_featured' => $index % 5 === 0,
                'deadline' => $index >= 96 && $index < 100 ? now()->subDays(3)->toDateString() : now()->addDays(7 + $index % 50)->toDateString(),
                'views_count' => 40 + ($index * 43) % 1800,
            ]);

            if ($job->wasRecentlyCreated) {
                $postedAt = now()->subDays($index % 21)->subHours(1 + $index % 10);
                $job->forceFill(['created_at' => $postedAt, 'updated_at' => $postedAt])->save();
                $job->skills()->sync(array_map(fn (string $name): int => $catalog['skills'][$name]->id, $profile['skills']));
            }
            $jobs[] = $job;
        }

        return $jobs;
    }

    /** @return array<int, array{user: User, cv: Cv, profile: int, applications: int}> */
    private function createCandidates(array $data, array $catalog, string $password): array
    {
        $candidates = [];
        foreach ($data['candidates'] as $index => $name) {
            $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $profileIndex = $index % count($data['profiles']);
            $profile = $data['profiles'][$profileIndex];
            $candidate = User::firstOrCreate(['email' => 'ungvien'.$number.'@demo.jobconnect.test'], [
                'name' => $name, 'password' => $password, 'phone' => '09000000'.$number,
                'role' => 'candidate', 'is_active' => $index < 48,
                'bio' => $profile['summary'].' Mong muốn làm việc trong môi trường học hỏi và phối hợp đội nhóm. Hồ sơ minh họa của JobConnect.',
            ]);
            if (! $candidate->isCandidate()) {
                throw new LogicException('Email mẫu đã được sử dụng bởi vai trò khác: '.$candidate->email);
            }

            $path = 'cvs/demo-expanded/candidate-'.$number.'.pdf';
            if ($candidate->wasRecentlyCreated) {
                $candidate->forceFill(['created_at' => now()->subMonths(4)])->save();
                $candidate->skills()->sync(array_map(fn (string $skill): int => $catalog['skills'][$skill]->id, $profile['skills']));
                $cv = Cv::create([
                    'user_id' => $candidate->id, 'title' => 'CV '.$name.' - '.$profile['name'],
                    'file_path' => $path, 'file_type' => 'pdf', 'is_primary' => true,
                ]);
                $cv->forceFill(['created_at' => now()->subDays(60), 'updated_at' => now()->subDays(60)])->save();
            } else {
                $cv = $candidate->cvs()->where('file_path', $path)->first();
            }

            if ($cv === null) {
                continue;
            }
            if (Storage::disk('public')->missing($path)) {
                if (! Storage::disk('public')->put($path, file_get_contents($this->fixturePath($index)))) {
                    throw new RuntimeException('Không thể lưu CV mẫu: '.$path);
                }
            }
            $candidates[] = ['user' => $candidate, 'cv' => $cv, 'profile' => $profileIndex, 'applications' => 3];
        }

        foreach (['ungvien@jobconnect.vn' => 0, 'bichngoc@jobconnect.vn' => 1] as $email => $profileIndex) {
            $candidate = User::where('email', $email)->where('role', 'candidate')->first();
            if ($candidate?->primaryCv !== null) {
                $candidates[] = ['user' => $candidate, 'cv' => $candidate->primaryCv, 'profile' => $profileIndex, 'applications' => 6];
            }
        }

        return $candidates;
    }

    private function createActivity(array $profiles, array $candidates, array $jobs): void
    {
        $statuses = ['submitted', 'viewed', 'interview', 'accepted', 'rejected', 'withdrawn'];
        foreach ($candidates as $index => $entry) {
            $candidate = $entry['user'];
            $profileIndex = $entry['profile'];
            $matchingJobs = array_values(array_filter($jobs, fn (JobPost $job, int $key): bool => $key < 96 && $key % count($profiles) === $profileIndex, ARRAY_FILTER_USE_BOTH));

            for ($offset = 0; $offset < $entry['applications']; $offset++) {
                $job = $matchingJobs[($index + $offset) % count($matchingJobs)];
                if (! $candidate->wasRecentlyCreated && ! $job->wasRecentlyCreated) {
                    continue;
                }
                if ($job->status !== 'approved' || $job->is_expired) {
                    continue;
                }

                $status = $statuses[($index * 3 + $offset) % count($statuses)];
                $appliedAt = now()->subDays(2 + $index % 12)->setTime(8 + $offset, 15);
                if ($appliedAt->lt($job->created_at)) {
                    $appliedAt = $job->created_at->copy()->addMinutes(30 + $offset * 5);
                }
                $application = Application::firstOrCreate(['job_post_id' => $job->id, 'user_id' => $candidate->id], [
                    'cv_id' => $entry['cv']->id,
                    'cover_letter' => 'Tôi quan tâm đến vị trí '.$job->title.'. '.$profiles[$profileIndex]['summary'].' Mong được trao đổi thêm về công việc. Đây là hồ sơ minh họa để kiểm thử website.',
                    'status' => $status, 'applied_at' => $appliedAt,
                ]);

                if ($application->wasRecentlyCreated) {
                    $application->forceFill(['created_at' => $application->applied_at, 'updated_at' => $application->applied_at])->save();
                }
                if ($application->wasRecentlyCreated && $status === 'interview') {
                    $online = $index % 2 === 0;
                    Interview::create([
                        'application_id' => $application->id,
                        'interview_date' => now()->addDays(2 + $index % 10)->setTime(9 + $offset, 0),
                        'interview_type' => $online ? 'online' : 'offline',
                        'location_or_link' => $online ? 'https://meeting.example/phong-phong-van-'.$application->id : $job->company->address,
                        'note' => 'Lịch minh họa: chuẩn bị CV và giới thiệu kinh nghiệm, dự án đã làm.', 'status' => 'scheduled',
                    ]);
                }
            }

            for ($offset = 0; $offset < 2; $offset++) {
                $job = $matchingJobs[($index + 7 + $offset) % count($matchingJobs)];
                if (($candidate->wasRecentlyCreated || $job->wasRecentlyCreated) && $job->status === 'approved' && ! $job->is_expired) {
                    $candidate->savedJobs()->syncWithoutDetaching([$job->id]);
                }
            }
        }
    }

    private function fixturePath(int $index): string
    {
        return database_path('seeders/fixtures/expanded_cv_'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'.pdf');
    }
}
