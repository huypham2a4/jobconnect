@extends('layouts.app')

@section('title', 'Đăng Ký Tài Khoản - JobConnect')

@section('content')
<section class="section" style="min-height: calc(100vh - 350px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
  <div class="container" style="max-width: 580px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); padding: 36px 32px;">
      
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--primary-light); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Tạo Tài Khoản Mới</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Tham gia nền tảng tuyển dụng thông minh thế hệ mới</p>
      </div>

      

      @php
        $initialRole = old('role', request('role') === 'employer' ? 'employer' : 'candidate');
      @endphp

      <form action="{{ route('register.post') }}" method="POST" id="form-register">
        @csrf

        <!-- Chọn vai trò -->
        <div style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 10px;">
            Bạn tham gia với tư cách:
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <label class="role-option" id="label-role-candidate">
              <input type="radio" name="role" value="candidate" {{ $initialRole === 'candidate' ? 'checked' : '' }} onchange="toggleRoleFields()">
              <div>
                <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">Ứng viên</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tìm việc làm & ứng tuyển</span>
              </div>
            </label>

            <label class="role-option" id="label-role-employer">
              <input type="radio" name="role" value="employer" {{ $initialRole === 'employer' ? 'checked' : '' }} onchange="toggleRoleFields()">
              <div>
                <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">Nhà tuyển dụng</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Đăng tin & tuyển nhân tài</span>
              </div>
            </label>
          </div>
        </div>

        <!-- Thông tin cơ bản -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label for="name" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Họ và tên <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn A"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>

          <div>
            <label for="phone" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Số điện thoại
            </label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="0901234567"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>
        </div>

        <div style="margin-bottom: 16px;">
          <label for="email" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
            Địa chỉ Email <span style="color: #ef4444;">*</span>
          </label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@domain.com"
                 style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
        </div>

        <!-- Khu vực thông tin công ty (chỉ hiện khi chọn Nhà tuyển dụng) -->
        <div id="employer-fields" class="register-company-box" @if($initialRole !== 'employer') hidden @endif>
          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary); margin-bottom: 12px;">
            <i class="fa-solid fa-building" style="margin-right: 6px;"></i> Thông Tin Công Ty / Doanh Nghiệp
          </h4>

          <div style="margin-bottom: 12px;">
            <label for="company_name" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Tên công ty <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" placeholder="Ví dụ: Công ty TNHH Công Nghệ ABC"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label for="city" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
                Tỉnh / Thành phố <span style="color: #ef4444;">*</span>
              </label>
              <select name="city" id="city" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
                @foreach(config('provinces') as $province)
                  <option value="{{ $province }}" @selected(old('city', 'TP. Hồ Chí Minh') === $province)>{{ $province }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="address" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
                Địa chỉ chi tiết
              </label>
              <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Số nhà, tên đường, quận"
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
          <div>
            <label for="password" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Mật khẩu <span style="color: #ef4444;">*</span>
            </label>
            <input type="password" name="password" id="password" required placeholder="Tối thiểu 6 ký tự"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>

          <div>
            <label for="password_confirmation" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Xác nhận mật khẩu <span style="color: #ef4444;">*</span>
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Nhập lại mật khẩu"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>
        </div>

        <button type="submit" class="btn-search" style="width: 100%; justify-content: center; height: 48px; font-size: 1rem; border-radius: var(--radius-md);">
          <span>Đăng ký tài khoản</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
        Đã có tài khoản? 
        <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Đăng nhập ngay</a>
      </div>

    </div>
  </div>
</section>

<script>
function toggleRoleFields() {
  const isEmployer = document.querySelector('input[name="role"]:checked').value === 'employer';
  document.getElementById('employer-fields').hidden = !isEmployer;
}
</script>
@endsection
