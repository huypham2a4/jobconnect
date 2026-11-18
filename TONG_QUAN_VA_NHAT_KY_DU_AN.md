# JobConnect — Tổng quan và nhật ký dự án

Cập nhật đến ngày **13/11/2026**.

File này ghi lại web JobConnect đang có gì, những phần anh em mình đã kiểm tra và bổ sung, cách chạy, cách test và những điểm cần nhớ khi làm tiếp. Đây là bản tổng hợp trạng thái hiện tại của dự án, có thể dùng để ôn lại, trình bày với thầy hoặc cung cấp ngữ cảnh cho người tiếp tục sửa code.

## 1. Thông tin nhanh

| Nội dung | Hiện trạng |
|---|---|
| Tên web | JobConnect |
| Mục đích | Kết nối ứng viên với nhà tuyển dụng; Admin quản lý và kiểm duyệt |
| Bối cảnh | Đồ án môn Lập trình mã nguồn mở; README hiện ghi Nhóm 9, HUIT |
| Thư mục trên máy | `D:\Laragon_PHP\laragon\www\DA\jobconnect-main` |
| Địa chỉ thường dùng | <http://127.0.0.1:8000> |
| Backend | PHP 8.3, Laravel **13.37.0** theo `composer.lock` |
| Database sử dụng hằng ngày | MySQL, database `jobconnect` |
| Giao diện | Blade, CSS và JavaScript |
| Môi trường local | Laragon trên Windows |
| Xác thực | Đăng nhập bằng session; mật khẩu được hash |
| Ba vai trò | `candidate`, `employer`, `admin` |
| Kiểm thử | PHPUnit; database test là SQLite trong bộ nhớ |

Laragon cung cấp môi trường PHP, MySQL và công cụ chạy local. Phần ứng dụng được viết bằng Laravel theo mô hình MVC. Nút chạy đã chuẩn bị sử dụng PHP web server ở cổng 8000 hoặc cổng trống tiếp theo trong khoảng 8000–8020.

Các màn hình đang sử dụng lấy CSS/JavaScript trong `public`, nên cách chạy bằng file bat không cần build npm cho giao diện này.

## 2. Điểm xuất phát và phạm vi công việc

JobConnect đã có source và nhiều chức năng cơ bản trước khi anh em mình làm việc. Các controller, model, màn hình ba vai trò và bộ dữ liệu ban đầu là nền tảng được tiếp nhận để kiểm tra, sửa và mở rộng.

| Lúc kiểm tra ban đầu | Phần đã xử lý |
|---|---|
| Cần thao tác khởi chạy thủ công | Tạo file bat chạy, dừng và test |
| Tin mới của nhà tuyển dụng được đặt thẳng thành `approved` | Chuyển về quy trình chờ Admin duyệt |
| Nút lưu việc chỉ đổi giao diện | Lưu thật trong database, giữ sau khi đăng nhập lại |
| CV seed có bản ghi nhưng thiếu tệp thật | Bổ sung PDF mẫu mở/tải được |
| File test cũ có đường dẫn máy khác và sửa dữ liệu | Thay bằng lệnh gọi bộ PHPUnit an toàn hơn |
| Đầu trang, menu và bố cục còn chật | Chỉnh lại giao diện và cách hiển thị trên điện thoại |
| Nhóm tài khoản trải nghiệm sát nhau | Làm lại thành các thẻ có icon, tên và email tách dòng |
| Chỉ có 5 công ty, 7 tin và 3 ứng viên | Thêm bộ dữ liệu minh họa lớn hơn |
| Phân trang mặc định hiển thị xấu khi có nhiều dữ liệu | Tạo giao diện phân trang riêng bằng tiếng Việt |
| Chưa có trang xem và chỉnh quyền | Thêm trang Phân quyền cho Admin và kiểm tra quyền ở server |

## 3. Các chức năng hiện có

### 3.1. Khách chưa đăng nhập

- Xem trang chủ, thống kê và những tin đang công khai.
- Tìm việc theo từ khóa, ngành, địa điểm, hình thức làm việc và mức lương.
- Sắp xếp theo tin mới, lương hoặc hạn nộp; chuyển trang kết quả.
- Xem chi tiết tin đã duyệt và còn hạn.
- Đăng ký ứng viên hoặc nhà tuyển dụng, đăng nhập và đổi giao diện sáng/tối.

Liên kết Ngành nghề và Công ty ở đầu trang dẫn đến các khu vực tương ứng trên trang chủ.

### 3.2. Ứng viên

