@extends('layouts.app')

@section('title', 'Đăng Nhập - JobConnect')

@section('content')
<section class="section" style="min-height: calc(100vh - 350px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
  <div class="container" style="max-width: 480px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); padding: 36px 32px;">
      
      <div style="text-align: center; margin-bottom: 28px;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--primary-light); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Chào Mừng Trở Lại!</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Đăng nhập để kết nối việc làm và quản lý tuyển dụng</p>
      </div>

      

      

      

      <form action="{{ route('login.post') }}" method="POST" id="form-login">
        @csrf
        
        <div style="margin-bottom: 20px;">
          <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Địa chỉ Email
          </label>
          <div style="position: relative;">
            <i class="fa-regular fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light);"></i>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="ten@domain.com"
                   style="width: 100%; padding: 12px 16px 12px 42px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none; transition: var(--transition);"
                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label for="password" style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">
              Mật khẩu
            </label>
            <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--primary); text-decoration: none;">Quên mật khẩu?</a>
          </div>
          <div style="position: relative;">
            <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); z-index: 2;"></i>
            <input type="password" name="password" id="password" required placeholder="••••••••"
                   style="width: 100%; padding: 12px 16px 12px 42px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none; transition: var(--transition);"
                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
          </div>
        </div>

        <div style="display: flex; align-items: center; margin-bottom: 24px;">
          <input type="checkbox" name="remember" id="remember" style="margin-right: 8px; accent-color: var(--primary); cursor: pointer;">
          <label for="remember" style="font-size: 0.9rem; color: var(--text-muted); cursor: pointer;">Ghi nhớ đăng nhập</label>
        </div>

        <button type="submit" class="btn-search" style="width: 100%; justify-content: center; height: 48px; font-size: 1rem; border-radius: var(--radius-md);">
          <span>Đăng nhập ngay</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <section class="demo-accounts" aria-labelledby="demo-accounts-title">
        <h3 id="demo-accounts-title">Tài khoản trải nghiệm nhanh</h3>
        <p class="demo-accounts-hint">Chọn vai trò để tự điền, rồi bấm Đăng nhập ngay.</p>

        <div class="demo-accounts-list">
          <button type="button" class="demo-account demo-account--admin" aria-pressed="false"
                  onclick="fillAccount('admin@jobconnect.vn', 'password', this)">
            <span class="demo-account-icon" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
            <span class="demo-account-info">
              <strong>Quản trị viên <span class="demo-account-tag">Admin</span></strong>
              <span class="demo-account-email">admin@jobconnect.vn</span>
            </span>
            <i class="fa-solid fa-arrow-right demo-account-arrow" aria-hidden="true"></i>
          </button>

          <button type="button" class="demo-account demo-account--employer" aria-pressed="false"
                  onclick="fillAccount('fpt@jobconnect.vn', 'password', this)">
            <span class="demo-account-icon" aria-hidden="true"><i class="fa-solid fa-building"></i></span>
            <span class="demo-account-info">
              <strong>Nhà tuyển dụng <span class="demo-account-tag">FPT</span></strong>
              <span class="demo-account-email">fpt@jobconnect.vn</span>
            </span>
            <i class="fa-solid fa-arrow-right demo-account-arrow" aria-hidden="true"></i>
          </button>

          <button type="button" class="demo-account demo-account--candidate" aria-pressed="false"
                  aria-label="Dùng tài khoản ứng viên Nguyễn Văn An, ungvien@jobconnect.vn"
                  onclick="fillAccount('ungvien@jobconnect.vn', 'password', this)">
            <span class="demo-account-icon" aria-hidden="true"><i class="fa-solid fa-user-graduate"></i></span>
            <span class="demo-account-info">
              <strong>Ứng viên</strong>
              <span class="demo-account-email">ungvien@jobconnect.vn</span>
            </span>
            <i class="fa-solid fa-arrow-right demo-account-arrow" aria-hidden="true"></i>
          </button>
        </div>

        <p class="demo-accounts-password"><span><i class="fa-solid fa-key" aria-hidden="true"></i> Mật khẩu chung</span><code>password</code></p>
      </section>

      <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
        Chưa có tài khoản? 
        <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Đăng ký miễn phí</a>
      </div>

    </div>
  </div>
</section>

<script>
function fillAccount(email, password, selectedButton) {
  document.getElementById('email').value = email;
  document.getElementById('password').value = password;
  document.querySelectorAll('.demo-account').forEach(function (button) {
    button.setAttribute('aria-pressed', button === selectedButton ? 'true' : 'false');
  });
}
</script>
@endsection
