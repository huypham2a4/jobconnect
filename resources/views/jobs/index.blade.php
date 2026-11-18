@extends('layouts.app')
@section('title', 'Tìm việc làm - JobConnect')
@section('content')
<section class="search-page-header"><div class="container">
  <span class="eyebrow">TÌM ĐIỀU PHÙ HỢP VỚI BẠN</span><h1>Cơ hội mới, hành trình mới.</h1><p>Tìm kiếm theo kỹ năng, địa điểm và mức lương bạn mong muốn.</p>
  <form action="{{ route('jobs.index') }}" method="GET" class="search-panel">
    <div class="hero-search">
      <label><span>Vị trí hoặc kỹ năng</span><div><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="{{ request('q') }}" placeholder="Laravel, Marketing..." aria-label="Vị trí hoặc kỹ năng"></div></label>
      <label><span>Ngành nghề</span><div><select name="category" aria-label="Ngành nghề"><option value="">Tất cả ngành nghề</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach</select></div></label>
      <label><span>Địa điểm</span><div><select name="location" aria-label="Địa điểm"><option value="">Toàn quốc</option>@foreach($locations as $location)<option value="{{ $location->slug }}" @selected(request('location') === $location->slug)>{{ $location->name }}</option>@endforeach</select></div></label>
      <button class="btn-search" type="submit">Tìm kiếm <i class="fa-solid fa-arrow-right"></i></button>
    </div>
    <div class="search-filters">
      <label>Hình thức<select name="type"><option value="">Tất cả hình thức</option>@foreach(['full-time'=>'Toàn thời gian','part-time'=>'Bán thời gian','remote'=>'Từ xa','internship'=>'Thực tập','hybrid'=>'Kết hợp'] as $value=>$label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select></label>
      <label>Kinh nghiệm<select name="experience_level"><option value="">Tất cả cấp bậc</option>@foreach(['all'=>'Không yêu cầu','intern'=>'Thực tập (Intern)','fresher'=>'Fresher','junior'=>'Junior','middle'=>'Middle','senior'=>'Senior','lead'=>'Lead / Manager'] as $value=>$label)<option value="{{ $value }}" @selected(request('experience_level') === $value)>{{ $label }}</option>@endforeach</select></label>
      <label>Mức lương<select name="salary_range">@foreach([''=>'Tất cả mức lương','under_15'=>'Dưới 15 triệu','15_25'=>'15 - 25 triệu','25_40'=>'25 - 40 triệu','above_40'=>'Trên 40 triệu','negotiable'=>'Thỏa thuận'] as $value=>$label)<option value="{{ $value }}" @selected(request('salary_range', '') === $value)>{{ $label }}</option>@endforeach</select></label>
      <label>Sắp xếp<select name="sort">@foreach(['latest'=>'Mới nhất','salary_high'=>'Lương cao nhất','deadline'=>'Sắp hết hạn'] as $value=>$label)<option value="{{ $value }}" @selected(request('sort','latest') === $value)>{{ $label }}</option>@endforeach</select></label>
      <a href="{{ route('jobs.index') }}">Xóa bộ lọc <i class="fa-solid fa-xmark"></i></a>
    </div>
  </form>
</div></section>
<section class="section"><div class="container">
  <div class="results-heading"><h2><strong>{{ $jobs->total() }}</strong> việc làm phù hợp</h2><span>Tin đã được kiểm duyệt</span></div>
  @if($jobs->isEmpty())
    <div class="empty-state"><i class="fa-solid fa-magnifying-glass"></i><h2>Không tìm thấy việc làm phù hợp</h2><p>Thử từ khóa khác hoặc điều chỉnh bộ lọc để khám phá thêm cơ hội.</p><a href="{{ route('jobs.index') }}" class="btn-search">Xem tất cả việc làm</a></div>
  @else
    <div class="jobs-grid">@foreach($jobs as $job)<x-job-card :job="$job" :saved="in_array($job->id, $savedJobIds)" />@endforeach</div>
    <div class="pagination-wrap">{{ $jobs->links() }}</div>
  @endif
</div></section>
@endsection