- Xem tổng quan cá nhân.
- Cập nhật họ tên, điện thoại, giới thiệu và kỹ năng.
- Tải CV PDF/DOC/DOCX lên, đổi tên, chọn CV mặc định, mở/tải tệp và xóa CV khi được phép.
- Lưu hoặc bỏ lưu việc làm; danh sách thuộc từng tài khoản.
- Ứng tuyển bằng CV có sẵn hoặc tải CV mới ngay khi nộp.
- Gửi thư ngỏ kèm hồ sơ.
- Xem lịch sử, trạng thái và lịch phỏng vấn.
- Rút hồ sơ còn ở trạng thái được phép rút.

### 3.3. Nhà tuyển dụng

- Đăng ký kèm hồ sơ công ty.
- Xem tổng quan tuyển dụng của công ty mình.
- Cập nhật tên, thành phố, quy mô, địa chỉ, website và giới thiệu công ty.
- Tạo tin, sửa tin và gửi Admin duyệt.
- Đóng tin, yêu cầu mở lại và gia hạn thêm 30 ngày từ giao diện.
- Xem hồ sơ ứng viên nộp vào tin của công ty mình, lọc theo tin và trạng thái.
- Mở CV, cập nhật trạng thái xử lý và tiếp nhận thông tin liên hệ.
- Tạo hoặc sửa lịch phỏng vấn trực tuyến/trực tiếp.
- Cập nhật kết quả trúng tuyển hoặc chưa phù hợp.

### 3.4. Admin

- Xem thống kê người dùng, tin tuyển dụng và lượt ứng tuyển.
- Thống kê theo ngành bằng các truy vấn `JOIN`, `COUNT`, `GROUP BY`.
- Xem trước, duyệt hoặc từ chối tin tuyển dụng.
- Tìm/lọc người dùng, khóa và mở khóa tài khoản.
- Thêm và liệt kê ngành nghề.
- Xem, chỉnh quyền của ba vai trò và tra quyền theo từng người dùng.

Các chức năng cần đăng nhập còn phụ thuộc vào quyền Admin đang bật cho vai trò tương ứng.

## 4. Phần phân quyền đã bổ sung

### 4.1. Đường vào và cách dùng

1. Đăng nhập bằng tài khoản Admin.
2. Chọn **Phân quyền** trong menu hoặc hàng tab quản trị.
3. Chọn **Ứng viên**, **Nhà tuyển dụng** hoặc **Quản trị viên**.
4. Bật/tắt các ô **Xem**, **Tạo mới**, **Cập nhật**, **Xóa** phù hợp với từng chức năng.
5. Bấm **Lưu quyền**.
6. Dùng phần **Quyền theo người dùng** để tìm người, lọc trạng thái và bấm **Xem quyền**.

Trang quản trị: <http://127.0.0.1:8000/admin/permissions>.

Quyền được chỉnh **theo vai trò**. Một lần lưu ảnh hưởng đến mọi tài khoản cùng vai trò đó. Phần tra cứu người dùng cho biết quyền họ nhận từ vai trò và tình trạng hoạt động của tài khoản.

### 4.2. Cấu hình mặc định

| Vai trò | Số quyền theo cấu hình mặc định | Phạm vi chính |
|---|---:|---|
| Ứng viên | 13 | Tổng quan, hồ sơ cá nhân, CV, lưu việc, ứng tuyển |
| Nhà tuyển dụng | 11 | Tổng quan, công ty, tin tuyển dụng (gồm xóa tin rỗng), ứng viên, lịch phỏng vấn |
| Admin | 11 | Tổng quan, kiểm duyệt, người dùng, ngành nghề (thêm/sửa/xóa), phân quyền |

Tổng cộng có **35 bản ghi quyền theo vai trò** khi khởi tạo. Số quyền đang bật có thể thay đổi sau khi Admin điều chỉnh.

### 4.3. Cách kiểm tra quyền hoạt động

- Quyền lưu trong bảng `role_permissions`.
- `RoleMiddleware` kiểm tra đăng nhập, tài khoản hoạt động và đúng vai trò.
- `EnsurePermission` kiểm tra quyền của chức năng trước khi cho request chạy tiếp.
- `RolePermission` chỉ chấp nhận quyền thuộc phạm vi của vai trò đó; thiếu bản ghi quyền tùy chọn thì không tự cấp quyền.
- Đường dẫn được bảo vệ có ánh xạ quyền trong `config/role_permissions.php`.
- Thu hồi quyền thì request tiếp theo bị kiểm tra lại, kể cả khi người dùng đã đăng nhập từ trước.
- Gõ URL hoặc gửi request trực tiếp không bỏ qua kiểm tra quyền.
- Menu tài khoản và tab quản lý được lọc theo quyền xem trang.
- Sau đăng nhập, web chọn trang đầu tiên được phép truy cập; nếu không còn trang quản lý phù hợp thì về trang chủ.
- Quyền tạo lịch phỏng vấn và quyền sửa lịch đã có được kiểm tra riêng.

