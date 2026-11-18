# Kịch bản test web JobConnect

Soạn ngày 18/11/2026, sau đợt rà soát và sửa lỗi cùng ngày. Làm lần lượt từ phần 0 đến phần 9; mỗi bước có cột **Kết quả đúng** để đối chiếu. Bước nào ra khác thì ghi lại URL, tài khoản đang dùng và ảnh chụp màn hình.

## 0. Chuẩn bị

1. Bấm `CHAY_JOBCONNECT.bat`, đợi trình duyệt mở <http://127.0.0.1:8000> (nếu cổng 8000 bận thì dùng địa chỉ cửa sổ bat báo).
2. Nên mở **2 trình duyệt khác nhau** (hoặc 1 cửa sổ thường + 1 cửa sổ ẩn danh) để vừa đăng nhập NTD/Admin vừa đăng nhập Ứng viên, khỏi phải đăng xuất liên tục.
3. Mật khẩu mọi tài khoản mẫu: `password`.

| Vai trò | Email dùng trong kịch bản |
|---|---|
| Admin | `admin@jobconnect.vn` |
| NTD FPT | `fpt@jobconnect.vn` |
| NTD VNG (để thử truy cập chéo) | `vng@jobconnect.vn` |
| Ứng viên An | `ungvien@jobconnect.vn` |
| Ứng viên Ngọc | `bichngoc@jobconnect.vn` |

Chạy test tự động trước khi test tay: bấm `KIEM_TRA_TAT_CA.bat` → phải ra **92 tests, 0 failures**.

---

## 1. Khách chưa đăng nhập

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 1.1 | Mở trang chủ | Thấy thống kê, ngành nghề, việc làm nổi bật, công ty hàng đầu |
| 1.2 | Vào **Tìm việc**, gõ `PHP`, chọn 1 địa điểm, bấm tìm | Chỉ ra tin đang công khai có liên quan; URL giữ tham số tìm kiếm |
| 1.3 | Chuyển sang trang 2 của kết quả | Vẫn giữ từ khóa/bộ lọc |
| 1.4 | Lọc mức lương `15–25 triệu`, sắp xếp theo hạn nộp | Danh sách thay đổi, không lỗi |
| 1.5 | Tìm `khong-co-cong-viec-xyz` | Hiện thông báo không tìm thấy |
| 1.6 | Mở 1 tin bất kỳ, bấm **Ứng tuyển ngay** | Hiện hộp "Yêu cầu đăng nhập" |
| 1.7 | **(Mới sửa)** Trong hộp đó bấm **Đăng nhập ngay**, đăng nhập Ứng viên An | Sau khi đăng nhập **quay lại đúng tin vừa xem**, không bị đẩy về trang tổng quan |
| 1.8 | Đăng xuất. Ở trang tìm việc bấm icon lưu (bookmark) trên 1 thẻ việc | Chuyển tới trang đăng nhập; đăng nhập xong quay lại trang tìm việc |
| 1.9 | Vào **Công ty** (`/companies`), mở 1 công ty | Thấy hồ sơ công ty và các tin còn hạn của công ty đó |
| 1.10 | Gõ trực tiếp `http://127.0.0.1:8000/candidate/dashboard` | Bị đưa về trang đăng nhập |
| 1.11 | Bấm đổi giao diện sáng/tối rồi tải lại trang | Giữ đúng chế độ đã chọn |

