@extends('layouts.app')

@section('title', 'Kênh Tuyển Dụng - ' . $company->name)

@section('content')
<section class="section">
  <div class="container">
    
    <!-- Employer Header -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div style="display: flex; align-items: center; gap: 18px;">
        <div style="width: 64px; height: 64px; border-radius: var(--radius-md); background: #2563eb; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800;">
          {{ mb_substr($company->name, 0, 2) }}
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h2 style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin: 0;">
              {{ $company->name }}
            </h2>
            @if($company->is_verified)
              <i class="fa-solid fa-circle-check" style="color: #0284c7;" title="Doanh nghiệp đã xác thực"></i>
            @endif
          </div>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin: 4px 0 0;">
            <i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> {{ $company->city }} • {{ $company->size ?? 'Quy mô chưa cập nhật' }}
          </p>
        </div>
      </div>

      <div style="display: flex; gap: 10px;">
        <a href="{{ route('employer.company') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
          <i class="fa-solid fa-building"></i> Hồ sơ doanh nghiệp
        </a>
        <a href="{{ route('employer.jobs.create') }}" class="btn-search" style="padding: 10px 20px; font-size: 0.9rem;">
          <i class="fa-solid fa-plus-circle"></i> Đăng tin mới
        </a>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px;">
      <div class="employer-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-briefcase"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Tổng tin đã đăng</span>
          <strong style="font-size: 1.6rem; color: var(--text-main);">{{ $stats['total_jobs'] }}</strong>
        </div>
      </div>

      <div class="employer-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Tin đang tuyển dụng</span>
          <strong style="font-size: 1.6rem; color: #059669;">{{ $stats['active_jobs'] }}</strong>
        </div>
      </div>

      <div class="employer-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fdf2f8; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-users"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Hồ sơ ứng tuyển</span>
          <strong style="font-size: 1.6rem; color: #db2777;">{{ $stats['total_applications'] }}</strong>
        </div>
      </div>

      <div class="employer-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Phỏng vấn đã hẹn</span>
          <strong style="font-size: 1.6rem; color: #d97706;">{{ $stats['interviews_scheduled'] }}</strong>
        </div>
      </div>
    </div>

    <!-- 2 Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
      
      <!-- Recent Job Posts -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-list-check" style="color: var(--primary); margin-right: 8px;"></i> Tin Tuyển Dụng Gần Đây
          </h3>
          <a href="{{ route('employer.jobs.index') }}" style="font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Xem tất cả
          </a>
        </div>

        @if($recentJobs->isEmpty())
          <div style="text-align: center; padding: 30px; color: var(--text-muted);">
            <p>Doanh nghiệp chưa đăng tin tuyển dụng nào.</p>
            <a href="{{ route('employer.jobs.create') }}" class="btn-search" style="display: inline-flex; padding: 8px 18px; font-size: 0.88rem;">
              Đăng tin đầu tiên
            </a>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($recentJobs as $job)
              <div style="padding: 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <a href="{{ route('jobs.show', $job->id) }}" style="font-weight: 600; font-size: 0.95rem; color: var(--text-main); text-decoration: none;">
                    {{ $job->title }}
                  </a>
                  <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 2px;">
                    Hạn: {{ $job->days_left_text }} • <strong>{{ $job->applications_count }}</strong> hồ sơ
                  </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                  <a href="{{ route('employer.applications', ['job_id' => $job->id]) }}" class="btn-login" style="padding: 4px 10px; font-size: 0.8rem; background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8;">
                    Xem hồ sơ
                  </a>
                  <a href="{{ route('employer.jobs.edit', $job->id) }}" style="color: var(--text-muted); font-size: 0.9rem; padding: 4px 8px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- Recent Applications -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-user-check" style="color: #059669; margin-right: 8px;"></i> Ứng Viên Mới Nộp
          </h3>
          <a href="{{ route('employer.applications') }}" style="font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Quản lý hồ sơ
          </a>
        </div>

        @if($recentApplications->isEmpty())
          <div style="text-align: center; padding: 30px; color: var(--text-muted);">
            <p>Chưa có ứng viên nào nộp hồ sơ.</p>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($recentApplications as $app)
              <div style="padding: 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <strong style="font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center;">
                    @if($app->status === 'submitted')
                      <span class="new-applicant-spark active" title="Hồ sơ mới nộp (chưa xem)"></span>
                    @else
                      <span class="new-applicant-spark viewed" title="Hồ sơ đã xem"></span>
                    @endif
                    {{ $app->user->name }}
                  </strong>
                  <span style="font-size: 0.82rem; color: var(--text-muted);">
                    {{ $app->jobPost->title }} • {{ $app->applied_at ? $app->applied_at->diffForHumans() : '' }}
                  </span>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                  <span class="job-tag {{ $app->status_color }}" style="font-size: 0.78rem; padding: 4px 10px;">
                    {{ $app->status_label }}
                  </span>
                  <a href="{{ route('employer.applications', ['job_id' => $app->job_post_id]) }}" class="btn-login" style="padding: 4px 10px; font-size: 0.8rem; background: #f8fafc; border: 1px solid var(--border-color);">
                    Xử lý
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>

  </div>
</section>
@endsection