Ba quyền của Admin luôn được giữ là `dashboard.view`, `permissions.view`, `permissions.update`, giúp Admin còn đường vào để quản lý lại quyền.

Dấu **—** trên bảng nghĩa là chức năng đó không có thao tác tương ứng (ví dụ Trang tổng quan chỉ có thao tác Xem; Kiểm duyệt tin và Quản lý người dùng chỉ có Xem và Cập nhật).

Trang này hiện chưa cấp quyền ghi đè riêng cho từng tài khoản và chưa có chức năng đổi vai trò của một người dùng. Việc chỉnh quyền cũng không bỏ qua giới hạn sở hữu CV, hồ sơ cá nhân hoặc công ty.

### 4.4. Một tình huống để trình bày với thầy

1. Admin chọn vai trò **Ứng viên**.
2. Bỏ ô **Cập nhật** của **Hồ sơ cá nhân**, rồi lưu.
3. Đăng nhập ứng viên và thử lưu thay đổi hồ sơ: request bị chặn, dữ liệu cũ giữ nguyên.
4. Admin bật lại quyền đó và lưu.
5. Ứng viên thử lại: cập nhật được.

Nên dùng tài khoản thử hoặc thực hiện trong lúc không có người khác đang sửa dữ liệu, vì quyền áp dụng cho cả vai trò. Bật lại quyền sau khi trình bày để tiếp tục test các chức năng khác.

## 5. Các quy tắc nghiệp vụ cần nhớ

### Tin tuyển dụng

| Trạng thái | Ý nghĩa |
|---|---|
| `pending` | Chờ Admin duyệt |
| `approved` | Đã duyệt; còn hạn mới công khai |
| `closed` | Nhà tuyển dụng đã đóng tin |
| `rejected` | Admin đã từ chối |

- Tin mới tạo, tin sửa nội dung và tin yêu cầu mở lại đều phải chờ duyệt.
- Gia hạn chỉ thay hạn nộp; không tự chuyển tin thành đã duyệt.
- Chỉ tin `approved` và chưa hết hạn mới hiện trong tìm kiếm công khai và nhận ứng tuyển.
- Chủ tin và Admin có thể xem trước tin chưa công khai.
- Nhà tuyển dụng chỉ được thao tác với tin và ứng viên thuộc công ty mình.

### Hồ sơ ứng tuyển

| Trạng thái | Nhãn phía ứng viên |
|---|---|
| `submitted` | Đã nộp hồ sơ |
| `viewed` | NTD đã xem |
| `interview` | Mời phỏng vấn |
| `accepted` | Đã trúng tuyển |
| `rejected` | Chưa phù hợp |
| `withdrawn` | Đã rút hồ sơ |

- Một ứng viên không nộp lại cùng vị trí trong vòng 6 tháng, kể cả đã rút hồ sơ.
- Chỉ hồ sơ còn `submitted` được rút.
- Không tiếp tục xử lý hồ sơ đã rút.
- Liên hệ được hiển thị cho nhà tuyển dụng ở các trạng thái `viewed`, `interview`, `accepted`.
- Nhãn chọn phía nhà tuyển dụng có thể là **Đã xem xét**, **Trúng tuyển**; phía ứng viên dùng nhãn như bảng trên.
- Lịch phỏng vấn phải ở tương lai và có địa điểm hoặc đường dẫn.

### CV và tài khoản

- CV nhận PDF, DOC, DOCX; tối đa 5 MB.
- CV đầu tiên là mặc định. Xóa CV mặc định chưa sử dụng sẽ chọn lại CV còn lại.
- CV đã dùng ứng tuyển được giữ lại, không xóa qua chức năng quản lý CV.
- Tên tệp upload được tạo riêng để các lần tải lên không ghi đè nhau.
- Không được dùng hoặc sửa CV của người khác.
- Email đăng ký không trùng; xác nhận mật khẩu phải khớp.
- Người dùng không tự đăng ký vai trò Admin.
- Tài khoản khóa không được đăng nhập hoặc tiếp tục dùng các chức năng được bảo vệ.
- Admin không tự khóa tài khoản đang đăng nhập.

