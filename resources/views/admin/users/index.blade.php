@extends('layouts.app')

@section('title', 'Quản Lý Người Dùng - Admin')

@section('content')
<section class="section">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Quản Lý Tài Khoản Người Dùng
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Theo dõi danh sách ứng viên, nhà tuyển dụng, cấp quyền và xử lý vi phạm
        </p>
      </div>

      <!-- Filters -->
      <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên, email hoặc SĐT..."
               style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; outline: none; width: 220px;">

        <select name="role" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; background: var(--bg-card); outline: none;">
          <option value="">Tất cả vai trò</option>
          <option value="candidate" {{ request('role') === 'candidate' ? 'selected' : '' }}>Ứng viên (Candidate)</option>
          <option value="employer" {{ request('role') === 'employer' ? 'selected' : '' }}>Nhà tuyển dụng (Employer)</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
        </select>
      </form>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
          <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
              <th style="padding: 14px 20px;">Thành viên</th>
              <th style="padding: 14px 16px;">Liên hệ</th>
              <th style="padding: 14px 16px;">Vai trò</th>
              <th style="padding: 14px 16px;">Ngày tham gia</th>
              <th style="padding: 14px 16px; text-align: center;">Trạng thái</th>
              <th style="padding: 14px 20px; text-align: right;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $u)
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 16px 20px;">
                  <strong style="color: var(--text-main); font-size: 0.98rem; display: block;">
                    {{ $u->name }}
                  </strong>
                  <span style="font-size: 0.8rem; color: var(--text-muted);">
                    ID: #{{ $u->id }}
                  </span>
                </td>

                <td style="padding: 16px;">
                  <div style="color: var(--text-main);">{{ $u->email }}</div>
                  <div style="font-size: 0.82rem; color: var(--text-muted);">{{ $u->phone ?? 'Chưa cập nhật SĐT' }}</div>
                </td>

                <td style="padding: 16px;">
                  @if($u->isAdmin())
                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      <i class="fa-solid fa-shield-halved" style="margin-right: 2px;"></i> Admin
                    </span>
                  @elseif($u->isEmployer())
                    <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      <i class="fa-solid fa-building" style="margin-right: 2px;"></i> NTD
                    </span>
                  @else
                    <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                      <i class="fa-solid fa-user-graduate" style="margin-right: 2px;"></i> Ứng viên
                    </span>
                  @endif
                </td>

                <td style="padding: 16px; color: var(--text-muted); font-size: 0.88rem;">
                  {{ $u->created_at ? $u->created_at->format('d/m/Y') : '' }}
                </td>

                <td style="padding: 16px; text-align: center;">
                  @if($u->is_active)
                    <span style="color: #059669; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-circle-check"></i> Hoạt động
                    </span>
                  @else
                    <span style="color: #dc2626; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-lock"></i> Đã khóa
                    </span>
                  @endif
                </td>

                <td style="padding: 16px 20px; text-align: right;">
                  @if($u->id !== auth()->id())
                    <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" style="display: inline;">
                      @csrf
                      <button type="submit" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color); color: {{ $u->is_active ? '#dc2626' : '#059669' }};">
                        @if($u->is_active)
                          <i class="fa-solid fa-lock"></i> Khóa tài khoản
                        @else
                          <i class="fa-solid fa-lock-open"></i> Mở khóa
                        @endif
                      </button>
                    </form>
                  @else
                    <span style="font-size: 0.8rem; color: var(--text-light);">(Tài khoản hiện tại)</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="padding: 20px; display: flex; justify-content: center;">
        {{ $users->links() }}
      </div>
    </div>

  </div>
</section>
@endsection
