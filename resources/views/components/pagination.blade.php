@if ($paginator->hasPages())
  <nav class="pagination-nav" role="navigation" aria-label="Phân trang">
    <p class="pagination-summary">Hiển thị {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} trong {{ $paginator->total() }} kết quả</p>
    <div class="pagination-pages">
      @if ($paginator->onFirstPage())
        <span class="pagination-page is-disabled" aria-disabled="true">Trước</span>
      @else
        <a class="pagination-page" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">Trước</a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="pagination-ellipsis" aria-hidden="true">{{ $element }}</span>
        @elseif (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page === $paginator->currentPage())
              <span class="pagination-page is-current" aria-current="page" aria-label="Trang {{ $page }}">{{ $page }}</span>
            @else
              <a class="pagination-page" href="{{ $url }}" aria-label="Trang {{ $page }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a class="pagination-page" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">Sau</a>
      @else
        <span class="pagination-page is-disabled" aria-disabled="true">Sau</span>
      @endif
    </div>
  </nav>
@endif
