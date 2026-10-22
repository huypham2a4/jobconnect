<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'JobConnect - Nền Tảng Tuyển Dụng & Việc Làm Thông Minh')</title>
  <meta name="description" content="@yield('meta_description', 'JobConnect - Nền tảng kết nối việc làm thông minh với hệ thống gợi ý kỹ năng, bảo mật hồ sơ.')">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,700;1,800&family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,400;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/jobconnect.css') }}">
  <link rel="stylesheet" href="{{ asset('css/theme-rebirth.css') }}">
  <script>
    (function() {
      try {
        const savedTheme = localStorage.getItem('jobconnect_theme');
        if (savedTheme === 'rebirth') {
          document.documentElement.setAttribute('data-theme', 'rebirth');
        }
      } catch (e) {}
    })();
  </script>
  @yield('styles')
</head>
<body>

  <header class="header-nav">
    <div class="container nav-container">
      <a href="{{ url('/') }}" class="brand-logo" id="nav-brand-logo">
        <div class="brand-icon">
          <i class="fa-solid fa-fire-flame-curved"></i>
        </div>
        <span>Job<span class="highlight">Connect</span></span>
      </a>

      <nav class="nav-links">
        <a href="{{ url('/') }}" class="nav-link active" id="nav-home">Trang chủ</a>
        <a href="{{ route('jobs.index') }}" class="nav-link" id="nav-jobs">Tìm việc làm</a>
        <a href="{{ url('/#categories-section') }}" class="nav-link" id="nav-categories">Ngành nghề</a>
        <a href="{{ url('/#companies-section') }}" class="nav-link" id="nav-companies">Công ty</a>
        <a href="{{ url('/#features-section') }}" class="nav-link" id="nav-features">Tính năng</a>
      </nav>

      <div class="nav-actions">
        <!-- Theme Toggle Switch -->
        <div class="theme-switch-container" id="theme-switch-btn" role="button" tabindex="0" title="Chuyển đổi giao diện: Chuẩn / Tái Sinh – Bùng Cháy">
          <div class="theme-toggle-switch">
            <div class="theme-toggle-knob">
              <i class="fa-solid fa-fire"></i>
            </div>
          </div>
          <span class="theme-switch-label" id="theme-switch-text">Chuẩn</span>
        </div>
        @auth
          @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn-login" style="background: #fef2f2; border-color: #fecaca; color: #b91c1c;">
              <i class="fa-solid fa-shield-halved"></i>
              <span>Trang Admin</span>
            </a>
          @elseif(Auth::user()->isEmployer())
            <a href="{{ route('employer.dashboard') }}" class="btn-login" style="background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8;">
              <i class="fa-solid fa-building-user"></i>
              <span>Quản Lý Tuyển Dụng</span>
            </a>
            <a href="{{ route('employer.jobs.create') }}" class="btn-post-job">
              <i class="fa-solid fa-plus-circle"></i>
              <span>Đăng tin</span>
            </a>
          @else
            <a href="{{ route('candidate.dashboard') }}" class="btn-login" style="background: #ecfdf5; border-color: #a7f3d0; color: #047857;">
              <i class="fa-solid fa-user-graduate"></i>
              <span>Hồ Sơ Của Tôi</span>
            </a>
          @endif

          <span style="font-size: 0.88rem; font-weight: 600; color: var(--text-main); display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f8fafc; border-radius: var(--radius-full); border: 1px solid var(--border-color);">
            <i class="fa-regular fa-circle-user" style="color: var(--primary);"></i>
            {{ Auth::user()->name }}
          </span>

          <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" title="Đăng xuất" class="btn-login" style="background: transparent; border-color: var(--border-color); color: var(--text-muted); padding: 8px 12px;">
              <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn-login" id="btn-login-header">
            <i class="fa-regular fa-user"></i>
            <span>Đăng nhập</span>
          </a>
          <a href="{{ route('register') }}" class="btn-login" id="btn-register-header" style="background: #f1f5f9; border-color: #e2e8f0;">
            <i class="fa-solid fa-user-plus"></i>
            <span>Đăng ký</span>
          </a>
          <a href="{{ route('register') }}?role=employer" class="btn-post-job" id="btn-post-job-header">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Đăng tin tuyển dụng</span>
          </a>
        @endauth
      </div>

      <button class="mobile-toggle" aria-label="Menu" id="btn-mobile-toggle">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </header>

  <main>
    @if(session('success'))
      <div class="container" style="margin-top: 16px;">
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: var(--radius-sm); font-size: 0.92rem; display: flex; align-items: center; justify-content: space-between;">
          <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}</span>
          <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #065f46;"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>
    @endif

    @if(session('error'))
      <div class="container" style="margin-top: 16px;">
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 18px; border-radius: var(--radius-sm); font-size: 0.92rem; display: flex; align-items: center; justify-content: space-between;">
          <span><i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> {{ session('error') }}</span>
          <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #991b1b;"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>
    @endif
    @yield('content')
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="{{ url('/') }}" class="brand-logo">
            <div class="brand-icon">
              <i class="fa-solid fa-briefcase"></i>
            </div>
            <span>Job<span class="highlight">Connect</span></span>
          </a>
          <p class="footer-desc">
            Hệ sinh thái kết nối ứng viên và nhà tuyển dụng thông minh. Tích hợp khớp nối kỹ năng (Skill-matching), quy trình phỏng vấn chuẩn và bảo mật hồ sơ chuyên nghiệp.
          </p>
          <div class="social-links">
            <a href="#" class="social-btn" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="social-btn" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" class="social-btn" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="social-btn" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>

        <div>
          <h4 class="footer-title">Dành cho Ứng viên</h4>
          <ul class="footer-links">
            <li><a href="#jobs-section">Tìm kiếm việc làm</a></li>
            <li><a href="#categories-section">Khám phá ngành nghề</a></li>
            <li><a href="#">Tạo hồ sơ năng lực (Skills)</a></li>
            <li><a href="#">Theo dõi trạng thái ứng tuyển</a></li>
            <li><a href="#">Cẩm nang phỏng vấn</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-title">Dành cho Nhà tuyển dụng</h4>
          <ul class="footer-links">
            <li><a href="#">Đăng tin tuyển dụng</a></li>
            <li><a href="#">Tìm kiếm hồ sơ nhân tài</a></li>
            <li><a href="#">Gửi lời mời phỏng vấn</a></li>
            <li><a href="#">Giải pháp quản lý ứng viên</a></li>
            <li><a href="#">Báo giá dịch vụ</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-title">Liên hệ & Hỗ trợ</h4>
          <ul class="footer-links">
            <li><i class="fa-solid fa-location-dot" style="margin-right: 8px; color: #38bdf8;"></i> TP. Hồ Chí Minh</li>
            <li><i class="fa-solid fa-phone" style="margin-right: 8px; color: #38bdf8;"></i> (028) 3899 8888</li>
            <li><i class="fa-solid fa-envelope" style="margin-right: 8px; color: #38bdf8;"></i> support@jobconnect.vn</li>
            <li><i class="fa-solid fa-clock" style="margin-right: 8px; color: #38bdf8;"></i> T2 - T6: 08:00 - 18:00</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} JobConnect. Dự án Lập Trình Mạng Nâng Cao - Nhóm 9. All rights reserved.</p>
        <p>Phát triển trên nền tảng Laravel 12 & MySQL.</p>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/jobconnect.js') }}"></script>
  @yield('scripts')
</body>
</html>