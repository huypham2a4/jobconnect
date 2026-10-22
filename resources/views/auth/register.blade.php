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

      @if($errors->any())
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem;">
          <ul style="list-style: none; margin: 0; padding: 0;">
            @foreach($errors->all() as $err)
              <li><i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> {{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('register.post') }}" method="POST" id="form-register">
        @csrf

        <!-- Chọn vai trò -->
        <div style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 10px;">
            Bạn tham gia với tư cách:
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; border: 2px solid var(--primary); background: var(--primary-light); border-radius: var(--radius-md); cursor: pointer; transition: var(--transition);" id="label-role-candidate">
              <input type="radio" name="role" value="candidate" {{ old('role', 'candidate') === 'candidate' ? 'checked' : '' }} onchange="toggleRoleFields()" style="accent-color: var(--primary);">
              <div>
                <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">Ứng viên</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tìm việc làm & ứng tuyển</span>
              </div>
            </label>

            <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; border: 1px solid var(--border-color); background: #f8fafc; border-radius: var(--radius-md); cursor: pointer; transition: var(--transition);" id="label-role-employer">
              <input type="radio" name="role" value="employer" {{ old('role') === 'employer' ? 'checked' : '' }} onchange="toggleRoleFields()" style="accent-color: var(--primary);">
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
        <div id="employer-fields" style="display: {{ old('role') === 'employer' ? 'block' : 'none' }}; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;">
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
                <option value="TP. Hồ Chí Minh" {{ old('city') === 'TP. Hồ Chí Minh' ? 'selected' : '' }}>TP. Hồ Chí Minh</option>
                <option value="Hà Nội" {{ old('city') === 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                <option value="Đà Nẵng" {{ old('city') === 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                <option value="Cần Thơ" {{ old('city') === 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                <option value="Khác" {{ old('city') === 'Khác' ? 'selected' : '' }}>Khác</option>
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
  const employerFields = document.getElementById('employer-fields');
  const labelCandidate = document.getElementById('label-role-candidate');
  const labelEmployer = document.getElementById('label-role-employer');

  if (isEmployer) {
    employerFields.style.display = 'block';
    labelEmployer.style.border = '2px solid var(--primary)';
    labelEmployer.style.background = 'var(--primary-light)';
    labelCandidate.style.border = '1px solid var(--border-color)';
    labelCandidate.style.background = '#f8fafc';
  } else {
    employerFields.style.display = 'none';
    labelCandidate.style.border = '2px solid var(--primary)';
    labelCandidate.style.background = 'var(--primary-light)';
    labelEmployer.style.border = '1px solid var(--border-color)';
    labelEmployer.style.background = '#f8fafc';
  }
}
</script>
@endsection
