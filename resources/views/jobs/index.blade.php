@extends('layouts.app')

@section('title', 'Tìm Kiếm Việc Làm - JobConnect')

@section('content')
<!-- Search & Filter Banner -->
<section style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 40px 0; color: #ffffff;">
  <div class="container">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
      <h1 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; color: #ffffff;">
        Tìm Kiếm Cơ Hội Nghề Nghiệp Mơ Ước
      </h1>
      <p style="color: #94a3b8; font-size: 1.05rem; margin-bottom: 28px;">
        Khám phá hàng ngàn công việc chất lượng từ các doanh nghiệp hàng đầu
      </p>

      <form action="{{ route('jobs.index') }}" method="GET" class="search-box-wrapper" style="box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div class="search-field">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Vị trí, kỹ năng (Laravel, React, UI/UX...)" id="search-q">
        </div>

        <div class="search-field">
          <i class="fa-solid fa-layer-group"></i>
          <select name="category" id="filter-cat">
            <option value="">Tất cả ngành nghề</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                {{ $cat->name }} ({{ $cat->job_posts_count }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="search-field">
          <i class="fa-solid fa-location-dot"></i>
          <select name="location" id="filter-loc">
            <option value="">Tất cả địa điểm</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->slug }}" {{ request('location') === $loc->slug ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn-search">
          <span>Tìm kiếm</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>
    </div>
  </div>
</section>

<!-- Main Jobs Content -->
<section class="section">
  <div class="container">
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 32px; align-items: start;">
      
      <!-- Filter Sidebar -->
      <aside style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-filter" style="color: var(--primary); margin-right: 6px;"></i> Bộ Lọc Tìm Kiếm
          </h3>
          <a href="{{ route('jobs.index') }}" style="font-size: 0.82rem; color: var(--primary); text-decoration: none;">Đặt lại</a>
        </div>

        <form action="{{ route('jobs.index') }}" method="GET" id="sidebar-filter-form">
          @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
          @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
          @if(request('location')) <input type="hidden" name="location" value="{{ request('location') }}"> @endif

          <!-- Hình thức làm việc -->
          <div style="margin-bottom: 24px;">
            <h4 style="font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 12px;">Hình thức công việc</h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              @php
                $types = [
                  '' => 'Tất cả hình thức',
                  'full-time' => 'Toàn thời gian (Full-time)',
                  'part-time' => 'Bán thời gian (Part-time)',
                  'remote' => 'Làm việc từ xa (Remote)',
                  'internship' => 'Thực tập sinh (Internship)',
                  'hybrid' => 'Linh hoạt (Hybrid)',
                ];
              @endphp
              @foreach($types as $val => $lbl)
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); cursor: pointer;">
                  <input type="radio" name="type" value="{{ $val }}" {{ request('type', '') === $val ? 'checked' : '' }} onchange="document.getElementById('sidebar-filter-form').submit()" style="accent-color: var(--primary);">
                  {{ $lbl }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Mức lương -->
          <div style="margin-bottom: 24px;">
            <h4 style="font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 12px;">Mức lương</h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              @php
                $ranges = [
                  '' => 'Tất cả mức lương',
                  'under_15' => 'Dưới 15 triệu',
                  '15_25' => '15 - 25 triệu',
                  '25_40' => '25 - 40 triệu',
                  'above_40' => 'Trên 40 triệu',
                  'negotiable' => 'Thỏa thuận',
                ];
              @endphp
              @foreach($ranges as $rVal => $rLbl)
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); cursor: pointer;">
                  <input type="radio" name="salary_range" value="{{ $rVal }}" {{ request('salary_range', '') === $rVal ? 'checked' : '' }} onchange="document.getElementById('sidebar-filter-form').submit()" style="accent-color: var(--primary);">
                  {{ $rLbl }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Kỹ năng nổi bật -->
          <div>
            <h4 style="font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 12px;">Kỹ năng phổ biến</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
              @foreach($skills->take(10) as $sk)
                <a href="{{ route('jobs.index', ['q' => $sk->name]) }}" class="job-tag" style="text-decoration: none;">
                  {{ $sk->name }}
                </a>
              @endforeach
            </div>
          </div>
        </form>
      </aside>

      <!-- Results Area -->
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <p style="font-size: 0.95rem; color: var(--text-muted); margin: 0;">
            Tìm thấy <strong style="color: var(--text-main);">{{ $jobs->total() }}</strong> việc làm phù hợp
          </p>

          <form action="{{ route('jobs.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px;">
            @foreach(request()->except('sort') as $k => $v)
              <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <label for="sort" style="font-size: 0.88rem; color: var(--text-muted);">Sắp xếp:</label>
            <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; background: var(--bg-card); outline: none;">
              <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
              <option value="salary_high" {{ request('sort') === 'salary_high' ? 'selected' : '' }}>Lương cao nhất</option>
              <option value="deadline" {{ request('sort') === 'deadline' ? 'selected' : '' }}>Sắp hết hạn</option>
            </select>
          </form>
        </div>

        @if($jobs->isEmpty())
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 60px 20px; text-align: center;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #f1f5f9; color: var(--text-light); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 16px;">
              <i class="fa-solid fa-folder-open"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Không tìm thấy việc làm phù hợp</h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 20px;">Vui lòng thử tìm kiếm với từ khóa khác hoặc điều chỉnh lại các tiêu chí bộ lọc.</p>
            <a href="{{ route('jobs.index') }}" class="btn-search" style="display: inline-flex; padding: 10px 24px;">
              Xem tất cả việc làm
            </a>
          </div>
        @else
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-bottom: 32px;">
            @foreach($jobs as $job)
              @php
                $compName = $job->company ? $job->company->name : 'Doanh nghiệp';
                $words = explode(' ', $compName);
                $shortName = count($words) > 1
                    ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1))
                    : mb_strtoupper(mb_substr($compName, 0, 2));
              @endphp
              <div class="job-card" id="job-card-{{ $job->id }}">
                <div class="job-card-top">
                  <div class="company-logo" style="background: #2563eb;">
                    {{ $shortName }}
                  </div>
                  <div class="job-title-group">
                    <a href="{{ route('jobs.show', $job->id) }}">
                      <h3 class="job-title">{{ $job->title }}</h3>
                    </a>
                    <div class="company-name">
                      <span>{{ $compName }}</span>
                      @if($job->company && $job->company->is_verified)
                        <i class="fa-solid fa-circle-check verified-badge" title="Doanh nghiệp xác thực"></i>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="job-meta-row">
                  <div class="salary-badge">
                    <i class="fa-solid fa-dong-sign"></i>
                    <span>{{ $job->salary_text }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $job->location ? $job->location->name : 'Toàn quốc' }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>{{ ucfirst($job->type) }}</span>
                  </div>
                </div>

                <div class="job-tags">
                  @foreach($job->skills as $sk)
                    <span class="job-tag">{{ $sk->name }}</span>
                  @endforeach
                </div>

                <div class="job-card-bottom">
                  <div class="deadline-pill">
                    <span class="{{ $job->is_expired ? 'flame-dot-expired' : 'flame-dot-active' }}"></span>
                    <i class="fa-regular fa-clock"></i>
                    <span>{{ $job->days_left_text }}</span>
                  </div>
                  <a href="{{ route('jobs.show', $job->id) }}" class="btn-apply-sm">
                    Xem chi tiết
                  </a>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Pagination Links -->
          <div style="display: flex; justify-content: center; margin-top: 32px;">
            {{ $jobs->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>
</section>
@endsection
