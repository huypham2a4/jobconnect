# JOBCONNECT - NỀN TẢNG TUYỂN DỤNG & VIỆC LÀM THÔNG MINH

> **Đồ Án Môn Học**: Lập Trình Mã Nguồn Mở (LTMNM)  
> **Đơn Vị**: Trường Đại Học Công Thương TP.HCM (HUIT)  
> **Nhóm Thực Hiện**: Nhóm 9  
> **Nền Tảng Công Nghệ**: Laravel 12 + PHP 8.3 + MySQL (Laragon) + Blade Template + CSS thuần / Bootstrap 5 chuẩn hóa  

---

## 1. GIỚI THIỆU DỰ ÁN
JobConnect là nền tảng kết nối trực tiếp giữa **Ứng viên (Candidate)**, **Nhà tuyển dụng (Employer)** và được quản lý tập trung bởi **Quản trị viên (Admin)**. Hệ thống được chuyển đổi toàn diện sang mô hình kiến trúc MVC của Laravel 12, tuân thủ nghiêm ngặt chuẩn kiến thức thực hành từ Lab 1 đến Lab 8:
- Sử dụng mô hình MVC chuẩn RESTful (7 actions resource).
- Xác thực phân quyền session-based bảo mật thuần (không phụ thuộc Breeze/Sanctum).
- Quan hệ Eloquent chặt chẽ (`hasMany`, `belongsTo`, `belongsToMany`, `hasOne`).
- Thiết kế giao diện kép: Chế độ **Chuẩn (Light/Corporate)** và chế độ **"Tái Sinh – Bùng Cháy" (Rebirth Dark/Fire)** chuyển đổi tức thì bằng công tắc gạt.

---

## 2. YÊU CẦU MÔI TRƯỜNG & PHẦN MỀM
- **Hệ thống phát triển**: Laragon (khuyến nghị trên Windows).
- **PHP**: Phiên bản `>= 8.2` (Hiện tại đang chạy trên PHP 8.3.30 ZTS).
- **Cơ sở dữ liệu**: MySQL 8.x / MariaDB.
- **Quản lý gói**: Composer 2.x.
- **Trình duyệt**: Chrome, Edge, Firefox, Brave.

---

## 3. HƯỚNG DẪN CÀI ĐẶT & CHẠY TRÊN LARAGON (TỪNG BƯỚC)

### Bước 1: Khởi động Laragon & Tạo Cơ sở dữ liệu
1. Mở phần mềm **Laragon**, nhấn nút **Start All** để khởi động Apache & MySQL.
2. Mở công cụ quản lý CSDL (nhấn **Database** trong Laragon hoặc truy cập phpMyAdmin tại `http://localhost/phpmyadmin`).
3. Tạo mới một database có tên:
   ```sql
   CREATE DATABASE jobconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### Bước 2: Thiết lập file môi trường (.env)
1. Đảm bảo file `.env` đã được cấu hình đúng thông số kết nối MySQL:
   ```ini
   APP_NAME=JobConnect
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jobconnect
   DB_USERNAME=root
   DB_PASSWORD=

   SESSION_DRIVER=file
   CACHE_STORE=file
   FILESYSTEM_DISK=public
   ```

### Bước 3: Cài đặt thư viện phụ thuộc
Mở terminal trong thư mục dự án (`E:\LTMNM\laragon\www\jobconnect`):
```bash
composer install
```

### Bước 4: Khởi tạo Application Key
```bash
php artisan key:generate
```

### Bước 5: Chạy Migration & Seeder dữ liệu mẫu
Lệnh này sẽ tự động tạo toàn bộ cấu trúc bảng và nạp tài khoản, dữ liệu mẫu đa dạng:
```bash
php artisan migrate:fresh --seed
```

### Bước 6: Tạo liên kết Storage (Lưu trữ CV & Hình ảnh)
```bash
php artisan storage:link
```

### Bước 7: Khởi chạy máy chủ nội bộ
```bash
php artisan serve --port=8000
```
Truy cập hệ thống tại: **`http://127.0.0.1:8000`**

---

## 4. TÀI KHOẢN TRẢI NGHIỆM HỆ THỐNG (DEMO SEED DATA)

Mật khẩu chung cho tất cả tài khoản mẫu là: **`password`**

