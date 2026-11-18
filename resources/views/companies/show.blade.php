@extends('layouts.app')

@section('title', $company->name . ' - Hồ sơ doanh nghiệp | JobConnect')

@section('content')
<section class="company-profile-hero">
  <div class="container">
    <div class="company-breadcrumb">
      <a href="{{ route('home') }}">Trang chủ</a> <span>/</span>
      <a href="{{ route('companies.index') }}">Doanh nghiệp</a> <span>/</span>
      <span>{{ $company->name }}</span>
    </div>

    <div class="company-hero-card">
      <div class="company-hero-left">
        @php
          $words = explode(' ', $company->name);
          $shortName = count($words) > 1
            ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1))
            : mb_strtoupper(mb_substr($company->name, 0, 2));
        @endphp
        <div class="company-hero-avatar">{{ $shortName }}</div>
        <div>
          <div class="company-title-line">
            <h1>{{ $company->name }}</h1>
            @if($company->is_verified)
              <span class="verified-pill"><i class="fa-solid fa-circle-check"></i> Đã xác thực</span>
            @endif
          </div>
          <div class="company-hero-meta">
            <span><i class="fa-solid fa-location-dot"></i> {{ $company->city ?? 'Toàn quốc' }}</span>
            @if($company->size)
              <span><i class="fa-solid fa-users"></i> {{ $company->size }}</span>
            @endif
            @if($company->address)
              <span><i class="fa-solid fa-map-pin"></i> {{ $company->address }}</span>
            @endif
            @if($company->website)
              <a href="{{ $company->website }}" target="_blank" rel="noopener"><i class="fa-solid fa-globe"></i> {{ $company->website }}</a>
            @endif
          </div>
        </div>
      </div>

      <div class="company-hero-stats">
        <div><strong>{{ $jobs->total() }}</strong><span>Việc đang tuyển</span></div>
        <div><strong>{{ number_format($totalViews) }}</strong><span>Lượt xem tin</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="company-about-card">
      <h2><i class="fa-solid fa-building"></i> Giới thiệu về {{ $company->name }}</h2>
      <p>{{ $company->description ?: 'Doanh nghiệp chưa cập nhật phần giới thiệu chi tiết.' }}</p>
    </div>

    <div class="results-heading" style="margin-top:32px;">
      <h2>Vị trí đang tuyển tại <strong>{{ $company->name }}</strong> ({{ $jobs->total() }})</h2>
    </div>

    @if($jobs->isEmpty())
      <div class="empty-state">
        <i class="fa-solid fa-briefcase"></i>
        <h2>Hiện chưa có tin tuyển dụng đang mở</h2>
        <p>Doanh nghiệp hiện chưa có vị trí nào đang công khai. Bạn có thể khám phá các cơ hội khác.</p>
        <a href="{{ route('jobs.index') }}" class="btn-search">Khám phá việc làm khác</a>
      </div>
    @else
      <div class="jobs-grid">
        @foreach($jobs as $job)
          <x-job-card :job="$job" :saved="in_array($job->id, $savedJobIds ?? [])" />
        @endforeach
      </div>
      <div class="pagination-wrap">{{ $jobs->links() }}</div>
    @endif
  </div>
</section>
@endsection
