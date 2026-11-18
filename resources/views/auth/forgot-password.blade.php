@extends('layouts.app')

@section('title', 'Quên Mật Khẩu - JobConnect')

@section('content')
<section class="section" style="min-height: calc(100vh - 350px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
  <div class="container" style="max-width: 480px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); padding: 36px 32px;">
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--primary-light); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
          <i class="fa-solid fa-key"></i>
        </div>
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Khôi Phục Mật Khẩu</h2>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu mới.</p>
      </div>

      @if(session('demo_reset_url'))
        <div class="moderation-note" style="margin-bottom: 20px;">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
          <strong>Chế độ Local Demo:</strong> Hệ thống đã tạo liên kết đặt lại mật khẩu (thời hạn 60 phút):
          <div style="margin-top: 8px;">
            <a href="{{ session('demo_reset_url') }}" style="color: var(--primary); font-weight: 700; word-break: break-all;">Mở trang đặt lại mật khẩu ngay <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      @endif

      <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
          <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Địa chỉ Email</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="ten@domain.com"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <button type="submit" class="btn-search" style="width: 100%; justify-content: center; height: 46px; font-size: 0.98rem;">
          <span>Tạo liên kết đặt lại mật khẩu</span> <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div style="text-align: center; margin-top: 22px; font-size: 0.9rem;">
        <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a>
      </div>
    </div>
  </div>
</section>
@endsection