## 6. Các thay đổi giao diện đã làm

- Chỉnh đầu trang, logo, menu điều hướng và menu tài khoản cho gọn hơn.
- Dùng giao diện xanh/trắng, thẻ việc làm và các khu vực trang chủ rõ hơn.
- Chỉnh trang tìm việc và bộ lọc.
- Hỗ trợ sáng/tối, lưu lựa chọn sau khi tải lại.
- Sửa bố cục màn hình hẹp; đã kiểm tra các kích thước 390 px và 320 px ở những trang liên quan.
- Làm lại các tài khoản trải nghiệm nhanh ở trang đăng nhập: icon màu, vai trò và email tách dòng, mật khẩu chung đặt dưới danh sách.
- Các nút trải nghiệm vẫn tự điền email/mật khẩu, chưa tự gửi form đăng nhập.
- Thêm phân trang riêng bằng tiếng Việt, xử lý lỗi mũi tên mặc định quá lớn khi dữ liệu nhiều.
- Làm trang Phân quyền có thẻ vai trò, bảng quyền, tra người dùng và gợi ý vuốt ngang trên điện thoại.
- Đổi phiên bản URL CSS theo thời gian cập nhật file để trình duyệt nhận CSS mới khi tải trang.

## 7. Chạy, dừng và tài khoản mẫu

### Ba file sử dụng thường xuyên

Các file nằm trong thư mục gốc dự án.

| File | Công dụng |
|---|---|
| `CHAY_JOBCONNECT.bat` | Chuẩn bị môi trường, chạy Laragon/MySQL và server web, mở trình duyệt |
| `DUNG_JOBCONNECT.bat` | Dừng riêng server web JobConnect |
| `KIEM_TRA_TAT_CA.bat` | Chạy toàn bộ bộ test và giữ cửa sổ để đọc kết quả |

Nút dừng giữ Laragon và MySQL chạy để các dự án khác vẫn dùng được. Nút chạy sử dụng lại server hợp lệ đang chạy, giữ dữ liệu đang có và chỉ thực hiện các bước chuẩn bị cần thiết. Database mới hoàn toàn mới được nạp bộ seed khởi tạo.

Nếu cổng 8000 bận, dùng địa chỉ chương trình thông báo và tự mở. Lần đầu có thể cần Internet để cài Composer dependencies.

### Tài khoản cơ bản

Mật khẩu mặc định của các tài khoản mẫu là **`password`**. Nếu đã đổi mật khẩu thì dùng mật khẩu mới.

| Vai trò | Email |
|---|---|
| Admin | `admin@jobconnect.vn` |
| Nhà tuyển dụng FPT | `fpt@jobconnect.vn` |
| Nhà tuyển dụng VNG | `vng@jobconnect.vn` |
| Nhà tuyển dụng MoMo | `momo@jobconnect.vn` |
| Nhà tuyển dụng Viettel | `viettel@jobconnect.vn` |
| Nhà tuyển dụng Shopee | `shopee@jobconnect.vn` |
| Ứng viên An | `ungvien@jobconnect.vn` |
| Ứng viên Ngọc | `bichngoc@jobconnect.vn` |
| Ứng viên Nam | `hoangnam@jobconnect.vn` |

Tài khoản mở rộng dùng email `hr01@demo.jobconnect.test` đến `hr20@demo.jobconnect.test`, và `ungvien01@demo.jobconnect.test` đến `ungvien50@demo.jobconnect.test`. Bộ mẫu có hai ứng viên cuối bị khóa để thử chức năng quản trị.

## 8. Bộ dữ liệu đã bổ sung

Đợt mở rộng đã thêm **20 công ty, 50 ứng viên, 120 tin và 50 CV PDF minh họa**, cùng dữ liệu ứng tuyển, phỏng vấn và lưu việc. Có tin đang tuyển, chờ duyệt, đã đóng, bị từ chối và hết hạn để kiểm tra các trường hợp khác nhau.

Số liệu ghi nhận sau khi nạp bộ mẫu ngày 24/10/2026:

| Dữ liệu | Số lượng |
|---|---:|
| Tổng tài khoản | 79 |
| Admin | 1 |
| Nhà tuyển dụng | 25 |
| Ứng viên | 53 |
| Công ty | 25 |
| Tổng tin tuyển dụng | 127 |
| Tin đang công khai | 103 |
| Tin chờ duyệt | 10 |
| CV | 52 |
| Hồ sơ ứng tuyển | 165 |
| Lịch phỏng vấn | 28 |
| Lượt lưu việc | 104 |
| Bản ghi quyền theo vai trò | 32 |

