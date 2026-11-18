@extends('layouts.app')

@section('title', 'Trung Tâm Thông Báo - JobConnect')

@section('content')
<section class="section">
  <div class="container" style="max-width: 860px;">
    <div class="page-heading">
      <div>
        <h1>Trung tâm thông báo</h1>
        <p>Cập nhật trạng thái hồ sơ, lịch phỏng vấn và kết quả kiểm duyệt mới nhất.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('notifications.index') }}" class="btn-login" style="{{ request('filter') !== 'unread' ? 'border-color:var(--primary); color:var(--primary);' : '' }}">Tất cả</a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn-login" style="{{ request('filter') === 'unread' ? 'border-color:var(--primary); color:var(--primary);' : '' }}">Chưa đọc ({{ $unreadCount }})</a>
        @if($unreadCount > 0)
          <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn-search" style="padding:10px 16px; font-size:12px;"><i class="fa-solid fa-check-double"></i> Đọc tất cả</button>
          </form>
        @endif
      </div>
    </div>

    @if($notifications->isEmpty())
      <div class="empty-state">
        <i class="fa-regular fa-bell"></i>
        <h2>Chưa có thông báo nào</h2>
        <p>Các cập nhật về hồ sơ, lịch phỏng vấn và tin tuyển dụng sẽ xuất hiện tại đây.</p>
      </div>
    @else
      <div class="notifications-list">
        @foreach($notifications as $item)
          <div class="notification-item {{ $item->isRead() ? 'is-read' : 'is-unread' }}">
            <div class="notification-item-icon"><i class="fa-solid {{ $item->icon }}"></i></div>
            <div class="notification-item-body">
              <div class="notification-item-top">
                <strong>{{ $item->title }}</strong>
                <span>{{ $item->created_at->diffForHumans() }}</span>
              </div>
              <p>{{ $item->message }}</p>
            </div>
            <div class="notification-item-actions">
              @if($item->link)
                <form action="{{ route('notifications.read', $item->id) }}" method="POST">
                  @csrf
                  <input type="hidden" name="go" value="1">
                  <button type="submit" class="btn-apply-sm">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></button>
                </form>
              @elseif(!$item->isRead())
                <form action="{{ route('notifications.read', $item->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn-login" style="padding:6px 12px; font-size:11px;">Đánh dấu đã đọc</button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="pagination-wrap">{{ $notifications->links() }}</div>
    @endif
  </div>
</section>
@endsection
