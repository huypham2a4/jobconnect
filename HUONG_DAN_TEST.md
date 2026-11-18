# Chạy và kiểm thử JobConnect

## Ba nút sử dụng hằng ngày

- Nhấp đúp `CHAY_JOBCONNECT.bat`: chuẩn bị database, bổ sung CV mẫu còn thiếu và mở web. Mặc định http://127.0.0.1:8000; nếu cổng bận, cửa sổ báo địa chỉ mới và trình duyệt mở đúng địa chỉ.
- Nhấp đúp `DUNG_JOBCONNECT.bat`: dừng riêng web JobConnect. Laragon và MySQL vẫn chạy để các bài lab khác sử dụng.
- Nhấp đúp `KIEM_TRA_TAT_CA.bat`: chạy toàn bộ PHPUnit test, hiển thị tên từng bài và dừng cửa sổ để bạn đọc kết quả. Khi thành công có dòng `TAT CA BAI TEST DA QUA`; nếu thất bại có tên bài cần kiểm tra.

Lần đầu mở web cần Internet nếu chưa cài Composer dependencies. Những lần sau giữ nguyên dữ liệu đã nhập, chỉ chạy migration mới. Không cần chạy npm cho giao diện này.

## Bộ test tự động hoạt động thế nào

Các bài test nằm ở `tests/Feature/JobConnectTest.php`. Chúng gửi request qua ứng dụng Laravel thật, tạo dữ liệu thử, kiểm tra phản hồi, trạng thái database và tệp CV.

Database test là **SQLite trong bộ nhớ**, được ép trong `phpunit.xml`; tệp upload được lưu trong disk giả của Laravel tại `storage/framework/testing`. Bộ test không sử dụng database MySQL `jobconnect` và không xóa CV của bạn.

Phạm vi đã có:

| Nhóm | Nội dung tự động kiểm tra |
|---|---|
| Giao diện phía máy chủ | Trang chủ, tìm kiếm, chi tiết tin, đăng nhập/đăng ký và toàn bộ trang quản lý của ba vai trò render thành công |
| Tìm việc | Từ khóa, ngành, địa điểm, hình thức, lương, sắp xếp, kết quả rỗng và giữ tham số khi phân trang |
| Tài khoản | Đăng ký ứng viên/doanh nghiệp; tự tạo công ty; hash mật khẩu; email trùng; xác nhận sai; chặn tự đăng ký Admin; đăng nhập ba vai trò và đăng xuất |
| Phân quyền | Khách phải đăng nhập; ứng viên không vào Admin/Employer; tài khoản khóa không thể đăng nhập hoặc tiếp tục nộp hồ sơ |
| Hồ sơ ứng viên | Lưu thông tin, thêm/bỏ kỹ năng và từ chối ID kỹ năng không hợp lệ |
| CV | Upload, đổi tên, mặc định, xóa, chọn CV mặc định thay thế; upload liên tiếp không ghi đè tệp; sai định dạng/quá 5 MB; chặn sửa/xóa/chọn CV của người khác |
| Bảo toàn hồ sơ | Không xóa CV đã dùng ứng tuyển; CV mẫu là PDF thật; bổ sung CV thiếu mà không ghi đè tệp đang có |
| Ứng tuyển | Chọn CV có sẵn hoặc upload mới; chặn CV của người khác; chặn nộp trùng trong 6 tháng; cho nộp lại sau 6 tháng; rút hồ sơ đúng trạng thái |
| Kiểm duyệt tin | Tin mới/sửa/mở lại phải chờ Admin; chỉ tin đã duyệt và còn hạn mới công khai; chủ tin/Admin được xem trước |
| Quản lý tin | Tạo/sửa, validation, đóng/mở lại, gia hạn hợp lệ; gia hạn không tự duyệt; không thao tác tin công ty khác |
| Xử lý ứng viên | Chuyển trạng thái, mở thông tin liên hệ, tạo/cập nhật lịch phỏng vấn, chặn lịch quá khứ và chặn xử lý hồ sơ đã rút |
| Admin | Dashboard thống kê, duyệt/từ chối tin, tìm người dùng, khóa/mở khóa, chặn tự khóa, thêm ngành và chặn tên trùng |
| Lưu việc | Lưu bền vững, lưu hai lần không trùng, danh sách cá nhân, bỏ lưu và xử lý tin đã ngừng tuyển |
| Quy trình đầu cuối | Employer tạo tin → Admin duyệt → Candidate lưu và nộp CV → Employer mời phỏng vấn → Candidate xem lịch → Employer chấp nhận |

