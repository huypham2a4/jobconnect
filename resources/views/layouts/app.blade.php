<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'JobConnect - Kết nối cơ hội nghề nghiệp')</title>
  <meta name="description" content="@yield('meta_description', 'Tìm việc phù hợp, lưu cơ hội và theo dõi hành trình ứng tuyển cùng JobConnect.')">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/jobconnect.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jobconnect-ui.css') }}?v={{ filemtime(public_path('css/jobconnect-ui.css')) }}">
  <script>try { if(localStorage.getItem('jobconnect_theme') === 'rebirth') document.documentElement.setAttribute('data-theme','rebirth'); } catch(e) {}</script>
  @yield('styles')
</head>
<body>
  @php
    $accountLinks = auth()->check() ? config('role_permissions.navigation.'.auth()->user()->role, []) : [];
    $routePermissions = config('role_permissions.routes');
    $canRoute = fn (string $name): bool => collect((array) ($routePermissions[$name] ?? []))->contains(fn (string $key): bool => $accessPermissions[$key] ?? false);
  @endphp
  <a class="skip-link" href="#main-content">Đến nội dung chính</a>
  <header class="header-nav">
    <div class="container nav-container">
      <a href="{{ route('home') }}" class="brand-logo" aria-label="JobConnect - Trang chủ"><span class="brand-icon"><i class="fa-solid fa-briefcase"></i></span><span>Job<span class="highlight">Connect</span><small>Kết nối cơ hội</small></span></a>
      <nav class="nav-links" aria-label="Điều hướng chính" id="main-navigation">
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
        <a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">Tìm việc làm</a>
        <a href="{{ route('home') }}#categories-section" class="nav-link">Ngành nghề</a>
        <a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">Công ty</a>
      </nav>
      <div class="nav-actions">
        <button type="button" class="icon-button" id="theme-switch-btn" aria-label="Chuyển giao diện sáng tối" title="Chuyển giao diện sáng tối"><i class="fa-regular fa-moon" id="theme-switch-icon" aria-hidden="true"></i></button>
        @auth
          <details class="notif-menu">
            <summary class="icon-button notif-bell" aria-label="Thông báo" title="Thông báo">
              <i class="fa-regular fa-bell" aria-hidden="true"></i>
              @if(($unreadNotificationsCount ?? 0) > 0)
                <span class="notif-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
              @endif
            </summary>
            <div class="notif-dropdown">
              <div class="notif-dropdown-head">
                <strong>Thông báo</strong>
                <a href="{{ route('notifications.index') }}">Xem tất cả</a>
              </div>
              @if(($recentNotifications ?? collect())->isEmpty())
                <p class="notif-empty">Chưa có thông báo mới.</p>
              @else
                <div class="notif-dropdown-list">
                  @foreach($recentNotifications as $notif)
                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="go" value="{{ $notif->link ? '1' : '0' }}">
                      <button type="submit" class="notif-dropdown-item {{ $notif->isRead() ? '' : 'is-unread' }}">
                        <i class="fa-solid {{ $notif->icon }}"></i>
                        <span>
                          <strong>{{ $notif->title }}</strong>
                          <small>{{ Str::limit($notif->message, 75) }}</small>
                        </span>
                      </button>
                    </form>
                  @endforeach
                </div>
              @endif
            </div>
          </details>
          @if(auth()->user()->isEmployer() && $canRoute('employer.jobs.create'))<a href="{{ route('employer.jobs.create') }}" class="btn-post-job header-cta"><i class="fa-solid fa-plus"></i> Đăng tin</a>@endif
          <details class="account-menu">
            <summary><span class="user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="account-name">{{ auth()->user()->name }}</span><i class="fa-solid fa-chevron-down account-chevron"></i></summary>
            <div class="account-dropdown">
              <p class="account-caption">{{ ['admin'=>'Quản trị viên','employer'=>'Nhà tuyển dụng','candidate'=>'Ứng viên'][auth()->user()->role] }}</p>
              @foreach($accountLinks as $name => $link)
                @if($canRoute($name))<a href="{{ route($name) }}"><i class="fa-solid {{ $link['icon'] }}"></i> {{ $link['label'] }}</a>@endif
              @endforeach
              <a href="{{ route('notifications.index') }}"><i class="fa-regular fa-bell"></i> Thông báo @if(($unreadNotificationsCount ?? 0) > 0)({{ $unreadNotificationsCount }})@endif</a>
              <a href="{{ route('account.password') }}"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a>
              <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</button></form>
            </div>
          </details>
        @else
          <a href="{{ route('login', request()->is('jobs*', 'companies*') ? ['redirect' => request()->getRequestUri()] : []) }}" class="header-login">Đăng nhập</a>
          <a href="{{ route('register', ['role'=>'employer']) }}" class="btn-post-job header-cta">Dành cho nhà tuyển dụng <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        @endauth
        <button type="button" class="mobile-toggle icon-button" aria-label="Mở menu" aria-controls="main-navigation" aria-expanded="false" id="btn-mobile-toggle"><i class="fa-solid fa-bars"></i></button>
      </div>
    </div>
  </header>
  @auth
    @if(request()->is('candidate/*','employer/*','admin/*'))
      <div class="workspace-bar"><nav class="container" aria-label="Chức năng tài khoản">
        @foreach($accountLinks as $name => $link)
          @if($canRoute($name))<a class="{{ request()->routeIs($name) ? 'active' : '' }}" href="{{ route($name) }}">{{ $link['short'] }}</a>@endif
        @endforeach
      </nav></div>
    @endif
  @endauth
  <main id="main-content">
    @if(session('success'))<div class="container flash-wrap"><x-alert type="success" :message="session('success')" /></div>@endif
    @if(session('error'))<div class="container flash-wrap"><x-alert type="error" :message="session('error')" /></div>@endif
    @if($errors->any())<div class="container flash-wrap"><div class="notice notice-error" role="alert"><strong>Vui lòng kiểm tra lại thông tin</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
    @yield('content')
  </main>
  <footer class="footer">
    <div class="container footer-main"><div><a href="{{ route('home') }}" class="footer-wordmark">Job<span>Connect</span></a><p>Kết nối đúng người. Mở ra cơ hội mới.</p><p class="footer-note">Đồ án Lập trình mã nguồn mở · Nhóm 9</p></div><div><h3>Dành cho ứng viên</h3><a href="{{ route('jobs.index') }}">Tìm kiếm việc làm</a><a href="{{ route('candidate.saved.index') }}">Việc làm đã lưu</a><a href="{{ route('candidate.cvs') }}">Hồ sơ và CV</a></div><div><h3>Dành cho doanh nghiệp</h3><a href="{{ route('employer.jobs.create') }}">Đăng tin tuyển dụng</a><a href="{{ route('employer.applications') }}">Quản lý ứng viên</a><a href="{{ route('employer.company') }}">Thông tin công ty</a></div></div>
    <div class="container footer-bottom"><span>© {{ date('Y') }} JobConnect</span><span>Ứng dụng PHP · Laravel · MySQL</span></div>
  </footer>
  <div id="app-toast" class="app-toast" role="status" aria-live="polite"></div>
  <script src="{{ asset('js/jobconnect.js') }}?v={{ filemtime(public_path('js/jobconnect.js')) }}"></script>
  @yield('scripts')
</body>
</html>
