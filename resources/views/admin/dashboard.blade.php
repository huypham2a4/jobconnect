@extends('layouts.app')

@section('title', 'Trung Tâm Quản Trị Hệ Thống - JobConnect')

@section('content')
<section class="section">
  <div class="container">
    
    <!-- Admin Header -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div style="display: flex; align-items: center; gap: 18px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
          <h2 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
            Bảng Điều Khiển Quản Trị (Admin Portal)
          </h2>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
            Giám sát toàn diện hệ sinh thái, kiểm duyệt tin và quản lý phân quyền
          </p>
        </div>
      </div>

      <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.permissions.index') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);"><i class="fa-solid fa-user-shield"></i> Phân quyền</a>
        <a href="{{ route('admin.jobs.index') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
          <i class="fa-solid fa-clipboard-check"></i> Duyệt tin ({{ $stats['pending_jobs'] }})
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
          <i class="fa-solid fa-users-gear"></i> Quản lý người dùng
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn-search" style="padding: 10px 18px; font-size: 0.88rem;">
          <i class="fa-solid fa-layer-group"></i> Ngành nghề
        </a>
      </div>
    </div>

    <!-- Stats Grid (6 KPIs) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px;">
      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Tổng người dùng</span>
        <strong style="font-size: 1.4rem; color: var(--text-main);">{{ $stats['total_users'] }}</strong>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Ứng viên (Candidates)</span>
        <strong style="font-size: 1.4rem; color: #059669;">{{ $stats['candidates_count'] }}</strong>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Doanh nghiệp (Employers)</span>
        <strong style="font-size: 1.4rem; color: #2563eb;">{{ $stats['employers_count'] }}</strong>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Tin đang hoạt động</span>
        <strong style="font-size: 1.4rem; color: #0891b2;">{{ $stats['active_jobs'] }}</strong>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Tin chờ duyệt</span>
        <strong style="font-size: 1.4rem; color: #dc2626;">{{ $stats['pending_jobs'] }}</strong>
      </div>

      <div class="candidate-stat-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
        <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Lượt ứng tuyển</span>
        <strong style="font-size: 1.4rem; color: #8b5cf6;">{{ $stats['total_applications'] }}</strong>
      </div>
    </div>

    <!-- 2 Column Section -->
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 28px; align-items: start;">
      
      <!-- Pending Jobs Approval Queue -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-hourglass-half" style="color: #d97706; margin-right: 8px;"></i> Hàng Đợi Duyệt Tin Mới
          </h3>
          <a href="{{ route('admin.jobs.index', ['status' => 'pending']) }}" style="font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Xem tất cả
          </a>
        </div>

        @if($pendingJobs->isEmpty())
          <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; color: #10b981; margin-bottom: 12px;"></i>
            <p style="margin: 0;">Tuyệt vời! Hiện không có tin tuyển dụng nào đang chờ phê duyệt.</p>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($pendingJobs as $pJob)
              <div style="padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                  <a href="{{ route('jobs.show', $pJob->id) }}" target="_blank" style="font-weight: 700; font-size: 1rem; color: var(--text-main); text-decoration: none;">
                    {{ $pJob->title }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.8rem; color: var(--text-light);"></i>
                  </a>
                  <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px;">
                    Công ty: <strong>{{ $pJob->company ? $pJob->company->name : 'N/A' }}</strong> • Ngành: {{ $pJob->category ? $pJob->category->name : 'N/A' }}
                  </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                  <form action="{{ route('admin.jobs.approve', $pJob->id) }}" method="POST">
                    @csrf
                    <button type="submit" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-weight: 600; padding: 6px 14px; border-radius: var(--radius-sm); font-size: 0.82rem; cursor: pointer;">
                      <i class="fa-solid fa-check"></i> Duyệt
                    </button>
                  </form>

                  <form action="{{ route('admin.jobs.reject', $pJob->id) }}" method="POST">
                    @csrf
                    <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-weight: 600; padding: 6px 14px; border-radius: var(--radius-sm); font-size: 0.82rem; cursor: pointer;">
                      <i class="fa-solid fa-xmark"></i> Từ chối
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- Recent Users -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
            <i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 8px;"></i> Người Dùng Mới
          </h3>
          <a href="{{ route('admin.users.index') }}" style="font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Tất cả
          </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          @foreach($recentUsers as $rUser)
            <div style="padding: 10px 12px; background: #f8fafc; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="font-size: 0.9rem; color: var(--text-main); display: block;">{{ $rUser->name }}</strong>
                <span style="font-size: 0.78rem; color: var(--text-muted);">{{ $rUser->email }}</span>
              </div>
              <span class="job-tag" style="font-size: 0.72rem; padding: 2px 8px;">
                {{ ucfirst($rUser->role) }}
              </span>
            </div>
          @endforeach
        </div>
      </div>

    </div>

    <!-- Analytics Section (Join & Group By Statistics) -->
    <div style="margin-top: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
      
      <!-- Thống kê tin tuyển dụng theo ngành nghề -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <i class="fa-solid fa-chart-pie" style="color: var(--primary); margin-right: 8px;"></i> Phân Bổ Tin Theo Ngành Nghề (Join + Group By)
        </h3>
        
        @if($categoryStats->isEmpty())
          <p style="color: var(--text-muted); font-size: 0.9rem;">Chưa có dữ liệu thống kê.</p>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @php $maxJob = $categoryStats->max('job_count') ?: 1; @endphp
            @foreach($categoryStats as $cStat)
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 4px;">
                  <span style="font-weight: 600; color: var(--text-main);">{{ $cStat->name }}</span>
                  <span style="color: var(--text-muted);">{{ $cStat->job_count }} tin</span>
                </div>
                <div style="height: 8px; background: rgba(0,0,0,0.06); border-radius: 4px; overflow: hidden;">
                  <div style="height: 100%; width: {{ ($cStat->job_count / $maxJob) * 100 }}%; background: var(--primary); border-radius: 4px;"></div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- Thống kê tỷ lệ ứng tuyển theo ngành nghề -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <i class="fa-solid fa-fire" style="color: #ef4444; margin-right: 8px;"></i> Top Ngành Thu Hút Ứng Tuyển Nhất (Join + Group By)
        </h3>

        @if($applicationStats->isEmpty())
          <p style="color: var(--text-muted); font-size: 0.9rem;">Chưa có dữ liệu ứng tuyển.</p>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @php $maxApp = $applicationStats->max('total_applications') ?: 1; @endphp
            @foreach($applicationStats as $aStat)
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 4px;">
                  <span style="font-weight: 600; color: var(--text-main);">{{ $aStat->category_name }}</span>
                  <span style="color: var(--text-muted); font-weight: 600;">{{ $aStat->total_applications }} lượt nộp</span>
                </div>
                <div style="height: 8px; background: rgba(0,0,0,0.06); border-radius: 4px; overflow: hidden;">
                  <div style="height: 100%; width: {{ ($aStat->total_applications / $maxApp) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #ef4444); border-radius: 4px;"></div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>

    <!-- Visual Charts & Top Viewed Jobs -->
    <div style="margin-top: 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <i class="fa-solid fa-chart-donut" style="color: #8b5cf6; margin-right: 8px;"></i> Biểu Đồ Trạng Thái Hồ Sơ Ứng Tuyển
        </h3>
        @php
          $statusMeta = [
            'submitted' => ['label' => 'Đã nộp', 'color' => '#2563eb'],
            'viewed' => ['label' => 'NTD đã xem', 'color' => '#7c3aed'],
            'interview' => ['label' => 'Phỏng vấn', 'color' => '#d97706'],
            'accepted' => ['label' => 'Trúng tuyển', 'color' => '#059669'],
            'rejected' => ['label' => 'Chưa phù hợp', 'color' => '#e11d48'],
            'withdrawn' => ['label' => 'Đã rút', 'color' => '#64748b'],
          ];
          $totalAppsChart = array_sum($applicationStatusStats ?? []);
          $circumference = 2 * M_PI * 54;
          $offset = 0;
        @endphp
        @if($totalAppsChart === 0)
          <p style="color: var(--text-muted); font-size: 0.9rem;">Chưa có hồ sơ ứng tuyển nào để hiển thị biểu đồ.</p>
        @else
          <div class="admin-donut-wrap">
            <svg viewBox="0 0 140 140" class="admin-donut-svg" aria-label="Biểu đồ trạng thái hồ sơ">
              <circle cx="70" cy="70" r="54" fill="none" stroke="var(--border-color)" stroke-width="18"></circle>
              @foreach($statusMeta as $key => $meta)
                @php
                  $count = $applicationStatusStats[$key] ?? 0;
                  $segment = ($count / $totalAppsChart) * $circumference;
                @endphp
                @if($count > 0)
                  <circle cx="70" cy="70" r="54" fill="none" stroke="{{ $meta['color'] }}" stroke-width="18"
                          stroke-dasharray="{{ number_format($segment, 2, '.', '') }} {{ number_format($circumference, 2, '.', '') }}"
                          stroke-dashoffset="-{{ number_format($offset, 2, '.', '') }}"
                          transform="rotate(-90 70 70)"></circle>
                  @php $offset += $segment; @endphp
                @endif
              @endforeach
              <text x="70" y="67" text-anchor="middle" class="donut-center-num">{{ $totalAppsChart }}</text>
              <text x="70" y="84" text-anchor="middle" class="donut-center-label">hồ sơ</text>
            </svg>
            <div class="admin-donut-legend">
              @foreach($statusMeta as $key => $meta)
                @php $count = $applicationStatusStats[$key] ?? 0; @endphp
                <div class="legend-row">
                  <span><i class="legend-dot" style="background:{{ $meta['color'] }}"></i> {{ $meta['label'] }}</span>
                  <strong>{{ $count }} ({{ round(($count / $totalAppsChart) * 100) }}%)</strong>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
          <i class="fa-solid fa-eye" style="color: #0891b2; margin-right: 8px;"></i> Top Tin Tuyển Dụng Được Xem Nhiều Nhất
        </h3>
        @if($topViewedJobs->isEmpty())
          <p style="color: var(--text-muted); font-size: 0.9rem;">Chưa có dữ liệu lượt xem.</p>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($topViewedJobs as $tJob)
              <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 10px;">
                <div style="min-width: 0;">
                  <a href="{{ route('jobs.show', $tJob->id) }}" style="font-weight: 600; color: var(--text-main); font-size: 0.92rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $tJob->title }}</a>
                  <span style="font-size: 0.78rem; color: var(--text-muted);">{{ $tJob->company?->name }}</span>
                </div>
                <span style="font-weight: 700; color: #0891b2; font-size: 0.85rem; white-space: nowrap;">
                  <i class="fa-regular fa-eye"></i> {{ number_format($tJob->views_count) }}
                </span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

  </div>
</section>
@endsection
