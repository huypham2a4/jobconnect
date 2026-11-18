@props(['job', 'saved' => false])
<article class="job-card" data-category="{{ $job->category?->slug }}">
  <div class="job-card-top">
    <div class="company-logo">{{ mb_strtoupper(mb_substr($job->company?->name ?? 'JC', 0, 2)) }}</div>
    <div class="job-title-group">
      <h3 class="job-title"><a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a></h3>
      <p class="company-name">
        @if($job->company?->slug)
          <a href="{{ route('companies.show', $job->company->slug) }}" class="company-inline-link">{{ $job->company->name }}</a>
        @else
          {{ $job->company?->name }}
        @endif
        @if($job->company?->is_verified)
          <i class="fa-solid fa-circle-check verified-badge" title="Doanh nghiệp đã xác thực"></i>
        @endif
      </p>
    </div>
    <x-save-job :job-id="$job->id" :saved="$saved" />
  </div>
  <div class="salary-badge"><i class="fa-solid fa-wallet"></i> {{ $job->salary_text }}</div>
  <div class="job-meta-row">
    <span class="meta-item"><i class="fa-solid fa-location-dot"></i>{{ $job->location?->name ?? 'Toàn quốc' }}</span>
    <span class="meta-item">{{ ['full-time'=>'Toàn thời gian', 'part-time'=>'Bán thời gian', 'remote'=>'Từ xa', 'internship'=>'Thực tập', 'hybrid'=>'Kết hợp'][$job->type] ?? $job->type }}</span>
  </div>
  <div class="job-tags">@foreach($job->skills->take(3) as $skill)<span class="job-tag">{{ $skill->name }}</span>@endforeach</div>
  <div class="job-card-bottom">
    <span class="deadline-pill"><i class="fa-regular fa-clock"></i>{{ $job->status !== 'approved' ? 'Tin đã ngừng tuyển' : $job->days_left_text }}</span>
    @if($job->status === 'approved' && !$job->is_expired)<a href="{{ route('jobs.show', $job->id) }}" class="btn-apply-sm">Chi tiết <i class="fa-solid fa-arrow-right"></i></a>@endif
  </div>
</article>
