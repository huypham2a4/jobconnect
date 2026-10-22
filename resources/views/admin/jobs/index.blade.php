@extends('layouts.app')

@section('title', 'Quản Lý Tất Cả Tin Tuyển Dụng - Admin')

@section('content')
<section class="section">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Quản Lý Toàn Bộ Tin Tuyển Dụng
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Kiểm duyệt, theo dõi trạng thái và xử lý các tin đăng vi phạm chính sách
        </p>
      </div>

      <!-- Filters -->
      <form action="{{ route('admin.jobs.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tiêu đề hoặc công ty..."
               style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; outline: none; width: 220px;">

        <select name="status" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; background: var(--bg-card); outline: none;">
          <option value="">Tất cả trạng thái</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
          <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
          <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
          <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đã đóng</option>
        </select>
      </form>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
          <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
              <th style="padding: 14px 20px;">Tin tuyển dụng</th>
              <th style="padding: 14px 16px;">Doanh nghiệp</th>
              <th style="padding: 14px 16px;">Ngành nghề</th>
              <th style="padding: 14px 16px;">Lương & Hạn nộp</th>
              <th style="padding: 14px 16px; text-align: center;">Trạng thái</th>
              <th style="padding: 14px 20px; text-align: right;">Kiểm duyệt</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jobs as $job)
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 16px 20px;">
                  <a href="{{ route('jobs.show', $job->id) }}" target="_blank" style="font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 0.98rem; display: block; margin-bottom: 2px;">
                    {{ $job->title }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.78rem; color: var(--text-light);"></i>
                  </a>
                  <span style="font-size: 0.8rem; color: var(--text-muted);">
                    Đăng ngày {{ $job->created_at ? $job->created_at->format('d/m/Y') : '' }} • {{ $job->applications_count }} ứng viên
                  </span>
                </td>

                <td style="padding: 16px; font-weight: 600; color: var(--text-main);">
                  {{ $job->company ? $job->company->name : 'N/A' }}
                </td>

                <td style="padding: 16px; color: var(--text-muted);">
                  {{ $job->category ? $job->category->name : 'N/A' }}
                </td>

                <td style="padding: 16px;">
                  <strong style="color: #059669; font-size: 0.88rem; display: block;">{{ $job->salary_text }}</strong>
                  <span style="font-size: 0.8rem; color: var(--text-muted);">Hạn: {{ $job->deadline ? $job->deadline->format('d/m/Y') : '' }}</span>
                </td>

                <td style="padding: 16px; text-align: center;">
                  @if($job->status === 'approved')
                    <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      Đã duyệt
                    </span>
                  @elseif($job->status === 'pending')
                    <span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      Chờ duyệt
                    </span>
                  @elseif($job->status === 'rejected')
                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      Từ chối
                    </span>
                  @else
                    <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      Đã đóng
                    </span>
                  @endif
                </td>

                <td style="padding: 16px 20px; text-align: right;">
                  <div style="display: inline-flex; align-items: center; gap: 6px;">
                    @if($job->status !== 'approved')
                      <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-login" style="padding: 4px 10px; font-size: 0.8rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857;" title="Phê duyệt tin">
                          <i class="fa-solid fa-check"></i> Duyệt
                        </button>
                      </form>
                    @endif

                    @if($job->status !== 'rejected')
                      <form action="{{ route('admin.jobs.reject', $job->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-login" style="padding: 4px 10px; font-size: 0.8rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;" title="Từ chối tin">
                          <i class="fa-solid fa-xmark"></i> Từ chối
                        </button>
                      </form>
                    @endif
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

  </div>
</section>
@endsection