## 2. Đăng ký, đăng nhập, mật khẩu

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 2.1 | Đăng ký ứng viên mới, nhập xác nhận mật khẩu sai | Báo "Xác nhận mật khẩu không trùng khớp" |
| 2.2 | Đăng ký với email `ungvien@jobconnect.vn` | Báo email đã được sử dụng |
| 2.3 | Đăng ký ứng viên mới hợp lệ (ví dụ `test1@demo.test`) | Đăng nhập luôn, vào trang của ứng viên |
| 2.4 | Đăng xuất, đăng ký **Nhà tuyển dụng** với tên công ty `Công ty Thử Nghiệm` | Vào kênh tuyển dụng, có hồ sơ công ty |
| 2.5 | **(Mới sửa)** Đăng ký thêm 1 NTD khác cũng đặt tên công ty `Công ty Thử Nghiệm` | Đăng ký thành công, không lỗi 500 (trước đây có thể trùng slug) |
| 2.6 | Đăng nhập sai mật khẩu | Báo "Email hoặc mật khẩu không chính xác" |
| 2.7 | **(Mới thêm)** Đăng nhập sai liên tục hơn 10 lần trong 1 phút | Bị chặn tạm thời (trang 429 Too Many Requests); chờ 1 phút là đăng nhập lại được |
| 2.8 | Vào **Quên mật khẩu**, nhập `ungvien@jobconnect.vn` | Hiện thông báo chung + link đặt lại (chỉ hiện ở môi trường local) |
| 2.9 | **(Mới sửa)** Nhập email không tồn tại, ví dụ `abc@xyz.test` | Hiện **đúng cùng câu thông báo** như 2.8 (không lộ email nào đã đăng ký), không có link |
| 2.10 | Mở link ở 2.8, đặt mật khẩu mới, đăng nhập bằng mật khẩu mới | Thành công. **Nhớ đổi lại về `password`** ở bước 2.11 |
| 2.11 | Menu tài khoản → **Đổi mật khẩu**: nhập sai mật khẩu hiện tại | Báo sai; nhập đúng thì đổi được |

## 3. Ứng viên (đăng nhập `ungvien@jobconnect.vn`)

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 3.1 | Vào **Tổng quan** | Thấy số hồ sơ đã nộp, lời mời PV, CV, việc gợi ý theo kỹ năng có % phù hợp |
| 3.2 | **Hồ sơ cá nhân**: sửa số điện thoại thành `0912345678`, thêm 1–2 kỹ năng, lưu | Báo thành công, tải lại vẫn giữ |
| 3.3 | **Quản lý CV**: tải lên 1 file `.pdf` | CV mới xuất hiện |
| 3.4 | Thử tải lên file `.png` hoặc file > 5 MB | Bị từ chối, thông báo tiếng Việt |
| 3.5 | Đổi tên CV, đặt CV khác làm mặc định | Nhãn "Mặc định" chuyển sang CV mới |
| 3.6 | Mở/tải 1 CV | PDF mở được trong trình duyệt, tên tệp hiển thị đúng |
| 3.7 | Xóa CV vừa tải lên (chưa dùng ứng tuyển) | Xóa được |
| 3.8 | Xóa CV đã dùng để ứng tuyển | Bị chặn, báo CV đang gắn với hồ sơ |
| 3.9 | Lưu 2 việc làm (bấm bookmark), vào **Việc đã lưu** | Thấy đúng 2 việc; bỏ lưu 1 việc thì mất khỏi danh sách |
| 3.10 | Đăng xuất rồi đăng nhập lại, vào **Việc đã lưu** | Danh sách vẫn còn |

## 4. Nhà tuyển dụng (đăng nhập `fpt@jobconnect.vn`)

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 4.1 | **Thông tin công ty**: nhập website `abc` | Báo website không hợp lệ (tiếng Việt) |
| 4.2 | Sửa thông tin công ty hợp lệ, lưu | Thành công; trang `/companies/...` hiện thông tin mới |
| 4.3 | **Tin tuyển dụng → Tạo tin**: để trống tiêu đề, mô tả < 30 ký tự | Báo lỗi tiếng Việt cho từng ô |
| 4.4 | Lương tối thiểu `30`, tối đa `10` | Báo "Lương tối đa phải lớn hơn hoặc bằng lương tối thiểu" |
| 4.5 | **(Mới sửa)** Để trống lương tối thiểu, chỉ nhập tối đa `30` | Tạo được tin; hiển thị lương "Lên đến 30 Triệu" (trước đây bị báo lỗi tiếng Anh) |
| 4.6 | Nhập lương `5000` (triệu) | Báo không quá 1000 |
| 4.7 | Tạo tin hợp lệ tên `Tin Demo Kịch Bản`, hạn nộp 30 ngày | Danh sách tin hiện **Chờ duyệt** |
| 4.8 | Bấm vào tiêu đề tin vừa tạo | Xem trước được, có nhãn "Bản xem trước · Chờ Admin duyệt" |
| 4.9 | (Trình duyệt khác, chưa đăng nhập) mở cùng URL tin đó | Lỗi 404 vì tin chưa công khai |
| 4.10 | **(Mới sửa)** Sửa tin, chọn hạn nộp là ngày hôm qua | Bị chặn: hạn nộp phải từ hôm nay trở đi |
| 4.11 | Tạo 2 tin cùng tiêu đề `Tin Trùng Tên`, xóa tin đầu, tạo lại tin thứ 3 cùng tiêu đề | **(Mới sửa)** Không lỗi 500 |
| 4.12 | Xóa tin `Tin Trùng Tên` còn lại (chưa có hồ sơ) | Xóa được |

