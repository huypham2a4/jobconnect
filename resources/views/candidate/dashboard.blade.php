@extends('layouts.app')

@section('title', 'Bảng Điều Khiển Ứng Viên - JobConnect')

@section('content')
<section class="section">
  <div class="container">
    
    <!-- User Welcome Header -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div style="display: flex; align-items: center; gap: 18px;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700;">
          {{ mb_substr($user->name, 0, 1) }}
        </div>
        <div>
          <h2 style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
            Xin chào, {{ $user->name }}!
          </h2>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
            <i class="fa-regular fa-envelope" style="margin-right: 4px;"></i> {{ $user->email }}
            @if($user->phone) • <i class="fa-solid fa-phone" style="margin-right: 4px;"></i> {{ $user->phone }} @endif
          </p>
        </div>
      </div>

      <div style="display: flex; gap: 10px;">
        <a href="{{ route('candidate.profile') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
          <i class="fa-solid fa-user-pen"></i> Chỉnh sửa hồ sơ & Kỹ năng
        </a>
        <a href="{{ route('candidate.cvs') }}" class="btn-search" style="padding: 10px 20px; font-size: 0.9rem;">
          <i class="fa-solid fa-file-arrow-up"></i> Quản lý CV
        </a>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px;">
      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-paper-plane"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Hồ sơ đã nộp</span>
          <strong style="font-size: 1.4rem; color: var(--text-main);">{{ $stats['total_applications'] }}</strong>
        </div>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Lời mời phỏng vấn</span>
          <strong style="font-size: 1.4rem; color: #d97706;">{{ $stats['interview_invites'] }}</strong>
        </div>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-trophy"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">Đã trúng tuyển</span>
          <strong style="font-size: 1.4rem; color: #059669;">{{ $stats['accepted_count'] }}</strong>
        </div>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fdf2f8; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
          <i class="fa-solid fa-file-pdf"></i>
        </div>
        <div>
          <span style="font-size: 0.82rem; color: var(--text-muted); display: block;">CV đã lưu</span>
          <strong style="font-size: 1.4rem; color: var(--text-main);">{{ $stats['cv_count'] }}</strong>
        </div>
      </div>
    </div>

    <!-- 2 Column Layout: Recent Applications & Skills -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 28px; align-items: start;">
      
      <!-- Recent Applications -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Lịch Sử Ứng Tuyển Gần Đây
          </h3>
          <a href="{{ route('candidate.applications') }}" style="font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Xem tất cả
          </a>
        </div>

        @if($applications->isEmpty())
          <div style="padding: 40px 20px; text-align: center; color: var(--text-muted);">
            <i class="fa-regular fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; color: var(--text-light);"></i>
            <p style="margin-bottom: 16px;">Bạn chưa nộp hồ sơ vào vị trí công việc nào.</p>
            <a href="{{ route('jobs.index') }}" class="btn-search" style="display: inline-flex; padding: 8px 20px; font-size: 0.9rem;">
              Khám phá việc làm ngay
            </a>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($applications as $app)
              <div style="padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                  <a href="{{ route('jobs.show', $app->job_post_id) }}" style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-main)'">
                    {{ $app->jobPost->title }}
                  </a>
                  <p style="font-size: 0.85rem; color: var(--text-muted); margin: 4px 0 0;">
                    {{ $app->jobPost->company ? $app->jobPost->company->name : 'Doanh nghiệp' }} • Nộp ngày {{ $app->applied_at ? $app->applied_at->format('d/m/Y') : '' }}
                  </p>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                  <span class="job-tag {{ $app->status_color }}" style="font-size: 0.82rem; font-weight: 600; padding: 4px 12px;">
                    {{ $app->status_label }}
                  </span>

                  @if($app->canWithdraw())
                    <form action="{{ route('candidate.applications.withdraw', $app->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn rút hồ sơ ứng tuyển này?')">
                      @csrf
                      <button type="submit" style="background: none; border: 1px solid #fecaca; color: #ef4444; border-radius: var(--radius-sm); padding: 4px 10px; font-size: 0.8rem; cursor: pointer;">
                        Rút hồ sơ
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- Skills & CV Highlights -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- User Skills Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin: 0;">
              <i class="fa-solid fa-code" style="color: var(--primary); margin-right: 6px;"></i> Hồ Sơ Kỹ Năng
            </h4>
            <a href="{{ route('candidate.profile') }}" style="font-size: 0.82rem; color: var(--primary); text-decoration: none;">Cập nhật</a>
          </div>

          @if($userSkills->isEmpty())
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">
              Chưa cập nhật kỹ năng. Thêm kỹ năng giúp hệ thống gợi ý công việc phù hợp hơn!
            </p>
            <a href="{{ route('candidate.profile') }}" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">+ Thêm kỹ năng ngay</a>
          @else
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
              @foreach($userSkills as $skill)
                <span class="job-tag" style="background: #eff6ff; color: #2563eb; border-color: #bfdbfe;">
                  {{ $skill->name }}
                </span>
              @endforeach
            </div>
          @endif
        </div>

        <!-- Primary CV Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin: 0;">
              <i class="fa-solid fa-file-lines" style="color: #059669; margin-right: 6px;"></i> CV Mặc Định
            </h4>
            <a href="{{ route('candidate.cvs') }}" style="font-size: 0.82rem; color: var(--primary); text-decoration: none;">Quản lý</a>
          </div>

          @if($primaryCv)
            <div style="padding: 12px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; gap: 10px;">
              <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                <i class="fa-solid fa-file-pdf" style="font-size: 1.6rem; color: #ef4444; flex-shrink: 0;"></i>
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <strong style="font-size: 0.9rem; color: var(--text-main); display: block; overflow: hidden; text-overflow: ellipsis;">{{ $primaryCv->title }}</strong>
                  <span style="font-size: 0.78rem; color: var(--text-muted);">Đã sẵn sàng để nộp nhanh</span>
                </div>
              </div>
              <a href="{{ route('cvs.download', $primaryCv->id) }}" target="_blank" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; white-space: nowrap; text-decoration: none;" title="Xem CV mặc định">
                <i class="fa-solid fa-eye"></i> Xem
              </a>
            </div>
          @else
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Bạn chưa có CV mặc định.</p>
            <a href="{{ route('candidate.cvs') }}" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">+ Tải lên CV mới</a>
          @endif
        </div>

      </div>

    </div>

    <!-- Recommended Jobs Based on Skills -->
    <div style="margin-top: 28px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); flex-wrap: wrap; gap: 10px;">
        <div>
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary); margin-right: 8px;"></i> Việc Làm Gợi Ý Theo Kỹ Năng
          </h3>
          <p style="font-size: 0.85rem; color: var(--text-muted); margin: 4px 0 0;">Đối sánh tự động từ bộ kỹ năng chuyên môn trong hồ sơ của bạn.</p>
        </div>
        <a href="{{ route('jobs.index') }}" style="font-size: 0.88rem; color: var(--primary); font-weight: 600;">Khám phá tất cả <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      @if($recommendedJobs->isEmpty())
        <div style="padding: 24px; text-align: center; color: var(--text-muted);">
          <p style="margin-bottom: 12px;">Hãy cập nhật kỹ năng chuyên môn để nhận gợi ý việc làm phù hợp nhất với bạn.</p>
          <a href="{{ route('candidate.profile') }}" class="btn-apply-sm" style="display: inline-flex; padding: 8px 16px;">Cập nhật kỹ năng ngay <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      @else
        <div class="recommended-jobs-grid">
          @foreach($recommendedJobs as $rec)
            @php $rJob = $rec['job']; $rMatch = $rec['analysis']; @endphp
            <div class="recommended-job-card">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
                <a href="{{ route('jobs.show', $rJob->id) }}" style="font-weight: 700; color: var(--text-main); font-size: 0.98rem; line-height: 1.4;">{{ $rJob->title }}</a>
                <span class="match-badge {{ $rMatch['tier_class'] }}">{{ $rMatch['score'] }}% hợp</span>
              </div>
              <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0 0 10px;">
                {{ $rJob->company?->name }} · {{ $rJob->location?->name ?? 'Toàn quốc' }} · <strong style="color:#059669;">{{ $rJob->salary_text }}</strong>
              </p>
              <div class="skill-pill-row" style="margin-bottom: 12px;">
                @foreach($rMatch['matched']->take(4) as $mSkill)
                  <span class="skill-pill is-matched"><i class="fa-solid fa-check"></i> {{ $mSkill->name }}</span>
                @endforeach
              </div>
              <a href="{{ route('jobs.show', $rJob->id) }}" class="btn-apply-sm">Xem & Ứng tuyển <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          @endforeach
        </div>
      @endif
    </div>

  </div>
</section>
@endsection