Đây là số liệu tại thời điểm kiểm tra. Số tin công khai sẽ giảm khi qua hạn; các số khác có thể thay đổi khi dùng và test web.

Các doanh nghiệp, hồ sơ và liên hệ bổ sung là dữ liệu giả lập. CV có tên, nội dung tiếng Việt và tệp PDF thật; không phải hồ sơ tìm việc thực tế.

### Các seeder liên quan

| Seeder | Mục đích |
|---|---|
| `DatabaseSeeder` | Bộ khởi tạo ban đầu; có gọi seed CV cơ bản và quyền mặc định |
| `DemoCvSeeder` | Bổ sung hai tệp CV cơ bản còn thiếu, giữ tệp đang tồn tại |
| `ExpandedDemoSeeder` | Bổ sung bộ dữ liệu lớn; chạy lại không nhân bản bộ mẫu hoặc ghi đè các chỉnh sửa đã kiểm tra |
| `RolePermissionSeeder` | Bổ sung quyền mặc định còn thiếu; giữ quyền đã được Admin thu hồi |

Dữ liệu cũ đã được đối chiếu trước/sau khi mở rộng; các bản ghi ban đầu giữ nguyên. Các test cũng kiểm tra giữ tên CV, tệp upload, mật khẩu, kỹ năng, trạng thái và thông tin đã chỉnh tay khi chạy lại seeder mở rộng.

Nếu cần nạp bộ mở rộng trên một bản cài khác, mở Terminal trong thư mục dự án rồi chạy:

```bat
php artisan migrate --force --no-interaction
php artisan db:seed --class=RolePermissionSeeder --force --no-interaction
php artisan db:seed --class=ExpandedDemoSeeder --force --no-interaction
```

Với database cũ vừa nhận phần code phân quyền, bước seed quyền giúp khởi tạo các quyền mặc định. Không chạy lại toàn bộ `DatabaseSeeder` trên database đã có người dùng: bộ khởi tạo ban đầu dùng các lệnh tạo mới.

Không dùng `migrate:fresh` để mở web hoặc sửa lỗi trên bản đang có dữ liệu; lệnh đó tạo lại các bảng và làm mất dữ liệu hiện tại.

## 9. Kiểm thử đã thực hiện

Kết quả lần chạy đầy đủ gần nhất ngày 24/10/2026:

**69 tests, 648 assertions, 0 errors, 0 failures.**

| File test | Phạm vi chính |
|---|---|
| `tests/Feature/JobConnectTest.php` | Tài khoản, tìm việc, CV, ứng tuyển, tin, phỏng vấn, Admin, lưu việc và quy trình đầu cuối |
| `tests/Feature/ExpandedDemoSeederTest.php` | Bộ dữ liệu lớn, liên kết CV, phân trang, chạy seed lại và bảo toàn chỉnh sửa |
| `tests/Feature/RolePermissionsTest.php` | Cấp/thu hồi quyền, URL trực tiếp, tạo/sửa lịch riêng, quyền Admin bắt buộc, quyền bị thiếu, điều hướng và tra người dùng |

Bộ tổng còn bao gồm các bài Example/Unit đang có trong dự án.

- `phpunit.xml` ép database test thành SQLite `:memory:`.
- Các bài test chức năng dùng vùng lưu trữ giả của Laravel cho CV.
- Test không sử dụng database MySQL `jobconnect` làm dữ liệu thử.
- Báo cáo JUnit nằm ở `storage/app/jobconnect-local/test-results.xml`.
- File `scratch/test_full_features.php` hiện chỉ gọi PHPUnit; phần đường dẫn cứng máy cũ và thao tác sửa MySQL đã được thay.

Có thể chạy bằng nút bat hoặc Terminal Laragon:

```bat
php vendor/bin/phpunit --configuration phpunit.xml --testdox
```

Ngoài PHPUnit, đã thử trên trình duyệt các phần: menu, sáng/tối, tự điền tài khoản, nút lưu việc, tải CV, lọc và chuyển trang, tra người dùng, chọn/lưu quyền và bố cục điện thoại. Test PHP không thay thế kiểm tra cảm nhận và thao tác giao diện.

### Quy trình demo tổng thể