## 5. Admin kiểm duyệt (đăng nhập `admin@jobconnect.vn`)

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 5.1 | **Tổng quan**: xem thống kê, biểu đồ theo ngành | Hiển thị số liệu, không lỗi |
| 5.2 | **Duyệt tin** → lọc **Chờ duyệt**, tìm `Tin Demo Kịch Bản` | Có nút **Duyệt** và **Từ chối** |
| 5.3 | Bấm **Từ chối**, nhập lý do `Bổ sung quyền lợi` | Tin chuyển "Đã từ chối", hiện lý do |
| 5.4 | (Tab FPT) Mở chuông thông báo | Có thông báo tin bị từ chối kèm lý do; trang Tin tuyển dụng hiện lý do |
| 5.5 | (FPT) Sửa tin, gửi lại | Tin về **Chờ duyệt**, lý do cũ bị xóa |
| 5.6 | (Admin) **Duyệt** tin | Tin công khai; FPT nhận thông báo "đã được duyệt" |
| 5.7 | (Khách) Tìm `Tin Demo Kịch Bản` ở trang tìm việc | Thấy tin |
| 5.8 | **(Mới sửa)** (FPT) **Đóng tin** vừa duyệt. (Admin) lọc **Đã đóng** | Tin đã đóng **không còn nút Duyệt/Từ chối**; Admin không tự mở lại tin NTD đã đóng |
| 5.9 | (FPT) Bấm **Mở lại** | Tin về Chờ duyệt; Admin duyệt lại thì công khai |
| 5.10 | **Ngành nghề**: thêm `Kế toán thử`, rồi thêm `Ke toan thu` | **(Mới sửa)** Cả 2 đều thêm được, không lỗi 500 |
| 5.11 | Xóa ngành đang có tin | Bị chặn; xóa ngành rỗng vừa tạo thì được |
| 5.12 | **Người dùng**: tìm `bichngoc`, bấm **Khóa** | Tài khoản bị khóa |
| 5.13 | Đăng nhập `bichngoc@jobconnect.vn` | Báo tài khoản bị khóa. Quay lại Admin **Mở khóa** |
| 5.14 | Tìm chính `admin@...` trong danh sách | Không có nút khóa chính mình |
| 5.15 | Bấm xác thực/gỡ xác thực 1 công ty | Huy hiệu xác thực đổi trên trang `/companies` |

## 6. Luồng tuyển dụng đầu cuối (quan trọng nhất)

Dùng `Tin Demo Kịch Bản` đang công khai ở phần 5.

| # | Ai | Thao tác | Kết quả đúng |
|---|---|---|---|
| 6.1 | Ứng viên An | Mở tin, xem % phù hợp kỹ năng, bấm **Ứng tuyển ngay**, chọn CV có sẵn, viết thư ngỏ, gửi | Báo nộp thành công; nút đổi thành "Bạn đã ứng tuyển vị trí này" |
| 6.2 | Ứng viên An | Gửi lại lần nữa (F5 trang, bấm lại nếu còn) | Không nộp trùng được (quy tắc 6 tháng) |
| 6.3 | FPT | Chuông thông báo | Có "Có hồ sơ ứng tuyển mới" |
| 6.4 | FPT | **Hồ sơ ứng viên**, lọc theo `Tin Demo Kịch Bản` | Thấy An, % phù hợp, thư ngỏ |
| 6.5 | FPT | Xem ô số điện thoại/email | **(Mới sửa)** Số điện thoại hiện dạng che theo số thật của ứng viên (vd `091*******`), không còn số giả `0987***`; email bị che |
| 6.6 | FPT | Mở CV | Mở được PDF |
| 6.7 | FPT | Xem ô chọn trạng thái | **(Mới sửa)** Chưa có lịch PV thì **không có** lựa chọn "Mời phỏng vấn" |
| 6.8 | FPT | Chọn **Đã xem xét** | Số điện thoại và email hiện đầy đủ |
| 6.9 | FPT | Xem lại ô trạng thái | **(Mới sửa)** Không còn lựa chọn "Chưa duyệt" (không quay lui được) |
| 6.10 | Ứng viên An | **Đã ứng tuyển** | Trạng thái "NTD đã xem"; không còn nút rút hồ sơ |
| 6.11 | FPT | **Lên lịch phỏng vấn**: chọn giờ ở quá khứ | Báo thời gian phải ở tương lai |
| 6.12 | FPT | Lên lịch hợp lệ, hình thức **Trực tuyến**, link `https://meet.google.com/abc-defg-hij` | Báo gửi lời mời thành công; trạng thái "Mời phỏng vấn"; khung lịch ghi "**Đã lên lịch**" (trước là chữ tiếng Anh) |
| 6.13 | Ứng viên An | **Đã ứng tuyển** + chuông | Thấy thời gian, hình thức, link bấm được; có thông báo lời mời |
| 6.14 | FPT | Bấm **Cập nhật lịch PV**, đổi giờ | Form điền sẵn lịch cũ; lưu xong vẫn chỉ 1 lịch |
| 6.15 | FPT | Chọn **Trúng tuyển** | Nút "Cập nhật lịch PV" **biến mất** (Mới sửa) |
| 6.16 | Ứng viên An | **Đã ứng tuyển** | Thấy "Đã trúng tuyển" + thông báo |
| 6.17 | FPT | **Xuất CSV** | Tải được file, mở bằng Excel không lỗi font tiếng Việt |
| 6.18 | FPT | Chọn lại **Trúng tuyển** lần nữa (cùng trạng thái) | **(Mới sửa)** Báo không thay đổi, ứng viên không nhận thêm thông báo trùng |

