@props(['jobId', 'saved' => false])
@if(auth()->check() && auth()->user()->isCandidate())
  <button type="button" class="bookmark-btn {{ $saved ? 'saved' : '' }}" data-save-url="{{ route('candidate.saved.store', $jobId) }}" data-saved="{{ $saved ? 'true' : 'false' }}" aria-pressed="{{ $saved ? 'true' : 'false' }}" aria-label="{{ $saved ? 'Bỏ lưu việc làm' : 'Lưu việc làm' }}" title="{{ $saved ? 'Bỏ lưu việc làm' : 'Lưu việc làm' }}"><i class="{{ $saved ? 'fa-solid' : 'fa-regular' }} fa-bookmark" aria-hidden="true"></i></button>
@elseif(!auth()->check())
  <a class="bookmark-btn" href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" aria-label="Đăng nhập để lưu việc làm" title="Đăng nhập để lưu việc làm"><i class="fa-regular fa-bookmark" aria-hidden="true"></i></a>
@endif