| Vai trò (Role) | Email đăng nhập | Mật khẩu | Chức năng nổi bật |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@jobconnect.vn` | `password` | Duyệt/từ chối tin, khóa/mở người dùng, quản lý danh mục, thống kê Join & Group By. |
| **Nhà tuyển dụng 1** | `fpt@jobconnect.vn` | `password` | Quản lý tin FPT Software, xét duyệt hồ sơ ứng viên, gửi lời mời phỏng vấn, gia hạn tin. |
| **Nhà tuyển dụng 2** | `shopee@jobconnect.vn` | `password` | Quản lý tin Shopee Vietnam, quản lý hồ sơ, cập nhật thông tin công ty. |
| **Ứng viên 1** | `nguyenvanan@gmail.com` | `password` | Xem việc làm, ứng tuyển, quản lý CV (PDF/Docx), theo dõi trạng thái, rút hồ sơ. |
| **Ứng viên 2** | `tranthib@gmail.com` | `password` | Tìm kiếm theo kỹ năng, nộp hồ sơ kèm thư ngỏ, cập nhật kỹ năng cá nhân. |

---

## 5. DANH SÁCH CHỨC NĂNG THEO VAI TRÒ & QUY TẮC NGHIỆP VỤ

### 1. Phân Hệ Ứng Viên (Candidate)
- **Xác thực**: Đăng ký, đăng nhập tài khoản ứng viên (session-based).
- **Hồ sơ & Kỹ năng**: Cập nhật thông tin liên hệ, tiểu sử, chọn kỹ năng năng lực từ bảng `skills` qua bảng trung gian `user_skill`.
- **Quản lý CV (CRUD)**: Tải lên nhiều phiên bản CV (PDF, DOC, DOCX tối đa 5MB) lưu trữ trong `storage/app/public/cvs`, đặt CV mặc định, xem/tải về và xóa CV.
- **Tìm kiếm & Bộ lọc nâng cao**: Tìm việc theo từ khóa, ngành nghề, địa điểm, khoảng lương, hình thức làm việc, sắp xếp theo hạn nộp/lương cao nhất có phân trang.
- **Ứng tuyển việc làm**: Nộp hồ sơ trực tiếp từ CV có sẵn hoặc tải CV mới kèm thư ngỏ cá nhân hóa.
- **Lịch sử & Rút hồ sơ**: Theo dõi tiến độ hồ sơ qua 5 giai đoạn (`submitted` $\to$ `viewed` $\to$ `interview` $\to$ `accepted` $\to$ `rejected`). Có thể rút hồ sơ khi nhà tuyển dụng chưa duyệt.

### 2. Phân Hệ Nhà Tuyển Dụng (Employer)
- **Đăng ký doanh nghiệp**: Đăng ký tài khoản kèm tự động khởi tạo hồ sơ công ty.
- **Hồ sơ công ty**: Cập nhật tên, quy mô, trụ sở, website, mô tả doanh nghiệp.
- **CRUD Tin tuyển dụng**: Đăng tin mới, chỉnh sửa thông tin, yêu cầu & đãi ngộ, đóng tin, mở lại hoặc **Gia hạn tin tuyển dụng** thêm 30 ngày.
- **Xét duyệt ứng viên**: Xem danh sách ứng viên nộp theo từng tin, lọc trạng thái, mở khóa thông tin liên hệ khi chuyển trạng thái duyệt.
- **Lên lịch phỏng vấn**: Gửi lời mời phỏng vấn trực tiếp hoặc trực tuyến kèm thời gian, địa điểm, link họp và ghi chú cho ứng viên (lưu tại bảng `interviews`).

### 3. Phân Hệ Quản Trị Viên (Admin)
- **Duyệt tin tuyển dụng**: Phê duyệt hoặc từ chối các tin tuyển dụng mới tạo từ nhà tuyển dụng trước khi hiển thị ra trang chủ.
- **Quản lý người dùng**: Tra cứu, lọc theo vai trò, khóa hoặc mở khóa tài khoản người dùng vi phạm.
- **Quản trị danh mục**: Thêm mới và quản lý ngành nghề tuyển dụng (`categories`), gán icon và màu sắc.
- **Báo cáo & Thống kê thông minh**:
  - Thống kê phân bổ tin tuyển dụng theo ngành nghề (áp dụng câu lệnh SQL `JOIN` + `GROUP BY`).
  - Thống kê tỷ lệ ứng tuyển theo danh mục (áp dụng `JOIN` nhiều bảng giữa `categories`, `job_posts`, `applications` + `GROUP BY`).

---

## 6. CÁC QUY TẮC NGHIỆP VỤ ĐÃ HIỆN THỰC
1. **Kiểm duyệt hiển thị**: Chỉ những tin có `status = 'approved'` và chưa quá hạn (`deadline >= CURDATE()`) mới xuất hiện trên trang tìm kiếm công khai.
2. **Chống spam ứng tuyển**: Mỗi ứng viên chỉ được phép nộp hồ sơ vào cùng 1 vị trí tuyển dụng duy nhất 1 lần trong vòng 6 tháng.
3. **Quyền rút hồ sơ**: Ứng viên chỉ được phép rút hồ sơ khi hồ sơ ở trạng thái `submitted` (chưa được NTD duyệt xem).
4. **Bảo mật thông tin liên hệ**: Số điện thoại và Email của ứng viên được ẩn dạng che giấu (`0987***`, `user***@...`) cho đến khi nhà tuyển dụng xét duyệt tiếp nhận (`status != 'submitted'`).

---

## 7. CẤU TRÚC THƯ MỤC CHÍNH CỦA DỰ ÁN
```
jobconnect/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # AuthController, JobController, CandidateController, EmployerController, AdminController
│   │   └── Middleware/        # RoleMiddleware (phân quyền session thuần)
│   └── Models/                # User, Company, JobPost, Application, Interview, Cv, Category, Skill, Location
├── database/
│   ├── migrations/            # 10 file migration chuẩn Laravel 12
│   └── seeders/               # DatabaseSeeder dữ liệu mẫu thực tế
├── public/
│   ├── css/                   # jobconnect.css (chuẩn) & theme-rebirth.css (Tái Sinh - Bùng Cháy)
│   ├── js/                    # jobconnect.js (xử lý switch theme, hạt tro than, bộ đếm số liệu, que diêm)
│   └── storage/               # Symlink đến storage/app/public
├── resources/
│   └── views/
│       ├── layouts/           # app.blade.php
│       ├── components/        # alert.blade.php (<x-alert>), input.blade.php (<x-input>)
│       ├── auth/              # login.blade.php, register.blade.php
│       ├── jobs/              # index.blade.php, show.blade.php
│       ├── candidate/         # dashboard, applications, cvs, profile
│       ├── employer/          # dashboard, company, jobs (create, edit, index), applications (index)
│       └── admin/             # dashboard, jobs, users, categories
└── routes/
    └── web.php                # Định tuyến đầy đủ 5 phân hệ với route group & middleware role
```