Các test PHP không thay thế kiểm tra thao tác JavaScript và cảm nhận giao diện. Dùng checklist bên dưới để thử trực tiếp trên trình duyệt, đặc biệt menu điện thoại, nút lưu việc và mở CV.

File `scratch/test_full_features.php` đã được thay thành lệnh chạy cùng bộ PHPUnit này; không còn đường dẫn ổ E của máy cũ hay lệnh sửa database MySQL.

Nếu muốn chạy bằng terminal Laragon:

```bat
php vendor/bin/phpunit --configuration phpunit.xml --testdox
```

## Tài khoản mẫu

Mật khẩu chung: **password**.

| Vai trò | Email |
|---|---|
| Admin | admin@jobconnect.vn |
| Nhà tuyển dụng FPT | fpt@jobconnect.vn |
| Nhà tuyển dụng VNG để kiểm tra quyền khác công ty | vng@jobconnect.vn |
| Ứng viên An | ungvien@jobconnect.vn |
| Ứng viên Ngọc | bichngoc@jobconnect.vn |

Mở ba trình duyệt hoặc dùng cửa sổ ẩn danh để chuyển vai trò. Các cửa sổ ẩn danh của cùng một trình duyệt có thể dùng chung phiên; đăng xuất trước khi đổi tài khoản nếu cần. Tài khoản do bạn đổi mật khẩu thì dùng mật khẩu mới.

## Checklist thao tác trên giao diện

Ghi Đạt/Chưa đạt vào cột cuối. Đặt tên tin riêng, ví dụ “PHP Developer - Test 01”, để dễ tìm và phân biệt với dữ liệu mẫu.