1. FPT tạo tin mới → thấy **Chờ duyệt**.
2. Admin duyệt → tin xuất hiện công khai.
3. Ứng viên tìm tin, lưu việc và nộp CV.
4. FPT xem hồ sơ, chọn **Đã xem xét** và lên lịch phỏng vấn.
5. Ứng viên xem thời gian, hình thức và địa điểm/link trong lịch sử.
6. FPT chọn **Trúng tuyển** → ứng viên thấy kết quả mới.
7. Admin thử tắt/bật một quyền cập nhật và đối chiếu thao tác của vai trò đó.

## 10. Bản đồ những file cần biết

Các đường dẫn bên dưới tính từ thư mục gốc `jobconnect-main`.

| File hoặc thư mục | Vai trò |
|---|---|
| `routes/web.php` | Các route công khai và route ba vai trò |
| `bootstrap/app.php` | Đăng ký alias middleware `role`, `permission` |
| `app/Http/Middleware/RoleMiddleware.php` | Kiểm tra session, tài khoản khóa và vai trò |
| `app/Http/Middleware/EnsurePermission.php` | Kiểm tra quyền chức năng của từng request |
| `app/Models/RolePermission.php` | Đọc và xác định quyền có hiệu lực theo vai trò |
| `config/role_permissions.php` | Vai trò, menu, tài nguyên, thao tác, quyền bắt buộc và ánh xạ route |
| `app/Http/Controllers/AuthController.php` | Đăng ký, đăng nhập, đăng xuất và chọn trang được phép sau đăng nhập |
| `app/Http/Controllers/HomeController.php` | Nội dung và thống kê trang chủ |
| `app/Http/Controllers/JobController.php` | Tìm việc, chi tiết và nộp hồ sơ |
| `app/Http/Controllers/CandidateController.php` | Hồ sơ cá nhân, CV, lịch sử và rút hồ sơ |
| `app/Http/Controllers/SavedJobController.php` | Lưu/bỏ lưu việc trong database |
| `app/Http/Controllers/EmployerController.php` | Công ty, tin, ứng viên và lịch phỏng vấn |
| `app/Http/Controllers/AdminController.php` | Quản trị, duyệt tin, người dùng, ngành và phân quyền |
| `app/Providers/AppServiceProvider.php` | View composer cho lưu việc/quyền và cấu hình phân trang |
| `resources/views/layouts/app.blade.php` | Khung trang, menu và tab theo quyền |
| `resources/views/auth/login.blade.php` | Form đăng nhập và tài khoản trải nghiệm nhanh |
| `resources/views/admin/permissions.blade.php` | Trang chỉnh quyền và xem quyền theo người dùng |
| `resources/views/components/pagination.blade.php` | Phân trang riêng bằng tiếng Việt |
| `public/css/jobconnect.css` | CSS nền |
| `public/css/jobconnect-ui.css` | Các phần giao diện đã chỉnh và bổ sung |
| `public/js/jobconnect.js` | Các tương tác giao diện dùng chung |
| `database/migrations/` | Cấu trúc bảng, lưu việc, ứng tuyển lại và phân quyền |
| `database/seeders/fixtures/expanded_demo.json` | Danh sách công ty, nhóm nghề, kỹ năng và ứng viên mở rộng |
| `database/seeders/fixtures/expanded_cv_*.pdf` | 50 tệp CV minh họa để seeder sao chép |
| `storage/app/public/cvs/` | Các tệp CV của bản cài đang chạy |
| `public/storage` | Liên kết đến disk public để phục vụ tệp |
| `scratch/jobconnect-local.ps1` | Điều phối nút chạy/dừng/test |
| `scratch/setup_local.php` | Chuẩn bị database và migration khi khởi chạy local |
| `storage/logs/laravel.log` | Log lỗi ứng dụng |
| `storage/app/jobconnect-local/` | Trạng thái server, log chạy local và báo cáo test |

## 11. Database và kiến thức đang áp dụng

### Các bảng nghiệp vụ chính

- `users`: tài khoản, vai trò, tình trạng hoạt động và thông tin cá nhân.
- `companies`: hồ sơ công ty gắn với nhà tuyển dụng.
- `categories`, `locations`, `skills`: danh mục ngành, địa điểm và kỹ năng.
- `job_posts`: tin tuyển dụng, lương, hạn và trạng thái.
- `cvs`: bản ghi và đường dẫn CV.
- `applications`: hồ sơ nộp vào tin, CV sử dụng và trạng thái xử lý.
- `interviews`: lịch gắn với hồ sơ ứng tuyển.
- `user_skill`, `job_post_skill`: quan hệ kỹ năng của người và tin.
- `saved_jobs`: quan hệ ứng viên lưu việc.
- `role_permissions`: quyền thao tác theo vai trò.

