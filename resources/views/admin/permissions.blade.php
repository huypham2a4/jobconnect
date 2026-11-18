@extends('layouts.app')
@section('title', 'Phân quyền - JobConnect')
@section('content')
<section class="section permissions-page">
  <div class="container">
    <div class="page-heading">
      <div><span class="eyebrow">QUẢN TRỊ HỆ THỐNG</span><h1>Phân quyền</h1><p>Biết ai được làm gì và điều chỉnh quyền cho từng vai trò.</p></div>
      <a href="{{ route('admin.users.index') }}" class="btn-login permission-user-link"><i class="fa-solid fa-users"></i> Danh sách người dùng</a>
    </div>

    <div class="permission-role-cards" aria-label="Chọn vai trò cần quản lý">
      @foreach($roles as $role => $definition)
        @php
          $stat = $roleStats->get($role);
        @endphp
        <a href="{{ route('admin.permissions.index', ['role' => $role]) }}" class="permission-role-card {{ $role === $selectedRole ? 'is-selected' : '' }}" @if($role === $selectedRole) aria-current="true" @endif>
          <span class="permission-role-icon role-{{ $role }}"><i class="fa-solid {{ $definition['icon'] }}" aria-hidden="true"></i></span>
          <span class="permission-role-body"><strong>{{ $definition['label'] }}</strong><span>{{ $definition['description'] }}</span><small>{{ (int) ($stat?->total ?? 0) }} tài khoản · {{ (int) ($stat?->active ?? 0) }} đang hoạt động</small></span>
          <span class="permission-role-count">{{ count(array_filter($rolePermissions[$role])) }} quyền</span>
        </a>
      @endforeach
    </div>

    <div class="permission-panel">
      <div class="permission-panel-heading">
        <div><h2>Quyền của {{ $roles[$selectedRole]['label'] }}</h2><p>Đánh dấu để cấp quyền, bỏ dấu để thu hồi. Thay đổi áp dụng cho mọi tài khoản cùng vai trò.</p></div>
        <span class="permission-count">{{ count(array_filter($rolePermissions[$selectedRole])) }}/{{ count($rolePermissions[$selectedRole]) }} quyền đang bật</span>
      </div>
      <form method="POST" action="{{ route('admin.permissions.update') }}" id="role-permissions-form">
        @csrf
        <input type="hidden" name="role" value="{{ $selectedRole }}">
        <div class="permission-bulk-actions"><button type="button" data-permission-select="all">Chọn tất cả</button><button type="button" data-permission-select="none">Bỏ các quyền tùy chọn</button></div>
        <p class="permission-scroll-hint"><i class="fa-solid fa-arrows-left-right" aria-hidden="true"></i> Vuốt ngang bảng để xem đủ các cột quyền.</p>
        <div class="permission-table-scroll" tabindex="0" aria-label="Bảng quyền theo chức năng">
          <table class="permission-matrix">
            <thead><tr><th scope="col">Chức năng và phạm vi</th>@foreach(config('role_permissions.actions') as $label)<th scope="col">{{ $label }}</th>@endforeach</tr></thead>
            <tbody>
              @foreach($resources as $resource => $definition)
                @if(in_array($selectedRole, $definition['roles'], true))
                  <tr>
                    <th scope="row"><strong>{{ $definition['label'] }}</strong><span>{{ $definition['scope'] }}</span></th>
                    @foreach(config('role_permissions.actions') as $action => $label)
                      <td>
                        @if(in_array($action, $definition['actions'], true))
                          @php
                            $key = $resource.'.'.$action;
                            $required = in_array($key, config('role_permissions.required.'.$selectedRole, []), true);
                          @endphp
                          <label class="permission-checkbox" @if($required) title="Quyền luôn được giữ để Admin có thể quản lý lại hệ thống" @endif>
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, old('permissions', array_keys(array_filter($rolePermissions[$selectedRole]))), true)) @disabled($required) aria-label="{{ $label }} {{ $definition['label'] }}">
                            @if($required)<span><i class="fa-solid fa-lock" aria-hidden="true"></i> Luôn giữ</span>@endif
                          </label>
                        @else
                          <span class="permission-unavailable" aria-label="Không có thao tác này">—</span>
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="permission-save-bar"><p>Dấu — là thao tác chưa có ở chức năng này. Bấm lưu để áp dụng lựa chọn.</p><button type="submit" class="btn-search"><i class="fa-regular fa-floppy-disk"></i> Lưu quyền</button></div>
      </form>
    </div>

    <div class="permission-panel" id="user-access">
      <div class="permission-panel-heading"><div><h2>Quyền theo người dùng</h2><p>{{ $users->total() }} tài khoản {{ $roles[$selectedRole]['label'] }} phù hợp. Chọn một người để xem quyền đang có.</p></div></div>
      <form action="{{ route('admin.permissions.index') }}#user-access" method="GET" class="permission-user-filters">
        <input type="hidden" name="role" value="{{ $selectedRole }}">
        <label><span>Tên hoặc email</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm người dùng..."></label>
        <label><span>Trạng thái</span><select name="status"><option value="">Tất cả trạng thái</option><option value="active" @selected(request('status') === 'active')>Đang hoạt động</option><option value="locked" @selected(request('status') === 'locked')>Đã khóa</option></select></label>
        <button class="btn-search" type="submit">Tìm kiếm</button><a href="{{ route('admin.permissions.index', ['role' => $selectedRole]) }}#user-access" class="permission-clear">Xóa bộ lọc</a>
      </form>
      <div class="permission-table-scroll">
        <table class="permission-users-table">
          <thead><tr><th scope="col">Người dùng</th><th scope="col">Công ty hoặc vai trò</th><th scope="col">Trạng thái</th><th scope="col">Chi tiết</th></tr></thead>
          <tbody>
            @forelse($users as $user)
              <tr @if($selectedUser?->id === $user->id) class="is-selected" @endif>
                <td><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></td>
                <td>{{ $user->company?->name ?? $roles[$user->role]['label'] }}</td>
                <td><span class="permission-status {{ $user->is_active ? 'is-active' : 'is-locked' }}">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</span></td>
                <td><a class="user-permissions-link" href="{{ route('admin.permissions.index', array_merge(request()->only('role', 'q', 'status', 'page'), ['user_id' => $user->id])) }}#user-access">Xem quyền <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></td>
              </tr>
            @empty
              <tr><td colspan="4" class="permission-empty">Không tìm thấy tài khoản phù hợp.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="permission-user-pagination">{{ $users->links() }}</div>

      @if($selectedUser)
        <div class="permission-person-detail">
          <div><span class="eyebrow">TÀI KHOẢN ĐANG XEM</span><h3>{{ $selectedUser->name }}</h3><p>{{ $selectedUser->email }} · {{ $roles[$selectedUser->role]['label'] }}</p></div>
          @if(!$selectedUser->is_active)
            <p class="permission-locked-note"><i class="fa-solid fa-lock"></i> Tài khoản đã khóa. Các chức năng cần đăng nhập đang bị tạm ngưng, dù vai trò có quyền.</p>
          @else
            <div class="permission-effective-list">
              @foreach($rolePermissions[$selectedUser->role] as $key => $allowed)
                @if($allowed)
                  @php
                    [$resourceKey, $actionKey] = explode('.', $key);
                  @endphp
                  <span><i class="fa-solid fa-check" aria-hidden="true"></i> {{ $resources[$resourceKey]['label'] }} · {{ config('role_permissions.actions.'.$actionKey) }}</span>
                @endif
              @endforeach
            </div>
            @if(!in_array(true, $rolePermissions[$selectedUser->role], true))<p>Vai trò này hiện chưa được bật chức năng cần đăng nhập.</p>@endif
          @endif
        </div>
      @endif
    </div>
    <p class="permission-public-note"><i class="fa-solid fa-globe" aria-hidden="true"></i> Trang chủ và tìm việc công khai dành cho mọi người. Phạm vi dữ liệu cá nhân và công ty vẫn được áp dụng khi cấp quyền.</p>
  </div>
</section>
@endsection
@section('scripts')
<script>
document.querySelectorAll('[data-permission-select]').forEach(function (button) {
  button.addEventListener('click', function () {
    document.querySelectorAll('#role-permissions-form input[type="checkbox"]:not(:disabled)').forEach(function (checkbox) {
      checkbox.checked = button.dataset.permissionSelect === 'all';
    });
  });
});
</script>
@endsection