| Mã | Thao tác | Kết quả mong đợi | Kết quả |
|---|---|---|---|
| UI01 | Mở trang chủ ở máy tính | Logo, menu và tài khoản trên một hàng; không ép chữ xuống nhiều dòng | |
| UI02 | Thu cửa sổ xuống khoảng 390 px hoặc mở bằng chế độ mobile DevTools | Không tràn ngang; ô tìm kiếm và thẻ việc làm xếp gọn | |
| UI03 | Bấm menu điện thoại, chọn mục, mở menu tài khoản và nhấn Escape | Các menu mở/đóng được, các trang cá nhân truy cập được | |
| UI04 | Đổi sáng/tối rồi tải lại | Giữ giao diện đã chọn, chữ và ô nhập đọc được | |
| AU01 | Đăng nhập sai mật khẩu | Hiện lỗi, chưa đăng nhập | |
| AU02 | Đăng ký ứng viên với email mới; thử email trùng và xác nhận mật khẩu sai | Hợp lệ vào dashboard; dữ liệu sai được báo lỗi | |
| AU03 | Đăng ký nhà tuyển dụng kèm tên công ty và thành phố | Tạo được tài khoản và hồ sơ công ty | |
| AU04 | Đăng xuất rồi vào trang quản lý | Chuyển về đăng nhập | |
| EM01 | Đăng nhập FPT, sửa thông tin công ty, mở lại trang | Dữ liệu đã lưu vẫn còn | |
| EM02 | Đăng tin với tiêu đề, ngành/địa điểm, lương, kỹ năng, hạn tương lai, mô tả trên 30 ký tự | Báo gửi duyệt; trạng thái Chờ duyệt, chưa có trong tìm kiếm công khai | |
| EM03 | Để trống tiêu đề, chọn hạn quá khứ hoặc lương tối đa nhỏ hơn tối thiểu | Form báo lỗi, không tạo tin sai | |
| AD01 | Admin mở Duyệt tin, lọc Chờ duyệt và tìm tin EM02 | Thấy đúng tin vừa tạo | |
| AD02 | Admin duyệt tin EM02, sau đó ứng viên tìm tiêu đề | Tin xuất hiện và mở chi tiết được | |
| EM04 | FPT sửa tin đã duyệt | Tin trở lại Chờ duyệt, tạm ẩn công khai; Admin duyệt lại để tiếp tục | |
| EM05 | FPT đóng tin rồi mở lại | Đóng thì ẩn; mở lại gửi Chờ duyệt, không tự công khai | |
| EM06 | Gia hạn 30 ngày | Hạn tăng; trạng thái chờ/từ chối không tự chuyển thành đã duyệt | |
| AD03 | Admin từ chối một tin chờ | Nhà tuyển dụng thấy Đã từ chối; ứng viên không thấy tin; sửa và gửi lại được | |
| CA01 | An sửa tên, số điện thoại, tiểu sử và kỹ năng | Mở lại vẫn có dữ liệu; bỏ hết kỹ năng rồi lưu thì danh sách trống | |
| CV01 | Mở Quản lý CV, bấm Xem CV mẫu | PDF thật được mở, không gặp 404 | |
| CV02 | Upload PDF/Word dưới 5 MB, đổi tên, đặt làm mặc định | Tệp xuất hiện, tên đổi đúng, có nhãn mặc định | |
| CV03 | Upload tệp .exe hoặc tệp trên 5 MB | Bị từ chối và có thông báo | |
| CV04 | Xóa CV vừa upload khi chưa dùng ứng tuyển | Mất khỏi danh sách; nếu là mặc định thì chọn lại CV còn lại | |
| SV01 | Tại trang chủ/tìm kiếm/chi tiết, bấm dấu lưu tin | Có thông báo đã lưu; biểu tượng đổi trạng thái | |
| SV02 | Tải lại trang, đăng xuất/đăng nhập, mở Việc đã lưu từ menu tài khoản | Tin vẫn còn trong danh sách của đúng tài khoản | |
| SV03 | Bỏ lưu trong danh sách | Tin được bỏ; tài khoản khác không bị thay đổi | |
| AP01 | Nộp vào tin EM02 đã được Admin duyệt, chọn CV có sẵn | Thấy hồ sơ ở Đã ứng tuyển; nộp lại bị chặn | |
| AP02 | Nộp vào tin khác bằng cách upload CV mới | Tạo hồ sơ và lưu CV mới vào tài khoản | |
| AP03 | Rút một hồ sơ còn Đã nộp | Trạng thái Đã rút; không rút hồ sơ đã được tiếp nhận | |
| EM07 | FPT xem ứng viên của tin EM02 trước khi đổi trạng thái | Email/điện thoại được che; mở CV được | |
| EM08 | Chuyển hồ sơ sang Đã xem | Thông tin liên hệ mở; ứng viên thấy trạng thái mới | |
| EM09 | Mời phỏng vấn online/offline với thời gian tương lai, cập nhật lịch lần nữa | Lưu được một lịch mới nhất; ứng viên thấy thời gian/link/địa điểm | |
| EM10 | Thử lịch quá khứ, thiếu địa điểm, hoặc xử lý hồ sơ đã rút | Báo lỗi, dữ liệu không bị đổi sai | |
| EM11 | Chuyển trạng thái Trúng tuyển hoặc Chưa phù hợp | Ứng viên thấy đúng kết quả sau khi tải lại | |
| AD04 | Admin vào người dùng, tìm/lọc, khóa An; An thử thao tác/đăng nhập; mở khóa lại | Khóa có hiệu lực, mở khóa đăng nhập lại được; không tự khóa Admin hiện tại | |
| AD05 | Admin thêm ngành mới rồi thử thêm trùng tên | Ngành mới có trong danh sách, tên trùng báo lỗi | |
| AD06 | Admin xem thống kê sau các lần tạo tin/nộp hồ sơ | Số liệu phản ánh dữ liệu vừa tạo | |
| SEC01 | Ứng viên gõ URL /admin/dashboard hoặc /employer/dashboard | HTTP 403 | |
| SEC02 | VNG thử URL sửa tin FPT hoặc xử lý ứng viên của FPT | HTTP 404; dữ liệu FPT giữ nguyên | |
| RUN01 | Bấm chạy lần hai, sau đó dừng/chạy lại | Không mở trùng server; dữ liệu test tay vẫn còn | |

## Các quy tắc cần nhớ

- Tin mới, nội dung sửa và yêu cầu mở lại cần Admin phê duyệt. Gia hạn chỉ thay ngày hết hạn.
- Chỉ nộp vào tin đã duyệt và chưa hết hạn. Một ứng viên không nộp lại cùng vị trí trong 6 tháng, kể cả đã rút.
- CV đã gắn với hồ sơ ứng tuyển được giữ lại để nhà tuyển dụng xem; muốn thử xóa thì tạo CV chưa sử dụng.
- CV mẫu ghi rõ dữ liệu minh họa. Bạn có thể upload hồ sơ của mình để thử.
- Các trường hợp cần chờ 6 tháng hoặc truy cập ID của người khác đã có test tự động; không cần sửa dữ liệu MySQL thật để thử.

## Khi gặp lỗi

Ghi lại mã checklist, tài khoản, thao tác, kết quả thực tế và ảnh chụp. Với lỗi server, xem `storage/logs/laravel.log`; lỗi khởi chạy xem `storage/app/jobconnect-local/web-error.log` hoặc `mysql-error.log`.

Nếu đã đổi cổng/tài khoản MySQL, chỉnh thông số DB trong `.env`. Không dùng `migrate:fresh` để sửa lỗi bản đang dùng vì nó xóa dữ liệu.