Laravel còn có các bảng phục vụ hệ thống như migration, cache, session hoặc queue theo cấu hình/migration của dự án.

### Đối chiếu với kiến thức môn học

| Kiến thức | Thể hiện trong web |
|---|---|
| PHP và xử lý biểu mẫu | Đăng nhập, đăng ký, cập nhật, upload và xử lý request |
| MVC | Controller xử lý; Eloquent model làm việc với dữ liệu; Blade hiển thị |
| Route và middleware | Nhóm `candidate`, `employer`, `admin`; kiểm tra vai trò và quyền |
| Session | Duy trì đăng nhập và thông báo sau thao tác |
| Validation | Email, mật khẩu, loại/dung lượng CV, ngày phỏng vấn, lương, quyền gửi lên |
| CRUD | Các thao tác tạo/xem/sửa/xóa phù hợp với từng chức năng |
| SQL và quan hệ dữ liệu | Khóa ngoại, quan hệ một–nhiều, nhiều–nhiều, các bảng trung gian |
| Thống kê | `JOIN`, `COUNT`, `GROUP BY` trong phần Admin |
| Upload và lưu tệp | CV, Storage và liên kết `public/storage` |
| JavaScript và CSS | Menu, modal, sáng/tối, tự điền, lưu việc và bố cục thích ứng |
| Transaction và seed | Cấp quyền theo lô, bổ sung dữ liệu, giữ tính nhất quán |
| Kiểm thử | Gửi request qua Laravel, kiểm tra database, tệp và quyền |

Bảng này đối chiếu những khái niệm nhìn thấy trong code. Chưa khẳng định đã bao phủ toàn bộ bài lab/giáo trình, vì môn học vẫn đang được học tiếp.

## 12. Các tài liệu đã tạo

| Tài liệu | Nội dung và thời điểm |
|---|---|
| `HUONG_DAN_TEST.md` | Hướng dẫn chạy và checklist thử các chức năng; được soạn trước đợt thêm module phân quyền |
| `HDSD_JobConnect_De_Hieu.docx` | Bản Word 40 trang, 32 mục, 29 ảnh, hướng dẫn từng bước theo mẫu đã gửi; soạn ngày 22/10/2026 |
| `TONG_QUAN_VA_NHAT_KY_DU_AN.md` | Bản tổng hợp này, cập nhật cả dữ liệu mở rộng và phân quyền ngày 24/10/2026 |

Bản Word được tạo trước khi bổ sung trang Phân quyền và bộ dữ liệu lớn, nên chưa có hướng dẫn chi tiết hai phần mới đó. Khi số test trong tài liệu cũ khác với hiện tại, xem báo cáo JUnit và kết quả lần chạy mới nhất.

README đã được bổ sung thông tin chạy nhanh, nhưng vẫn còn một số đoạn cũ: đường dẫn ổ E của máy khác, phần khởi tạo lại database và mô tả cấu trúc/giao diện trước khi chỉnh. Với bản cài đang dùng, ưu tiên các file bat và hướng dẫn đã kiểm tra trong file này.

## 13. Những phần đã nâng cấp thêm và phạm vi hiện tại

- **Quên / đặt lại / đổi mật khẩu:** Đã hoàn thiện luồng `/forgot-password`, `/reset-password/{token}` (dùng bảng `password_reset_tokens`) và `/account/password` cho tài khoản đang đăng nhập. (Chưa bật gửi email SMTP ra ngoài; ở môi trường local/test hiển thị link đặt lại an toàn khi cấu hình dev/local.)
- **Trung tâm thông báo nội bộ (`user_notifications`):** Đã có chuông thông báo trên header và trang `/notifications`; tự động thông báo khi có ứng tuyển mới, cập nhật trạng thái/lịch phỏng vấn, và khi Admin duyệt hoặc từ chối tin. (Chưa gửi email/SMS ra ngoài.)
- **Xóa tin & sửa/xóa ngành:** Đã bổ sung thao tác xóa tin cho nhà tuyển dụng (`jobs.delete`), cập nhật và xóa ngành nghề rỗng cho Admin (`categories.update`, `categories.delete`), kèm trang thống kê biểu đồ SVG và ghi chú lý do từ chối tin (`rejection_note`).
- **Tải CV có kiểm soát quyền (`/cvs/{id}/download`):** Chỉ chủ sở hữu CV, Admin, hoặc nhà tuyển dụng có đơn ứng tuyển sử dụng CV đó mới tải được tệp.
- **Công ty công khai & xác thực doanh nghiệp:** Đã có danh bạ `/companies`, trang hồ sơ `/companies/{slug}` và nút Admin cấp/gỡ huy hiệu xác thực công ty (`is_verified`).
- **Động cơ gợi ý & chấm điểm phù hợp (`JobMatchingService`):** Tính điểm `%` phù hợp kỹ năng, kinh nghiệm và địa điểm trên trang tìm việc/chi tiết việc làm, bảng điều khiển ứng viên và danh sách ứng viên của nhà tuyển dụng; hỗ trợ xuất CSV danh sách ứng tuyển.
- **Phân quyền riêng từng tài khoản:** Vẫn cập nhật quyền theo vai trò; chưa có ghi đè quyền cá nhân hoặc đổi role trực tiếp trên giao diện.
- **Phạm vi triển khai:** Đã kiểm thử tự động trên bộ test PHPUnit SQLite và chuẩn bị script nâng cấp local; chưa triển khai website lên hosting công khai.

