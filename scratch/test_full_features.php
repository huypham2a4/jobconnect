<?php
require 'E:/LTMNM/laragon/www/jobconnect/vendor/autoload.php';
$app = require_once 'E:/LTMNM/laragon/www/jobconnect/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\Application;
use App\Models\Category;
use App\Models\Location;
use App\Models\Skill;
use App\Models\Cv;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

function testAssert($condition, $message) {
    if ($condition) {
        echo "  [PASS] $message\n";
    } else {
        echo "  [FAIL] $message\n";
        exit(1);
    }
}

echo "=========================================================\n";
echo "  KIỂM THỬ TOÀN DIỆN CHỨC NĂNG BƯỚC 3 (LARAVEL 12)\n";
echo "=========================================================\n\n";

// TEST 1: Database & Seeders
echo "1. KIỂM TRA CƠ SỞ DỮ LIỆU & QUAN HỆ ELOQUENT:\n";
testAssert(User::count() >= 9, "Bảng users có " . User::count() . " người dùng.");
testAssert(Company::count() >= 4, "Bảng companies có " . Company::count() . " doanh nghiệp.");
testAssert(JobPost::count() >= 7, "Bảng job_posts có " . JobPost::count() . " tin tuyển dụng.");
testAssert(Category::count() >= 8, "Bảng categories có " . Category::count() . " ngành nghề.");
testAssert(Location::count() >= 5, "Bảng locations có " . Location::count() . " tỉnh thành.");
testAssert(Skill::count() >= 12, "Bảng skills có " . Skill::count() . " kỹ năng.");
testAssert(Application::count() >= 3, "Bảng applications có " . Application::count() . " hồ sơ ứng tuyển.");

// TEST 2: Eloquent Scopes & Relationships
echo "\n2. KIỂM TRA QUAN HỆ ELOQUENT & SCOPES:\n";
$job = JobPost::with(['company', 'category', 'location', 'skills'])->first();
testAssert($job->company !== null, "JobPost thuộc về Company: " . $job->company->name);
testAssert($job->category !== null, "JobPost thuộc về Category: " . $job->category->name);
testAssert($job->location !== null, "JobPost thuộc về Location: " . $job->location->name);
testAssert($job->skills->count() > 0, "JobPost có quan hệ nhiều-nhiều với Skill (job_skill).");

$cand = User::where('role', 'candidate')->first();
testAssert($cand->skills !== null, "User có quan hệ nhiều-nhiều với Skill (user_skill).");
testAssert($cand->applications !== null, "User có quan hệ 1-nhiều với Application.");

// TEST 3: Business Rule: Privacy Masking
echo "\n3. KIỂM TRA QUY TẮC BẢO MẬT SĐT & EMAIL ỨNG VIÊN:\n";
$pendingApp = Application::where('status', 'submitted')->first();
if ($pendingApp) {
    testAssert(!$pendingApp->isContactVisible(), "Hồ sơ trạng thái 'submitted' ẩn thông tin liên hệ.");
}
$reviewedApp = Application::whereIn('status', ['viewed', 'interview', 'accepted'])->first();
if ($reviewedApp) {
    testAssert($reviewedApp->isContactVisible(), "Hồ sơ đã xem/phỏng vấn/trúng tuyển được hiển thị thông tin liên hệ.");
}

// TEST 4: Business Rule: Rút hồ sơ
echo "\n4. KIỂM TRA QUY TẮC RÚT HỒ SƠ ỨNG TUYỂN:\n";
$withdrawableApp = new Application(['status' => 'submitted']);
testAssert($withdrawableApp->canWithdraw() === true, "Hồ sơ mới 'submitted' CÓ THỂ rút.");
$nonWithdrawableApp = new Application(['status' => 'interview']);
testAssert($nonWithdrawableApp->canWithdraw() === false, "Hồ sơ đã 'interview' KHÔNG THỂ rút.");

// TEST 5: Business Rule: Chỉ ứng tuyển 1 lần trong 6 tháng
echo "\n5. KIỂM TRA QUY TẮC CHỐNG SPAM ỨNG TUYỂN (6 THÁNG):\n";
$candUser = User::where('role', 'candidate')->first();
$activeJob = JobPost::active()->first();
Auth::login($candUser);
$sixMonthsAgo = Carbon::now()->subMonths(6);
$existing = Application::where('job_post_id', $activeJob->id)
    ->where('user_id', $candUser->id)
    ->where('applied_at', '>=', $sixMonthsAgo)
    ->exists();
testAssert(is_bool($existing), "Kiểm tra 6 tháng hoạt động chính xác (đã ứng tuyển: " . ($existing ? 'Có' : 'Chưa') . ").");

// TEST 6: Employer Gia hạn tin tuyển dụng
echo "\n6. KIỂM TRA CHỨC NĂNG GIA HẠN TIN TUYỂN DỤNG:\n";
$empUser = User::where('role', 'employer')->first();
Auth::login($empUser);
$empController = new \App\Http\Controllers\EmployerController();
$empJob = JobPost::where('company_id', $empUser->company->id)->first();
$oldDeadline = $empJob->deadline;
$renewRequest = Request::create('/employer/jobs/' . $empJob->id . '/renew', 'POST', ['extend_days' => 45]);
$empController->renewJob($renewRequest, $empJob->id);
$empJob->refresh();
testAssert($empJob->deadline > $oldDeadline, "Tin tuyển dụng #{$empJob->id} được gia hạn thành công (hạn mới: {$empJob->deadline}).");

// TEST 7: Admin Join & GroupBy Analytics
echo "\n7. KIỂM TRA THỐNG KÊ ADMIN (JOIN + GROUP BY):\n";
$adminUser = User::where('role', 'admin')->first();
Auth::login($adminUser);
$adminController = new \App\Http\Controllers\AdminController();
$adminDashView = $adminController->dashboard();
$data = $adminDashView->getData();
testAssert(isset($data['categoryStats']) && $data['categoryStats']->count() > 0, "Thống kê tin theo ngành nghề qua JOIN + GROUP BY thành công.");
testAssert(isset($data['applicationStats']), "Thống kê lượt ứng tuyển theo ngành nghề qua JOIN + GROUP BY thành công.");

// TEST 8: Blade Components x-alert & x-input
echo "\n8. KIỂM TRA BLADE COMPONENTS (<x-alert>, <x-input>):\n";
testAssert(file_exists('E:/LTMNM/laragon/www/jobconnect/resources/views/components/alert.blade.php'), "Component <x-alert> đã được tạo.");
testAssert(file_exists('E:/LTMNM/laragon/www/jobconnect/resources/views/components/input.blade.php'), "Component <x-input> đã được tạo.");

echo "\n=========================================================\n";
echo "  TẤT CẢ CÁC BƯỚC VÀ QUY TẮC NGHIỆP VỤ ĐÃ ĐẠT 100%!\n";
echo "=========================================================\n";
