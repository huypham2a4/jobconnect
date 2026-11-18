@extends('layouts.app')
@section('title', 'Việc làm đã lưu - JobConnect')
@section('content')
<section class="section"><div class="container">
  <div class="page-heading"><div><span class="eyebrow">GÓC ỨNG VIÊN</span><h1>Việc làm đã lưu</h1><p>{{ $jobs->total() }} cơ hội bạn quan tâm. Danh sách được giữ lại khi đăng nhập trên thiết bị khác.</p></div><a href="{{ route('jobs.index') }}" class="btn-login">Khám phá việc làm <i class="fa-solid fa-arrow-right"></i></a></div>
  @if($jobs->isEmpty())<div class="empty-state"><i class="fa-regular fa-bookmark"></i><h2>Lưu lại cơ hội bạn yêu thích</h2><p>Bấm biểu tượng đánh dấu ở mỗi tin tuyển dụng để xem lại tại đây.</p><a href="{{ route('jobs.index') }}" class="btn-search">Tìm việc làm</a></div>
  @else<div class="jobs-grid">@foreach($jobs as $job)<x-job-card :job="$job" :saved="true" />@endforeach</div><div class="pagination-wrap">{{ $jobs->links() }}</div>@endif
</div></section>
@endsection