## 7. Rút hồ sơ

| # | Ai | Thao tác | Kết quả đúng |
|---|---|---|---|
| 7.1 | Ứng viên Ngọc | Ứng tuyển 1 tin của FPT bằng CV mới tải lên | Thành công |
| 7.2 | Ứng viên Ngọc | **Đã ứng tuyển** → **Rút hồ sơ** | Trạng thái "Đã rút hồ sơ" |
| 7.3 | FPT | Xem hồ sơ của Ngọc | Hiện nhãn "Đã rút hồ sơ", không có ô trạng thái/lịch PV; **(Mới sửa)** ô CV ghi "Ứng viên đã rút hồ sơ" thay vì link |
| 7.4 | FPT | Dán lại link CV cũ của Ngọc (nếu đã copy trước khi rút) | **(Mới sửa)** Lỗi 403, NTD không mở CV của hồ sơ đã rút |
| 7.5 | Ứng viên Ngọc | Mở lại tin đó | Không nộp lại được trong 6 tháng |

## 8. Gia hạn, đóng/mở, lượt xem

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 8.1 | (FPT) Bấm **Gia hạn** một tin đang tuyển (không dùng tin ở bước 8.3) | Hạn nộp cộng thêm 30 ngày, trạng thái không đổi |
| 8.2 | (FPT) Bấm xem tin của chính mình vài lần, ghi lại "lượt xem" ở trang Admin/Tổng quan | **(Mới sửa)** Lượt xem không tăng khi chính NTD hoặc Admin xem; khách/ứng viên xem thì tăng |
| 8.3 | (FPT) Tin "Senior Laravel / Backend Engineer" đang **Hết hạn** (hạn 29/10/2026 trong database hiện tại): bấm **Đóng tin**, rồi bấm **Mở lại** | **(Mới sửa)** Báo phải gia hạn trước khi mở lại |
| 8.4 | Gia hạn rồi bấm **Mở lại** | Tin về Chờ duyệt; Admin duyệt thì công khai |

## 9. Phân quyền và truy cập chéo

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 9.1 | (Admin) **Phân quyền** → Ứng viên → bỏ **Cập nhật** của **Hồ sơ cá nhân** → Lưu | Báo cập nhật quyền |
| 9.2 | (Ứng viên An) sửa hồ sơ cá nhân, lưu | Lỗi 403, dữ liệu không đổi |
| 9.3 | (Admin) bật lại quyền → (An) lưu lại | Lưu được. **Nhớ bật lại quyền sau khi test** |
| 9.4 | (Admin) Phân quyền → Quản trị viên: thử bỏ các ô Phân quyền | Ba quyền bắt buộc vẫn giữ, Admin không tự khóa mình khỏi trang |
| 9.5 | (Ứng viên) gõ `/admin/dashboard` hoặc `/employer/jobs` | Lỗi 403 |
| 9.6 | (VNG) mở `/employer/jobs/{id}/edit` với id tin của FPT | Lỗi 404 |
| 9.7 | (VNG) mở link CV của một ứng viên chỉ nộp vào FPT | Lỗi 403 |
| 9.8 | (Ứng viên Ngọc) mở link CV của An | Lỗi 403 |

