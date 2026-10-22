@extends('layouts.app')

@section('title', 'JobConnect - Nền Tảng Tuyển Dụng & Việc Làm Thông Minh')

@section('content')
<section class="hero-section">
  <div class="container hero-content">
    <div class="hero-badge">
      <i class="fa-solid fa-bolt-lightning"></i>
      <span>Nền Tảng Tuyển Dụng Thông Minh Thế Hệ Mới</span>
    </div>

    <h1 class="hero-title">
      <span class="theme-default-content">
        Kết Nối Cơ Hội - <br>
        <span class="gradient-text">Vươn Tới Thành Công</span> Cùng Bạn
      </span>
      <span class="theme-rebirth-content">
        TÀN TRO KHÔNG PHẢI KẾT THÚC.<br>
        <span class="fire-keyword">BÙNG CHÁY</span> LẠI SỰ NGHIỆP CỦA BẠN
      </span>
    </h1>

    <p class="hero-subtitle">
      <span class="theme-default-content">
        Khám phá hơn <strong>12.000+</strong> cơ hội nghề nghiệp chất lượng cao từ các doanh nghiệp hàng đầu. Gợi ý việc làm thông minh dựa trên kỹ năng thực tế.
      </span>
      <span class="theme-rebirth-content">
        Từ tro tàn của sự trì trệ đến ngọn lửa rực sáng của thành công mới. Đánh thức năng lực tiềm ẩn của bạn ngay hôm nay.
      </span>
    </p>

    <!-- Master Search Bar -->
    <form action="{{ url('/jobs') }}" method="GET" class="search-box-wrapper" id="home-search-form">
      <div class="search-field">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Vị trí tuyển dụng, kỹ năng (Laravel, React, UI/UX...)" id="search-keyword">
      </div>

      <div class="search-field">
        <i class="fa-solid fa-layer-group"></i>
        <select name="category" id="search-category">
          <option value="">Tất cả ngành nghề</option>
          @foreach($categories as $cat)
            <option value="{{ $cat['slug'] }}">{{ $cat['name'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="search-field">
        <i class="fa-solid fa-location-dot"></i>
        <select name="location" id="search-location">
          <option value="">Tất cả địa điểm</option>
          @foreach($locations as $loc)
            <option value="{{ $loc->slug }}">{{ $loc->name }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn-search" id="btn-submit-search">
        <span>Tìm việc ngay</span>
        <i class="fa-solid fa-arrow-right"></i>
      </button>
    </form>

    <!-- Quick Tags -->
    <div class="quick-tags">
      <span><i class="fa-solid fa-fire" style="color: #ef4444; margin-right: 4px;"></i> Xu hướng tìm kiếm:</span>
      <a href="{{ url('/jobs?q=Laravel') }}" class="tag-pill">Laravel</a>
      <a href="{{ url('/jobs?q=ReactJS') }}" class="tag-pill">ReactJS</a>
      <a href="{{ url('/jobs?q=Flutter') }}" class="tag-pill">Flutter</a>
      <a href="{{ url('/jobs?q=UI%2FUX') }}" class="tag-pill">UI/UX Designer</a>
      <a href="{{ url('/jobs?q=Data+Analyst') }}" class="tag-pill">Data Analyst</a>
      <a href="{{ url('/jobs?q=Digital+Marketing') }}" class="tag-pill">Digital Marketing</a>
    </div>

    <!-- Hero Stats Cards -->
    <div class="hero-stats">
      <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
        <div class="stat-info">
          <div class="stat-number" data-target="{{ intval($stats['jobs_count']) }}" data-suffix="+">{{ $stats['jobs_count'] }}</div>
          <div class="stat-label">Việc làm đang tuyển dụng</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #ecfeff; color: #0891b2;"><i class="fa-solid fa-building"></i></div>
        <div class="stat-info">
          <div class="stat-number" data-target="{{ intval($stats['companies_count']) }}" data-suffix="+">{{ $stats['companies_count'] }}</div>
          <div class="stat-label">Doanh nghiệp đã xác thực</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-file-shield"></i></div>
        <div class="stat-info">
          <div class="stat-number" data-target="{{ intval($stats['resumes_count']) }}" data-suffix="+">{{ $stats['resumes_count'] }}</div>
          <div class="stat-label">Hồ sơ ứng viên tiềm năng</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     CATEGORIES SECTION
     ========================================================================== -->
<section class="section section-alt" id="categories-section">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="section-badge">DANH MỤC TIÊU BIỂU</span>
        <h2 class="section-title">Khám Phá Ngành Nghề Nổi Bật</h2>
        <p class="section-subtitle">Lựa chọn ngành nghề định hướng tương lai với hàng ngàn cơ hội tuyển dụng hấp dẫn</p>
      </div>
      <a href="#all-categories" class="section-link">
        <span>Xem tất cả danh mục</span>
        <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <div class="categories-grid">
      @foreach($categories as $cat)
        <a href="{{ url('/jobs?category=' . $cat['slug']) }}" class="category-card" id="cat-card-{{ $cat['id'] }}">
          <div class="category-icon-box {{ $cat['bg_class'] }}">
            <i class="{{ $cat['icon'] }}"></i>
          </div>
          <div class="category-info">
            <h4>{{ $cat['name'] }}</h4>
            <p>{{ $cat['jobs_count'] }} vị trí đang tuyển</p>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ==========================================================================
     FEATURED JOBS SECTION
     ========================================================================== -->
<section class="section" id="jobs-section">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="section-badge">CƠ HỘI NGHỀ NGHIỆP</span>
        <h2 class="section-title">Việc Làm Nổi Bật & Mới Nhất</h2>
        <p class="section-subtitle">Cập nhật liên tục tin tuyển dụng chất lượng cao, đãi ngộ tốt từ các công ty uy tín</p>
      </div>
      <a href="{{ url('/jobs') }}" class="section-link">
        <span>Xem tất cả việc làm</span>
        <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all">Tất cả việc làm</button>
      <button class="filter-tab" data-filter="it">CNTT / Phần mềm</button>
      <button class="filter-tab" data-filter="marketing">Marketing</button>
      <button class="filter-tab" data-filter="design">Thiết kế UI/UX</button>
      <button class="filter-tab" data-filter="finance">Tài chính - Kế toán</button>
    </div>

    <!-- Jobs Grid -->
    <div class="jobs-grid">
      @foreach($featuredJobs as $job)
        <div class="job-card" data-category="{{ $job['category'] }}" id="job-card-{{ $job['id'] }}">
          <div class="job-card-top">
            <div class="company-logo" style="background: {{ $job['color'] }}">
              {{ $job['company_short'] }}
            </div>
            <div class="job-title-group">
              <a href="{{ url('/jobs/' . $job['id']) }}">
                <h3 class="job-title">{{ $job['title'] }}</h3>
              </a>
              <div class="company-name">
                <span>{{ $job['company'] }}</span>
                <i class="fa-solid fa-circle-check verified-badge" title="Doanh nghiệp xác thực"></i>
              </div>
            </div>
            <button class="bookmark-btn" aria-label="Lưu công việc" title="Lưu tin này">
              <i class="fa-regular fa-bookmark"></i>
            </button>
          </div>

          <div class="job-meta-row">
            <div class="salary-badge">
              <i class="fa-solid fa-dong-sign"></i>
              <span>{{ $job['salary'] }}</span>
            </div>
            <div class="meta-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>{{ $job['location'] }}</span>
            </div>
            <div class="meta-item">
              <i class="fa-solid fa-briefcase"></i>
              <span>{{ $job['type'] }}</span>
            </div>
          </div>

          <div class="job-tags">
            @foreach($job['tags'] as $tag)
              <span class="job-tag">{{ $tag }}</span>
            @endforeach
          </div>

          <div class="job-card-bottom">
            <div class="deadline-pill">
              <span class="flame-dot-active"></span>
              <i class="fa-regular fa-clock"></i>
              <span>{{ $job['deadline'] }}</span>
            </div>
            <a href="{{ url('/jobs/' . $job['id']) }}" class="btn-apply-sm">
              Ứng tuyển ngay
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ==========================================================================
     WHY CHOOSE JOBCONNECT (FEATURES HIGHLIGHT)
     ========================================================================== -->
<section class="section section-alt" id="features-section">
  <div class="container">
    <div class="section-header" style="text-align: center; display: block;">
      <span class="section-badge">ƯU THẾ VƯỢT TRỘI</span>
      <h2 class="section-title">Tại Sao Nên Chọn Nền Tảng JobConnect?</h2>
      <p class="section-subtitle" style="max-width: 600px; margin: 8px auto 0;">
        Giải pháp tuyển dụng toàn diện giải quyết triệt để vấn đề bảo mật hồ sơ và khớp nối kỹ năng
      </p>
    </div>

    <div class="features-grid">
      <div class="feature-box">
        <div class="feature-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <h3 class="feature-title">Gợi Ý Khớp Nối Kỹ Năng</h3>
        <p class="feature-desc">
          Thuật toán đối sánh giữa bảng <code>user_skill</code> và <code>job_skill</code> giúp tự động xếp hạng công việc phù hợp với hồ sơ năng lực của bạn nhất.
        </p>
      </div>

      <div class="feature-box">
        <div class="feature-icon-wrapper" style="background: #ecfdf5; color: #059669;">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h3 class="feature-title">Bảo Mật SĐT & Email</h3>
        <p class="feature-desc">
          Thông tin liên hệ nhạy cảm của ứng viên luôn được ẩn an toàn cho tới khi nhà tuyển dụng xét duyệt hồ sơ và gửi lời mời phỏng vấn chính thức.
        </p>
      </div>

      <div class="feature-box">
        <div class="feature-icon-wrapper" style="background: #fffbeb; color: #d97706;">
          <i class="fa-solid fa-timeline"></i>
        </div>
        <h3 class="feature-title">Minh Bạch Tiến Độ</h3>
        <p class="feature-desc">
          Theo dõi sát sao từng bước: <em>Applied &rarr; Viewed &rarr; Interview &rarr; Offer</em>. Ứng viên cũng có thể linh hoạt rút hồ sơ (<em>Withdrawn</em>) khi cần.
        </p>
      </div>

      <div class="feature-box">
        <div class="feature-icon-wrapper" style="background: #f5f3ff; color: #7c3aed;">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <h3 class="feature-title">Lời Mời Phỏng Vấn Tiện Lợi</h3>
        <p class="feature-desc">
          Tích hợp lịch hẹn phỏng vấn trực tuyến hoặc trực tiếp tại công ty với thông tin thời gian, hình thức, địa điểm và ghi chú rõ ràng.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     TOP EMPLOYERS SECTION
     ========================================================================== -->
<section class="section" id="companies-section">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="section-badge">ĐỐI TÁC TIN CẬY</span>
        <h2 class="section-title">Nhà Tuyển Dụng Tiêu Biểu</h2>
        <p class="section-subtitle">Gia nhập đội ngũ tại các tập đoàn và công ty công nghệ hàng đầu Việt Nam</p>
      </div>
      <a href="#all-companies" class="section-link">
        <span>Khám phá công ty</span>
        <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <div class="companies-grid">
      @foreach($topCompanies as $comp)
        <div class="company-card">
          <div class="company-avatar" style="background: {{ $comp['color'] }}">
            {{ $comp['avatar'] }}
          </div>
          <h4 class="company-card-name">{{ $comp['name'] }}</h4>
          <p class="company-card-location"><i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> {{ $comp['location'] }}</p>
          <span class="company-open-jobs">{{ $comp['jobs_count'] }} vị trí tuyển dụng</span>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ==========================================================================
     EMPLOYER CTA BANNER
     ========================================================================== -->
<section class="container" style="margin-bottom: 80px;">
  <div class="cta-banner">
    <div class="cta-text">
      <div class="theme-default-content">
        <h3>Bạn Là Doanh Nghiệp Có Nhu Cầu Tuyển Dụng?</h3>
        <p>
          Tiếp cận mạng lưới hơn 500.000+ ứng viên chất lượng cao. Đăng tin tuyển dụng nhanh chóng và tìm kiếm nhân tài phù hợp nhất ngay hôm nay!
        </p>
      </div>
      <div class="theme-rebirth-content">
        <p class="cta-quote">“Mỗi lần bắt đầu lại đều cần một mồi lửa.”</p>
        <h3>BÙNG CHÁY CON ĐƯỜNG PHÍA TRƯỚC</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
          Đánh thức ngọn lửa đam mê, tìm kiếm bến đỗ sự nghiệp xứng tầm với năng lực thực chiến của bạn.
        </p>
      </div>
    </div>
    <div class="cta-buttons">
      <a href="{{ route('jobs.index') }}" class="btn-cta-white">
        <span class="theme-default-content"><i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Đăng tin miễn phí</span>
        <span class="theme-rebirth-content"><i class="fa-solid fa-fire" style="margin-right: 6px;"></i> Bắt đầu ngay</span>
      </a>
      <a href="{{ route('register') }}?role=employer" class="btn-cta-outline">
        <span class="theme-default-content">Tư vấn dịch vụ</span>
        <span class="theme-rebirth-content">Dành cho Nhà tuyển dụng</span>
      </a>
    </div>
  </div>
</section>
@endsection