## 14. Nhật ký các đợt công việc

| Đợt | Nội dung đã hoàn thành |
|---|---|
| Kiểm tra ban đầu | Xác định nền tảng PHP/Laravel/Laragon, xem source và đối chiếu những phần đang áp dụng với tài liệu học |
| Chạy và test | Làm nút chạy/dừng/test; tách database test; thay file test cũ |
| Hoàn thiện chức năng | Sửa quy trình duyệt tin; lưu việc thật; CV mẫu có tệp; kiểm tra tài khoản, phân quyền sở hữu và quy trình ứng tuyển |
| Chỉnh giao diện | Làm lại đầu trang, trang chủ, tìm việc, menu, sáng/tối, điện thoại và nhóm tài khoản trải nghiệm |
| Hướng dẫn ngày 22/10 | Tạo checklist test và bản Word theo mẫu dễ hiểu |
| Mở rộng ngày 24/10 | Thêm bộ công ty/tin/ứng viên/CV/hoạt động; đối chiếu giữ dữ liệu cũ; sửa phân trang khi nhiều kết quả |
| Phân quyền ngày 24/10 | Thêm trang Admin xem/cập nhật quyền theo role, tra quyền người dùng, lưu database và kiểm tra ở server |
| Nâng cấp toàn diện (13/11) | Thêm `JobMatchingService`, Notification Center (`user_notifications`), Công ty `/companies` + Admin xác thực, Quên/Đặt lại/Đổi mật khẩu, `CvDownloadController` bảo mật, Xóa tin + Xuất CSV + Lý do từ chối tin + Sửa/Xóa ngành + Biểu đồ SVG + Lọc kinh nghiệm + `ActiveUserMiddleware` + Rà soát & sửa các điểm bất hợp lý (lọc `job_id` query, giao thoa khoảng lương, trạng thái hồ sơ đã rút/tin quá hạn, prefill lịch phỏng vấn & vai trò đăng ký NTD, đồng bộ trạng thái lưu việc trên trang công ty) |
| Kiểm tra hoàn tất gần nhất | **80 bài test, 761 assertions đều qua (100% pass)** trong bộ PHPUnit (`UpgradedFeaturesTest` + `RolePermissionsTest` + `ExpandedDemoSeederTest` + `JobConnectTest` + `ExampleTest`) |

## 15. Khi tiếp tục làm dự án

1. Bật web bằng file bat và kiểm tra đúng địa chỉ đang chạy.
2. Đọc file này, sau đó mở controller/view của chức năng sắp sửa.
3. Nếu sửa phân quyền, xem đồng thời `config/role_permissions.php`, `RolePermission`, `EnsurePermission` và `RolePermissionsTest`.
4. Nếu thêm route được bảo vệ, bổ sung ánh xạ quyền; xác định rõ phạm vi dữ liệu của người dùng/công ty.
5. Giữ dữ liệu đang sử dụng; thử thay đổi lớn trên database test hoặc bản sao phù hợp.
6. Chạy test liên quan và kiểm tra giao diện sau khi sửa.
7. Sau khi có chức năng mới, cập nhật tài liệu và kết quả kiểm thử để lần sau có ngữ cảnh đúng.

Không cần xem toàn bộ web là đã hoàn tất môn học. Đây là trạng thái đã làm đến thời điểm trên; có thể tiếp tục bổ sung theo yêu cầu bài học và góp ý của thầy.