## 10. Kiểm tra trên điện thoại

Nhấn F12 → biểu tượng điện thoại (Ctrl+Shift+M) → chọn rộng 390 px và 320 px. Lướt qua: trang chủ, tìm việc, chi tiết tin + hộp ứng tuyển, đăng nhập, Hồ sơ ứng viên của NTD, Phân quyền (bảng vuốt ngang được). Không có chữ tràn ra ngoài hoặc nút bị che.

## 11. Giao diện vừa chỉnh (đợt 2, 18/11)

| # | Thao tác | Kết quả đúng |
|---|---|---|
| 11.1 | Bấm nút mặt trăng chuyển giao diện tối → mở `/register` → chọn **Nhà tuyển dụng** | Hai thẻ vai trò và khung "Thông Tin Công Ty" đều nền tối, chữ đọc rõ, không còn ô trắng. Thẻ đang chọn viền xanh. |
| 11.2 | Bấm qua lại Ứng viên / Nhà tuyển dụng nhiều lần | Khung công ty ẩn/hiện đúng, thẻ được chọn đổi màu theo, không bị trắng lại |
| 11.3 | Ở form đăng ký, mở danh sách Tỉnh/Thành phố | Đủ 34 tỉnh thành (6 thành phố trực thuộc trung ương lên đầu, sau đó 28 tỉnh theo ABC) |
| 11.4 | Ở `/login`, `/register`, đổi mật khẩu, đặt lại mật khẩu: gõ mật khẩu rồi bấm hình con mắt | Mật khẩu hiện ra, icon đổi thành mắt gạch; bấm lần nữa thì ẩn lại |
| 11.5 | (Ứng viên) `/candidate/cvs` → chọn file `CV_Backend_2026.pdf` khi ô Tên hồ sơ đang trống | Ô tên tự điền `CV Backend 2026` (bỏ đuôi, đổi `_` thành khoảng trắng); sửa tên lại được |
| 11.6 | Tự gõ tên trước rồi mới chọn file | Tên tự gõ được giữ nguyên, không bị ghi đè |
| 11.7 | (FPT) Thông tin công ty → ô Tỉnh/Thành phố | Đủ 34 tỉnh; chọn "Quảng Ninh" rồi lưu → hiện lại đúng "Quảng Ninh" |
| 11.8 | (FPT) Tin tuyển dụng | Nhãn trạng thái (Chờ duyệt, Đang tuyển, Quá hạn, Đã đóng, Đã từ chối) nằm gọn trên 1 dòng; "Hình thức" ghi tiếng Việt (Toàn thời gian, Thực tập…) |
| 11.9 | (Admin) Ngành nghề → form Thêm danh mục | Có lưới 30 biểu tượng để bấm chọn thay cho ô gõ class. Chọn xe tải, thêm ngành "Logistics" → bảng hiện đúng biểu tượng xe tải |
| 11.10 | (Admin) bấm **Sửa** một ngành | Biểu tượng hiện tại của ngành đó đang được chọn sẵn; đổi biểu tượng khác rồi lưu → bảng cập nhật theo |

---

## Phụ lục: những gì đã sửa trong đợt 18/11/2026

