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

      @if(session('success'))
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem;">
          <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem;">
          <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i> {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem;">
          <ul style="list-style: none; margin: 0; padding: 0;">
            @foreach($errors->all() as $err)
              <li><i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> {{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

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
            <a href="#" style="font-size: 0.85rem; color: var(--primary); text-decoration: none;">Quên mật khẩu?</a>
          </div>
          <div style="position: relative;">
            <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light);"></i>
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

      <!-- Quick Demo Accounts -->
      <div style="margin-top: 28px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
        <p style="font-size: 0.82rem; color: var(--text-light); text-align: center; margin-bottom: 10px; font-weight: 600;">
          TÀI KHOẢN TRẢI NGHIỆM NHANH (Bấm để tự điền):
        </p>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          <button type="button" onclick="fillAccount('admin@jobconnect.vn', 'password')"
                  style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; font-size: 0.85rem; text-align: left; transition: var(--transition);"
                  onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span><i class="fa-solid fa-shield-halved" style="color: #ef4444; margin-right: 6px;"></i> <strong>Admin:</strong> admin@jobconnect.vn</span>
            <span style="color: var(--text-light); font-size: 0.78rem;">password</span>
          </button>

          <button type="button" onclick="fillAccount('fpt@jobconnect.vn', 'password')"
                  style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; font-size: 0.85rem; text-align: left; transition: var(--transition);"
                  onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span><i class="fa-solid fa-building" style="color: #2563eb; margin-right: 6px;"></i> <strong>Nhà tuyển dụng (FPT):</strong> fpt@jobconnect.vn</span>
            <span style="color: var(--text-light); font-size: 0.78rem;">password</span>
          </button>

          <button type="button" onclick="fillAccount('ungvien@jobconnect.vn', 'password')"
                  style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; font-size: 0.85rem; text-align: left; transition: var(--transition);"
                  onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span><i class="fa-solid fa-user-graduate" style="color: #10b981; margin-right: 6px;"></i> <strong>Ứng viên (Nguyễn Văn An):</strong> ungvien@jobconnect.vn</span>
            <span style="color: var(--text-light); font-size: 0.78rem;">password</span>
          </button>
        </div>
      </div>

      <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
        Chưa có tài khoản? 
        <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Đăng ký miễn phí</a>
      </div>

    </div>
  </div>
</section>

<script>
function fillAccount(email, password) {
  document.getElementById('email').value = email;
  document.getElementById('password').value = password;
}
</script>
@endsection
