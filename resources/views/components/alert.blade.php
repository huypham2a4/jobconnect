@props(['type' => 'info', 'message' => null, 'dismissible' => true])
<div {{ $attributes->class(['notice', 'notice-error' => $type === 'error']) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
  <span>{{ $message ?? $slot }}</span>
  @if($dismissible)<button type="button" class="notice-close" aria-label="Đóng thông báo" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>@endif
</div>
