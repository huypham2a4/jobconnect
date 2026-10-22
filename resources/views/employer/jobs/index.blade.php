@extends('layouts.app')

@section('title', 'Quản Lý Tin Tuyển Dụng - ' . $company->name)

@section('content')
<section class="section">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Danh Sách Tin Tuyển Dụng
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Quản lý, chỉnh sửa, đóng/mở và theo dõi lượt ứng tuyển của các vị trí công việc
        </p>
      </div>

      <a href="{{ route('employer.jobs.create') }}" class="btn-search" style="padding: 10px 24px; font-size: 0.95rem;">
        <i class="fa-solid fa-plus-circle"></i> Đăng tin mới
      </a>
    </div>

    @if($jobs->isEmpty())
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center;">
        <i class="fa-solid fa-briefcase" style="font-size: 3rem; color: var(--text-light); margin-bottom: 16px;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Chưa có tin tuyển dụng nào</h3>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Bắt đầu đăng tin để tiếp cận mạng lưới ứng viên chất lượng cao ngay hôm nay.</p>
        <a href="{{ route('employer.jobs.create') }}" class="btn-search" style="display: inline-flex; padding: 10px 24px;">Đăng tin tuyển dụng</a>
      </div>
    @else
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
            <thead>
              <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
                <th style="padding: 14px 20px;">Tiêu đề vị trí</th>
                <th style="padding: 14px 16px;">Ngành nghề & Địa điểm</th>
                <th style="padding: 14px 16px;">Mức lương</th>
                <th style="padding: 14px 16px;">Hạn nộp</th>
                <th style="padding: 14px 16px; text-align: center;">Hồ sơ</th>
                <th style="padding: 14px 16px; text-align: center;">Trạng thái</th>
                <th style="padding: 14px 20px; text-align: right;">Hành động</th>
              </tr>
            </thead>
            <tbody>
              @foreach($jobs as $job)
                <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                  <td style="padding: 16px 20px;">
                    <a href="{{ route('jobs.show', $job->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 0.98rem; display: block; margin-bottom: 2px;">
                      {{ $job->title }}
                    </a>
                    <span style="font-size: 0.8rem; color: var(--text-light);">
                      Hình thức: {{ ucfirst($job->type) }}
                    </span>
                  </td>

                  <td style="padding: 16px;">
                    <div style="color: var(--text-main); font-weight: 500;">{{ $job->category ? $job->category->name : 'N/A' }}</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> {{ $job->location ? $job->location->name : 'Toàn quốc' }}</div>
                  </td>

                  <td style="padding: 16px; font-weight: 600; color: #059669;">
                    {{ $job->salary_text }}
                  </td>

                  <td style="padding: 16px;">
                    <span style="font-size: 0.88rem; color: {{ $job->is_expired ? '#ef4444' : 'var(--text-main)' }};">
                      {{ $job->days_left_text }}
                    </span>
                  </td>

                  <td style="padding: 16px; text-align: center;">
                    <a href="{{ route('employer.applications', ['job_id' => $job->id]) }}" style="font-weight: 700; color: var(--primary); text-decoration: none; background: #eff6ff; padding: 4px 10px; border-radius: var(--radius-full);">
                      {{ $job->applications_count }}
                    </a>
                  </td>

                  <td style="padding: 16px; text-align: center;">
                    @if($job->status === 'approved')
                      <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                        Đang tuyển
                      </span>
                    @elseif($job->status === 'closed')
                      <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                        Đã đóng
                      </span>
                    @else
                      <span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                        Chờ duyệt
                      </span>
                    @endif
                  </td>

                  <td style="padding: 16px 20px; text-align: right;">
                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                      <a href="{{ route('employer.jobs.edit', $job->id) }}" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color);" title="Chỉnh sửa">
                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                      </a>

                      <form action="{{ route('employer.jobs.toggle', $job->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color); color: {{ $job->status === 'closed' ? '#059669' : '#dc2626' }};" title="{{ $job->status === 'closed' ? 'Mở lại tin' : 'Đóng tin' }}">
                          {{ $job->status === 'closed' ? 'Mở lại' : 'Đóng tin' }}
                        </button>
                      </form>

                      <!-- Nút Gia hạn tin tuyển dụng -->
                      <form action="{{ route('employer.jobs.renew', $job->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="extend_days" value="30">
                        <button type="submit" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857;" title="Gia hạn thêm 30 ngày">
                          <i class="fa-solid fa-clock-rotate-left"></i> Gia hạn
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div style="padding: 20px; display: flex; justify-content: center;">
          {{ $jobs->links() }}
        </div>
      </div>
    @endif

  </div>
</section>
@endsection
