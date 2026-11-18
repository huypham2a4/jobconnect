@extends('layouts.app')

@section('title', 'Lịch Sử Ứng Tuyển - JobConnect')

@section('content')
<section class="section">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Lịch Sử Ứng Tuyển Của Bạn
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Theo dõi tiến trình xét duyệt và lịch phỏng vấn từ các nhà tuyển dụng
        </p>
      </div>

      <a href="{{ route('jobs.index') }}" class="btn-search" style="padding: 10px 20px; font-size: 0.9rem;">
        <i class="fa-solid fa-magnifying-glass"></i> Tìm thêm việc làm
      </a>
    </div>

    @if($applications->isEmpty())
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 60px 20px; text-align: center;">
        <i class="fa-regular fa-folder-open" style="font-size: 3rem; color: var(--text-light); margin-bottom: 16px;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Bạn chưa nộp hồ sơ ứng tuyển nào</h3>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Hàng ngàn cơ hội việc làm hấp dẫn đang chờ đón bạn.</p>
        <a href="{{ route('jobs.index') }}" class="btn-search" style="display: inline-flex; padding: 10px 24px;">Tìm việc ngay</a>
      </div>
    @else
      <div style="display: flex; flex-direction: column; gap: 20px;">
        @foreach($applications as $app)
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
              <div>
                <a href="{{ route('jobs.show', $app->job_post_id) }}" style="text-decoration: none;">
                  <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-main)'">
                    {{ $app->jobPost->title }}
                  </h3>
                </a>
                <div style="display: flex; align-items: center; gap: 12px; font-size: 0.9rem; color: var(--text-muted); flex-wrap: wrap;">
                  <span><i class="fa-solid fa-building" style="margin-right: 4px;"></i> {{ $app->jobPost->company ? $app->jobPost->company->name : 'Doanh nghiệp' }}</span>
                  <span>•</span>
                  <span><i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> {{ $app->jobPost->location ? $app->jobPost->location->name : 'Toàn quốc' }}</span>
                  <span>•</span>
                  <span><i class="fa-regular fa-calendar-check" style="margin-right: 4px;"></i> Ngày nộp: {{ $app->applied_at ? $app->applied_at->format('d/m/Y H:i') : '' }}</span>
                  @if($app->cv)
                    <span>•</span>
                    <a href="{{ route('cvs.download', $app->cv->id) }}" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                      <i class="fa-regular fa-file-pdf" style="margin-right: 4px;"></i> {{ $app->cv->title }}
                    </a>
                  @endif
                </div>
              </div>

              <div style="display: flex; align-items: center; gap: 12px;">
                <span class="job-tag {{ $app->status_color }}" style="font-size: 0.88rem; font-weight: 600; padding: 6px 16px; border-radius: var(--radius-full);">
                  {{ $app->status_label }}
                </span>

                @if($app->canWithdraw())
                  <form action="{{ route('candidate.applications.withdraw', $app->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn rút hồ sơ ứng tuyển này? Hành động này không thể hoàn tác.')">
                    @csrf
                    <button type="submit" style="background: #fff1f2; border: 1px solid #fecaca; color: #e11d48; border-radius: var(--radius-sm); padding: 6px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: var(--transition);" onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'">
                      <i class="fa-solid fa-arrow-rotate-left"></i> Rút hồ sơ
                    </button>
                  </form>
                @endif
              </div>
            </div>

            <!-- Cover Letter Preview if provided -->
            @if($app->cover_letter)
              <div style="background: #f8fafc; border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 16px; font-size: 0.88rem; color: var(--text-muted); border-left: 3px solid var(--border-color);">
                <strong>Thư ngỏ của bạn:</strong> "{{ Str::limit($app->cover_letter, 180) }}"
              </div>
            @endif

            <!-- INTERVIEW INVITATION BOX -->
            @if($app->status === 'interview' && $app->interview)
              <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-md); padding: 20px; margin-top: 16px;">
                <h4 style="font-size: 1rem; font-weight: 700; color: #b45309; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                  <i class="fa-solid fa-bullhorn"></i> LỜI MỜI PHỎNG VẤN TỪ NHÀ TUYỂN DỤNG
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; font-size: 0.9rem; color: #78350f; margin-bottom: 12px;">
                  <div>
                    <strong><i class="fa-regular fa-calendar" style="margin-right: 6px;"></i> Thời gian:</strong>
                    {{ $app->interview->interview_date ? $app->interview->interview_date->format('H:i, d/m/Y') : 'Chưa xếp' }}
                  </div>

                  <div>
                    <strong><i class="fa-solid fa-video" style="margin-right: 6px;"></i> Hình thức:</strong>
                    {{ $app->interview->interview_type === 'online' ? 'Trực tuyến (Online)' : 'Trực tiếp tại văn phòng' }}
                  </div>
                </div>

                <div style="background: #ffffff; border: 1px solid #fef3c7; border-radius: var(--radius-sm); padding: 12px 16px; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
                  <strong>Địa điểm / Đường dẫn:</strong> 
                  @if($app->interview->interview_type === 'online' && preg_match('#^https?://#i', $app->interview->location_or_link))
                    <a href="{{ $app->interview->location_or_link }}" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; word-break: break-all; margin-left: 6px;">
                      {{ $app->interview->location_or_link }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                  @else
                    <span style="margin-left: 6px;">{{ $app->interview->location_or_link }}</span>
                  @endif
                </div>

                @if($app->interview->note)
                  <div style="font-size: 0.85rem; color: #92400e;">
                    <em>Ghi chú từ HR: {{ $app->interview->note }}</em>
                  </div>
                @endif
              </div>
            @endif

          </div>
        @endforeach

        <div style="display: flex; justify-content: center; margin-top: 20px;">
          {{ $applications->links() }}
        </div>
      </div>
    @endif

  </div>
</section>
@endsection
