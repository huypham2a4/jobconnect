@extends('layouts.app')

@section('title', 'Danh Sách Doanh Nghiệp - JobConnect')

@section('content')
<section class="search-page-header">
  <div class="container">
    <span class="eyebrow">KHÁM PHÁ MÔI TRƯỜNG LÀM VIỆC</span>
    <h1>Doanh nghiệp đang tuyển dụng.</h1>
    <p>Tìm hiểu văn hóa, quy mô và các vị trí đang mở tại những công ty hàng đầu trên JobConnect.</p>

    <form action="{{ route('companies.index') }}" method="GET" class="search-panel">
      <div class="hero-search">
        <label>
          <span>Tên doanh nghiệp hoặc lĩnh vực</span>
          <div><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="{{ request('q') }}" placeholder="FPT, VNG, Fintech..." aria-label="Tìm doanh nghiệp"></div>
        </label>
        <label>
          <span>Thành phố</span>
          <div>
            <i class="fa-solid fa-location-dot"></i>
            <select name="city" aria-label="Thành phố">
              <option value="">Tất cả thành phố</option>
              @foreach($cities as $city)
                <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
              @endforeach
            </select>
          </div>
        </label>
        <label style="border-right:0; display:flex; align-items:center; gap:8px; cursor:pointer; padding-left:10px;">
          <input type="checkbox" name="verified" value="1" @checked(request()->boolean('verified')) style="width:auto; accent-color:var(--primary);">
          <span style="margin:0; font-size:12px;">Chỉ công ty đã xác thực</span>
        </label>
        <button class="btn-search" type="submit">Lọc doanh nghiệp <i class="fa-solid fa-arrow-right"></i></button>
      </div>
    </form>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="results-heading">
      <h2><strong>{{ $companies->total() }}</strong> doanh nghiệp</h2>
      @if(request()->hasAny(['q', 'city', 'verified']))
        <a href="{{ route('companies.index') }}" style="font-size:12px; color:var(--text-muted);">Xóa bộ lọc <i class="fa-solid fa-xmark"></i></a>
      @endif
    </div>

    @if($companies->isEmpty())
      <div class="empty-state">
        <i class="fa-regular fa-building"></i>
        <h2>Không tìm thấy doanh nghiệp phù hợp</h2>
        <p>Hãy thử từ khóa khác hoặc bỏ bớt bộ lọc để xem toàn bộ doanh nghiệp.</p>
        <a href="{{ route('companies.index') }}" class="btn-search">Xem tất cả doanh nghiệp</a>
      </div>
    @else
      <div class="directory-companies-grid">
        @php $colors = ['#2563eb', '#0891b2', '#059669', '#7c3aed', '#ea580c', '#0f766e']; @endphp
        @foreach($companies as $idx => $company)
          @php
            $words = explode(' ', $company->name);
            $shortName = count($words) > 1
              ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1))
              : mb_strtoupper(mb_substr($company->name, 0, 2));
          @endphp
          <a href="{{ route('companies.show', $company->slug) }}" class="directory-company-card">
            <div class="directory-company-head">
              <div class="company-logo" style="background: {{ $colors[$idx % count($colors)] }}">{{ $shortName }}</div>
              <div style="min-width:0; flex:1;">
                <h3>
                  {{ $company->name }}
                  @if($company->is_verified)
                    <i class="fa-solid fa-circle-check verified-badge" title="Doanh nghiệp đã xác thực"></i>
                  @endif
                </h3>
                <p><i class="fa-solid fa-location-dot"></i> {{ $company->city ?? 'Toàn quốc' }} @if($company->size) · {{ $company->size }} @endif</p>
              </div>
            </div>
            <p class="directory-company-desc">{{ Str::limit($company->description ?: 'Doanh nghiệp đang đồng hành tuyển dụng nhân tài trên JobConnect.', 110) }}</p>
            <div class="directory-company-foot">
              <span class="company-jobs-pill">{{ $company->active_jobs_count }} vị trí đang tuyển</span>
              <span class="directory-company-link">Xem hồ sơ <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        @endforeach
      </div>

      <div class="pagination-wrap">{{ $companies->links() }}</div>
    @endif
  </div>
</section>
@endsection