| # | Vấn đề tìm thấy | Đã sửa thế nào | File chính |
|---|---|---|---|
| 1 | Tạo slug bằng cách đếm số slug "giống" → có thể sinh trùng, lỗi 500 (sau khi xóa tin, 2 công ty trùng tên, ngành "Kế toán"/"Ke toan") | Hàm `uniqueSlug()` dò đến khi không trùng; dùng cho tin, công ty, ngành | `Controller.php`, `EmployerController`, `AuthController`, `AdminController` |
| 2 | Chỉ nhập lương tối đa bị báo lỗi (tiếng Anh) dù hệ thống hỗ trợ "Lên đến X triệu"; không giới hạn lương | Chỉ so sánh khi có lương tối thiểu; giới hạn 1000 triệu | `EmployerController::validateJob` |
| 3 | Sửa tin được đặt hạn nộp ở quá khứ → tin chờ duyệt nhưng không bao giờ hiện | Hạn nộp khi sửa phải từ hôm nay | `EmployerController::validateJob` |
| 4 | Nhiều thông báo lỗi còn tiếng Anh | Thêm `lang/vi/validation.php`, đặt `APP_LOCALE=vi` | `lang/vi`, `.env` |
| 5 | Admin bấm Duyệt được cả tin NTD đã đóng (tự mở lại tin) | Chỉ duyệt tin chờ duyệt/đã từ chối; chỉ từ chối tin chờ duyệt/đang hiện; báo nếu tin đã quá hạn | `AdminController`, `admin/jobs/index` |
| 6 | NTD mở lại tin đã quá hạn → Admin duyệt xong vẫn không hiện | Bắt gia hạn trước khi mở lại | `EmployerController::toggleJobStatus` |
| 7 | NTD chuyển hồ sơ về "Chưa duyệt" được → ẩn lại liên hệ, ứng viên lại rút được | Chặn quay về trạng thái chưa duyệt | `EmployerController::updateApplicationStatus` |
| 8 | Chọn "Mời phỏng vấn" bằng ô trạng thái mà không có lịch | Bắt dùng nút Lên lịch phỏng vấn | như trên + view |
| 9 | Lên lịch PV được cho hồ sơ đã Trúng tuyển/Chưa phù hợp, làm trạng thái lùi về "Mời phỏng vấn" | Chặn, ẩn nút | `inviteInterview` + view |
| 10 | Cập nhật trùng trạng thái vẫn gửi thông báo cho ứng viên | Bỏ qua khi không đổi | `updateApplicationStatus` |
| 11 | Số điện thoại bị che luôn hiện số giả `0987***` cho mọi ứng viên | Che theo số thật | `employer/applications/index` |
| 12 | Trạng thái lịch PV hiện chữ tiếng Anh "Scheduled" | Đổi sang "Đã lên lịch / Đã phỏng vấn / Đã hủy" | như trên |
| 13 | NTD vẫn mở được CV của hồ sơ ứng viên đã rút | Chặn ở server, ẩn link | `CvDownloadController` + view |
| 14 | Tên tệp CV tiếng Việt trong header tải về có thể lỗi; tên bị lặp đuôi `_pdf.pdf` | Dùng header chuẩn có bản ASCII dự phòng, bỏ đuôi lặp | `CvDownloadController` |
| 15 | Khách bấm Đăng nhập ở trang tin → đăng nhập xong bị đưa về trang tổng quan, mất tin đang xem | Ghi nhớ trang tin/công ty và quay lại (chỉ nhận đường dẫn nội bộ `/jobs`, `/companies`) | `AuthController`, `jobs/show`, `save-job`, `layouts/app` |
| 16 | Quên mật khẩu báo khác nhau giữa email có/không tồn tại → dò được email | Một câu thông báo chung | `AuthController::sendResetLink` |
| 17 | Không giới hạn số lần thử đăng nhập/quên mật khẩu | Giới hạn 10 lần/phút (đăng nhập), 5 lần/phút (quên mật khẩu) | `routes/web.php` |
| 18 | Đặt lại mật khẩu không vô hiệu "ghi nhớ đăng nhập" cũ | Đổi `remember_token` khi đặt lại | `AuthController::resetPassword` |
| 19 | NTD/Admin xem trước tin cũng làm tăng lượt xem | Không tính lượt xem của chủ tin và Admin | `JobController::show` |
| 20 | Link phỏng vấn online in thẳng vào `href` → nhập `javascript:...` sẽ thành link chạy script | Chỉ tạo link khi bắt đầu bằng `http://` hoặc `https://` | `candidate/applications` |

Test tự động mới cho các điểm trên: `tests/Feature/ReviewFixesTest.php` (12 bài). Bản sao code trước khi sửa: `D:\Laragon_PHP\laragon\www\DA\backup_truoc_khi_sua_2026-11-18`.

### Những điểm thấy nhưng chưa sửa (có thể làm tiếp nếu thầy yêu cầu)

- Trang lỗi 403/404/429 vẫn là giao diện mặc định của Laravel (tiếng Anh).
- Đổi mật khẩu chưa đăng xuất các phiên đăng nhập ở máy khác.
- Từ khóa tìm kiếm có ký tự `%` hoặc `_` được hiểu như ký tự đại diện của SQL (không gây lỗi, chỉ ra kết quả rộng hơn).
- Danh bạ công ty vẫn hiện công ty của tài khoản NTD đã bị khóa.
