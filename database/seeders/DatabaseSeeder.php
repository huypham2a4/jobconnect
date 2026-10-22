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
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Quản trị viên (Admin)
        $admin = User::create([
            'name' => 'Quản Trị Viên Hệ Thống',
            'email' => 'admin@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '0901234567',
            'role' => 'admin',
            'is_active' => true,
            'bio' => 'Quản trị viên trưởng nền tảng JobConnect.',
        ]);

        // 2. Tài khoản Nhà tuyển dụng (Employers)
        $empFpt = User::create([
            'name' => 'HR Manager FPT Software',
            'email' => 'fpt@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '02873007575',
            'role' => 'employer',
            'is_active' => true,
            'bio' => 'Đại diện tuyển dụng chính thức của FPT Software.',
        ]);

        $empVng = User::create([
            'name' => 'Talent Acquisition VNG',
            'email' => 'vng@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '02838664666',
            'role' => 'employer',
            'is_active' => true,
            'bio' => 'Bộ phận phát triển nhân tài VNG Corporation.',
        ]);

        $empMomo = User::create([
            'name' => 'MoMo People & Culture',
            'email' => 'momo@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '02839977888',
            'role' => 'employer',
            'is_active' => true,
            'bio' => 'Bộ phận tuyển dụng ví điện tử MoMo.',
        ]);

        $empViettel = User::create([
            'name' => 'Viettel Telecom Recruitment',
            'email' => 'viettel@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '02462556789',
            'role' => 'employer',
            'is_active' => true,
            'bio' => 'Tuyển dụng khối Công nghệ số Tập đoàn Viettel.',
        ]);

        $empShopee = User::create([
            'name' => 'Shopee Vietnam HR Team',
            'email' => 'shopee@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '02873081221',
            'role' => 'employer',
            'is_active' => true,
            'bio' => 'Tuyển dụng Shopee & Sea Group Vietnam.',
        ]);

        // 3. Tài khoản Ứng viên (Candidates)
        $cand1 = User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'ungvien@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '0987654321',
            'role' => 'candidate',
            'is_active' => true,
            'bio' => 'Kỹ sư phần mềm 3 năm kinh nghiệm với PHP/Laravel, REST API và Vue.js.',
        ]);

        $cand2 = User::create([
            'name' => 'Trần Thị Bích Ngọc',
            'email' => 'bichngoc@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '0912345678',
            'role' => 'candidate',
            'is_active' => true,
            'bio' => 'Chuyên viên Digital Marketing, tối ưu SEO, chạy chiến dịch Performance Ads.',
        ]);

        $cand3 = User::create([
            'name' => 'Lê Hoàng Nam',
            'email' => 'hoangnam@jobconnect.vn',
            'password' => Hash::make('password'),
            'phone' => '0933221100',
            'role' => 'candidate',
            'is_active' => true,
            'bio' => 'Product UI/UX Designer, thành thạo Figma, Design System và User Research.',
        ]);

        // 4. Tạo thông tin Công ty (Companies)
        $compFpt = Company::create([
            'user_id' => $empFpt->id,
            'name' => 'FPT Software & Cloud Solutions',
            'slug' => 'fpt-software',
            'logo' => null,
            'website' => 'https://fpt-software.com',
            'address' => 'Khu Công Nghệ Cao, TP. Thủ Đức',
            'city' => 'TP. Hồ Chí Minh',
            'size' => '1000+ nhân viên',
            'description' => 'Tập đoàn công nghệ hàng đầu Đông Nam Á, tiên phong chuyển đổi số và phát triển phần mềm toàn cầu.',
            'is_verified' => true,
        ]);

        $compVng = Company::create([
            'user_id' => $empVng->id,
            'name' => 'VNG Corporation',
            'slug' => 'vng-corporation',
            'logo' => null,
            'website' => 'https://vng.com.vn',
            'address' => 'VNG Campus, Quận 7',
            'city' => 'TP. Hồ Chí Minh',
            'size' => '1000+ nhân viên',
            'description' => 'Kỳ lân công nghệ đầu tiên tại Việt Nam, sở hữu hệ sinh thái Zalo, Zing MP3, game online và cloud.',
            'is_verified' => true,
        ]);

        $compMomo = Company::create([
            'user_id' => $empMomo->id,
            'name' => 'MoMo Fintech Platform',
            'slug' => 'momo-fintech',
            'logo' => null,
            'website' => 'https://momo.vn',
            'address' => 'Tòa nhà Phú Mỹ Hưng, Quận 7',
            'city' => 'TP. Hồ Chí Minh',
            'size' => '500-1000 nhân viên',
            'description' => 'Nền tảng thanh toán và siêu ứng dụng tài chính số phổ biến số 1 tại Việt Nam.',
            'is_verified' => true,
        ]);

        $compViettel = Company::create([
            'user_id' => $empViettel->id,
            'name' => 'Tập Đoàn Viettel Digital Hub',
            'slug' => 'viettel-digital-hub',
            'logo' => null,
            'website' => 'https://viettel.vn',
            'address' => 'Số 1 Giang Văn Minh, Ba Đình',
            'city' => 'Hà Nội',
            'size' => '1000+ nhân viên',
            'description' => 'Tập đoàn Công nghiệp - Viễn thông Quân đội, dẫn đầu giải pháp số và hạ tầng đám mây quốc gia.',
            'is_verified' => true,
        ]);

        $compShopee = Company::create([
            'user_id' => $empShopee->id,
            'name' => 'Shopee Vietnam',
            'slug' => 'shopee-vietnam',
            'logo' => null,
            'website' => 'https://shopee.vn',
            'address' => 'Saigon Centre 2, Quận 1',
            'city' => 'TP. Hồ Chí Minh',
            'size' => '1000+ nhân viên',
            'description' => 'Sàn thương mại điện tử hàng đầu khu vực, kiến tạo trải nghiệm mua sắm trực tuyến toàn diện.',
            'is_verified' => true,
        ]);

        // 5. Tạo Danh mục (Categories)
        $categoriesData = [
            ['name' => 'Công Nghệ Thông Tin', 'slug' => 'it-software', 'icon' => 'fa-solid fa-code', 'bg_class' => 'icon-blue', 'description' => 'Phát triển phần mềm, ứng dụng web, mobile, DevOps, AI và dữ liệu.'],
            ['name' => 'Marketing & Truyền Thông', 'slug' => 'marketing', 'icon' => 'fa-solid fa-bullhorn', 'bg_class' => 'icon-cyan', 'description' => 'Digital Marketing, SEO, Content Creator, PR & Sự kiện.'],
            ['name' => 'Kinh Doanh & Bán Hàng', 'slug' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'bg_class' => 'icon-emerald', 'description' => 'B2B/B2C Sales, Phát triển thị trường, Account Executive.'],
            ['name' => 'Tài Chính & Kế Toán', 'slug' => 'finance', 'icon' => 'fa-solid fa-wallet', 'bg_class' => 'icon-amber', 'description' => 'Kế toán tổng hợp, kiểm toán, phân tích tài chính đầu tư.'],
            ['name' => 'Thiết Kế UI/UX & Đồ Họa', 'slug' => 'design', 'icon' => 'fa-solid fa-palette', 'bg_class' => 'icon-purple', 'description' => 'Thiết kế giao diện sản phẩm số, đồ họa thương hiệu, 3D.'],
            ['name' => 'Nhân Sự & Tuyển Dụng', 'slug' => 'hr', 'icon' => 'fa-solid fa-users-gear', 'bg_class' => 'icon-rose', 'description' => 'Tuyển dụng nhân tài, C&B, đào tạo và phát triển văn hóa.'],
            ['name' => 'Dịch Vụ Khách Hàng', 'slug' => 'customer-service', 'icon' => 'fa-solid fa-headset', 'bg_class' => 'icon-teal', 'description' => 'Chăm sóc khách hàng, tư vấn viên, vận hành hỗ trợ 24/7.'],
            ['name' => 'Quản Lý Dự Án & Agile', 'slug' => 'project-management', 'icon' => 'fa-solid fa-list-check', 'bg_class' => 'icon-indigo', 'description' => 'Project Manager, Scrum Master, Product Owner công nghệ.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::create($cat);
        }

        // 6. Tạo Địa điểm (Locations)
        $locationsData = [
            ['name' => 'TP. Hồ Chí Minh', 'slug' => 'hcm'],
            ['name' => 'Hà Nội', 'slug' => 'hn'],
            ['name' => 'Đà Nẵng', 'slug' => 'dn'],
            ['name' => 'Cần Thơ', 'slug' => 'can-tho'],
            ['name' => 'Làm việc từ xa (Remote)', 'slug' => 'remote'],
        ];

        $locations = [];
        foreach ($locationsData as $loc) {
            $locations[$loc['slug']] = Location::create($loc);
        }

        // 7. Tạo Kỹ năng (Skills)
        $skillsData = [
            'PHP', 'Laravel 12', 'MySQL', 'REST API', 'Docker', 'Vue.js', 'ReactJS',
            'TypeScript', 'TailwindCSS', 'Figma', 'UI/UX', 'SEO', 'Google Ads',
            'Python', 'AWS', 'Git', 'Agile/Scrum', 'Data Analytics', 'Kế toán thuế', 'B2B Sales'
        ];

        $skills = [];
        foreach ($skillsData as $sk) {
            $skills[$sk] = Skill::create([
                'name' => $sk,
                'slug' => Str::slug($sk),
            ]);
        }

        // Gán kỹ năng cho Ứng viên 1
        $cand1->skills()->sync([
            $skills['PHP']->id,
            $skills['Laravel 12']->id,
            $skills['MySQL']->id,
            $skills['REST API']->id,
            $skills['Git']->id,
        ]);

        // Gán kỹ năng cho Ứng viên 2
        $cand2->skills()->sync([
            $skills['SEO']->id,
            $skills['Google Ads']->id,
            $skills['Data Analytics']->id,
        ]);

        // Gán kỹ năng cho Ứng viên 3
        $cand3->skills()->sync([
            $skills['Figma']->id,
            $skills['UI/UX']->id,
            $skills['TailwindCSS']->id,
        ]);

        // 8. Tạo CV cho ứng viên
        $cv1 = Cv::create([
            'user_id' => $cand1->id,
            'title' => 'CV Kỹ Sư Backend Laravel - Nguyễn Văn An',
            'file_path' => 'cvs/sample_cv_nguyen_van_an.pdf',
            'file_type' => 'pdf',
            'is_primary' => true,
        ]);

        $cv2 = Cv::create([
            'user_id' => $cand2->id,
            'title' => 'CV Digital Marketing Lead - Trần Thị Bích Ngọc',
            'file_path' => 'cvs/sample_cv_bich_ngoc.pdf',
            'file_type' => 'pdf',
            'is_primary' => true,
        ]);

        // 9. Tạo Tin tuyển dụng (Job Posts)
        $jobPostsData = [
            [
                'company_id' => $compFpt->id,
                'category_id' => $categories['it-software']->id,
                'location_id' => $locations['hcm']->id,
                'title' => 'Senior Laravel / Backend Engineer',
                'slug' => 'senior-laravel-backend-engineer-fpt',
                'description' => 'Tham gia thiết kế và phát triển các hệ sinh thái phần mềm quy mô lớn cho khách hàng quốc tế. Xây dựng RESTful API và kiến trúc Microservices tối ưu hiệu năng.',
                'requirements' => "Tối thiểu 3 năm kinh nghiệm với PHP và Laravel framework.\nNắm vững MySQL, tối ưu query, đánh index và caching Redis.\nCó kinh nghiệm với Docker, Git và CI/CD cơ bản.\nKhả năng đọc hiểu tài liệu tiếng Anh tốt.",
                'benefits' => "Mức thu nhập hấp dẫn từ 28 - 45 triệu kèm thưởng quý/năm.\nBảo hiểm sức khỏe FPT Care cho nhân viên và người thân.\nXét tăng lương định kỳ 2 lần/năm, hỗ trợ thi chứng chỉ quốc tế AWS/Laravel.",
                'salary_min' => 28000000,
                'salary_max' => 45000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'senior',
                'status' => 'approved',
                'is_featured' => true,
                'deadline' => Carbon::now()->addDays(7)->toDateString(),
                'views_count' => 1420,
                'skills' => ['PHP', 'Laravel 12', 'MySQL', 'REST API', 'Docker'],
            ],
            [
                'company_id' => $compVng->id,
                'category_id' => $categories['it-software']->id,
                'location_id' => $locations['hcm']->id,
                'title' => 'Lead Frontend Developer (Vue 3 / React)',
                'slug' => 'lead-frontend-developer-vng',
                'description' => 'Dẫn dắt đội ngũ Frontend xây dựng giao diện ứng dụng web tốc độ cao, tương tác mượt mà phục vụ hàng triệu người dùng hàng ngày.',
                'requirements' => "Từ 4 năm kinh nghiệm Frontend với Vue 3 / React, TypeScript.\nThành thạo State Management (Pinia/Redux), TailwindCSS.\nCó tư duy về Web Performance, SEO và Accessibility.",
                'benefits' => "Lương thỏa thuận 30 - 50 triệu tùy năng lực + ESOP hấp dẫn.\nLàm việc tại VNG Campus hiện đại bậc nhất, bao ăn sáng trưa chiều.\n18 ngày phép năm + chế độ du lịch công ty hàng năm.",
                'salary_min' => 30000000,
                'salary_max' => 50000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'lead',
                'status' => 'approved',
                'is_featured' => true,
                'deadline' => Carbon::now()->addDays(12)->toDateString(),
                'views_count' => 2310,
                'skills' => ['Vue.js', 'ReactJS', 'TypeScript', 'TailwindCSS'],
            ],
            [
                'company_id' => $compShopee->id,
                'category_id' => $categories['marketing']->id,
                'location_id' => $locations['hn']->id,
                'title' => 'Chuyên Viên Digital Marketing & Growth',
                'slug' => 'chuyen-vien-digital-marketing-growth-shopee',
                'description' => 'Lên kế hoạch và thực thi các chiến dịch Performance Marketing trên các kênh Google, Facebook, TikTok. Tối ưu chi phí chuyển đổi (CAC) và gia tăng người dùng mới.',
                'requirements' => "Tối thiểu 2 năm kinh nghiệm chạy chiến dịch quảng cáo đa kênh.\nThành thạo Google Analytics 4, Meta Ads Manager.\nTư duy phân tích số liệu nhạy bén, chủ động đề xuất giải pháp.",
                'benefits' => "Mức lương 18 - 25 triệu + thưởng hiệu suất chiến dịch.\nMôi trường làm việc năng động, sáng tạo, nhiều cơ hội thăng tiến.\nThưởng voucher mua sắm hàng tháng trên Shopee.",
                'salary_min' => 18000000,
                'salary_max' => 25000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'middle',
                'status' => 'approved',
                'is_featured' => true,
                'deadline' => Carbon::now()->addDays(5)->toDateString(),
                'views_count' => 890,
                'skills' => ['SEO', 'Google Ads', 'Data Analytics'],
            ],
            [
                'company_id' => $compMomo->id,
                'category_id' => $categories['design']->id,
                'location_id' => $locations['hcm']->id,
                'title' => 'Product UI/UX Designer (Fintech App)',
                'slug' => 'product-ui-ux-designer-momo',
                'description' => 'Tham gia nghiên cứu trải nghiệm người dùng, thiết kế wireframe, prototype và luồng tính năng thanh toán tài chính mượt mà trên ứng dụng MoMo.',
                'requirements' => "Tối thiểu 2 năm kinh nghiệm UI/UX app di động.\nSử dụng thành thạo Figma, xây dựng Design System nhất quán.\nCó portfolio dự án thực tế ấn tượng.",
                'benefits' => "Mức lương 22 - 35 triệu tùy portfolio và kinh nghiệm.\nĐược cấp Macbook Pro mới 100% khi nhận việc.\nTham gia các khóa đào tạo quốc tế về Product Design.",
                'salary_min' => 22000000,
                'salary_max' => 35000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'middle',
                'status' => 'approved',
                'is_featured' => true,
                'deadline' => Carbon::now()->addDays(9)->toDateString(),
                'views_count' => 1150,
                'skills' => ['Figma', 'UI/UX'],
            ],
            [
                'company_id' => $compViettel->id,
                'category_id' => $categories['it-software']->id,
                'location_id' => $locations['remote']->id,
                'title' => 'DevOps / Cloud Platform Engineer (AWS)',
                'slug' => 'devops-cloud-platform-engineer-viettel',
                'description' => 'Xây dựng và vận hành hạ tầng Kubernetes trên AWS Cloud. Tự động hóa quy trình CI/CD đảm bảo triển khai nhanh chóng và hệ thống sẵn sàng cao (High Availability).',
                'requirements' => "Có kiến thức vững về Linux, Docker, Kubernetes.\nKinh nghiệm quản trị hạ tầng AWS hoặc Viettel IDC Cloud.\nBiết viết script tự động hóa (Bash, Python).",
                'benefits' => "Thu nhập 35 - 55 triệu, gói phụ cấp công nghệ cạnh tranh.\nCơ hội làm việc với các hệ thống hạ tầng viễn thông hàng triệu RPS.\nHỗ trợ làm việc từ xa (Remote) linh hoạt.",
                'salary_min' => 35000000,
                'salary_max' => 55000000,
                'salary_negotiable' => false,
                'type' => 'remote',
                'experience_level' => 'senior',
                'status' => 'approved',
                'is_featured' => true,
                'deadline' => Carbon::now()->addDays(4)->toDateString(),
                'views_count' => 1670,
                'skills' => ['AWS', 'Docker', 'Python', 'Git'],
            ],
            [
                'company_id' => $compFpt->id,
                'category_id' => $categories['it-software']->id,
                'location_id' => $locations['dn']->id,
                'title' => 'Junior Web Developer (PHP & MySQL)',
                'slug' => 'junior-web-developer-fpt-danang',
                'description' => 'Tham gia bảo trì và phát triển tính năng mới cho các hệ thống website quản trị doanh nghiệp. Được mentor 1-1 bởi các kỹ sư Senior.',
                'requirements' => "Tốt nghiệp Cao đẳng/Đại học chuyên ngành CNTT hoặc tương đương.\nNắm chắc kiến thức căn bản về PHP, OOP, MySQL, HTML/CSS/JS.\nChăm chỉ, ham học hỏi và có tinh thần trách nhiệm.",
                'benefits' => "Lương khởi điểm 10 - 16 triệu + phụ cấp ăn trưa.\nLộ trình đào tạo rõ ràng, xét lên Middle sau 1 năm.\nLàm việc tại FPT Complex Đà Nẵng xanh và tiện nghi.",
                'salary_min' => 10000000,
                'salary_max' => 16000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'junior',
                'status' => 'approved',
                'is_featured' => false,
                'deadline' => Carbon::now()->addDays(20)->toDateString(),
                'views_count' => 450,
                'skills' => ['PHP', 'MySQL', 'Git'],
            ],
            [
                'company_id' => $compShopee->id,
                'category_id' => $categories['sales']->id,
                'location_id' => $locations['hcm']->id,
                'title' => 'Key Account Executive (E-commerce)',
                'slug' => 'key-account-executive-shopee',
                'description' => 'Quản lý quan hệ với các nhãn hàng đối tác chiến lược trên sàn Shopee Mall. Hỗ trợ tối ưu gian hàng và đẩy mạnh doanh thu các đợt Mega Sale.',
                'requirements' => "1 - 2 năm kinh nghiệm làm việc trong lĩnh vực Sales B2B hoặc Thương mại điện tử.\nKỹ năng đàm phán, giao tiếp và xử lý tình huống khéo léo.\nSử dụng tốt Excel và các công cụ phân tích số liệu bán hàng.",
                'benefits' => "Thu nhập 15 - 22 triệu (Lương cứng + hoa hồng doanh số).\nThưởng các kỳ chiến dịch lớn 11.11, 12.12 hấp dẫn.\nĐược đào tạo bài bản quy trình quản trị thương mại điện tử quốc tế.",
                'salary_min' => 15000000,
                'salary_max' => 22000000,
                'salary_negotiable' => false,
                'type' => 'full-time',
                'experience_level' => 'middle',
                'status' => 'approved',
                'is_featured' => false,
                'deadline' => Carbon::now()->addDays(15)->toDateString(),
                'views_count' => 780,
                'skills' => ['B2B Sales', 'Data Analytics'],
            ],
        ];

        $createdJobPosts = [];
        foreach ($jobPostsData as $jobData) {
            $skillsToSync = $jobData['skills'] ?? [];
            unset($jobData['skills']);

            $jobPost = JobPost::create($jobData);
            $createdJobPosts[] = $jobPost;

            if (!empty($skillsToSync)) {
                $skillIds = [];
                foreach ($skillsToSync as $sName) {
                    if (isset($skills[$sName])) {
                        $skillIds[] = $skills[$sName]->id;
                    }
                }
                $jobPost->skills()->sync($skillIds);
            }
        }

        // 10. Tạo Hồ sơ ứng tuyển mẫu (Applications)
        // Ứng viên 1 nộp vào Job 1 (FPT Senior Laravel) - Trạng thái đã xem
        $app1 = Application::create([
            'job_post_id' => $createdJobPosts[0]->id,
            'user_id' => $cand1->id,
            'cv_id' => $cv1->id,
            'cover_letter' => 'Kính gửi ban tuyển dụng FPT Software, tôi có 3 năm kinh nghiệm lập trình Laravel và rất hào hứng với vị trí này.',
            'status' => 'viewed',
            'applied_at' => Carbon::now()->subDays(2),
        ]);

        // Ứng viên 1 nộp vào Job 2 (VNG Lead Frontend) - Trạng thái Mời phỏng vấn
        $app2 = Application::create([
            'job_post_id' => $createdJobPosts[1]->id,
            'user_id' => $cand1->id,
            'cv_id' => $cv1->id,
            'cover_letter' => 'Tôi đã có kinh nghiệm xây dựng ứng dụng với Vue 3 và TypeScript, mong muốn đóng góp vào dự án của VNG.',
            'status' => 'interview',
            'applied_at' => Carbon::now()->subDays(3),
        ]);

        // Tạo lịch phỏng vấn cho Application 2
        Interview::create([
            'application_id' => $app2->id,
            'interview_date' => Carbon::now()->addDays(3)->setTime(14, 0),
            'interview_type' => 'online',
            'location_or_link' => 'https://meet.google.com/job-vng-interview',
            'note' => 'Ứng viên vui lòng chuẩn bị máy tính có kết nối webcam và micro để phỏng vấn kỹ thuật trực tuyến.',
            'status' => 'scheduled',
        ]);

        // Ứng viên 2 nộp vào Job 3 (Shopee Marketing) - Trạng thái Đã nộp (chưa xem)
        $app3 = Application::create([
            'job_post_id' => $createdJobPosts[2]->id,
            'user_id' => $cand2->id,
            'cv_id' => $cv2->id,
            'cover_letter' => 'Tôi có thế mạnh về phân tích số liệu chuyển đổi và chạy chiến dịch Performance Ads.',
            'status' => 'submitted',
            'applied_at' => Carbon::now()->subHours(5),
        ]);
    }
}
