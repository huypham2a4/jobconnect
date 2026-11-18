@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - JobConnect')

@section('content')
<section class="section" style="min-height: calc(100vh - 350px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
  <div class="container" style="max-width: 480px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); padding: 36px 32px;">
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--primary-light); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Đặt Mật Khẩu Mới</h2>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Nhập mật khẩu mới tối thiểu 6 ký tự cho tài khoản của bạn.</p>
      </div>

      <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div style="margin-bottom: 16px;">
          <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Email tài khoản</label>
          <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="margin-bottom: 16px;">
          <label for="password" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Mật khẩu mới</label>
          <input type="password" name="password" id="password" required minlength="6" placeholder="••••••••"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="margin-bottom: 22px;">
          <label for="password_confirmation" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Xác nhận mật khẩu mới</label>
          <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6" placeholder="••••••••"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <button type="submit" class="btn-search" style="width: 100%; justify-content: center; height: 46px; font-size: 0.98rem;">
          <span>Lưu mật khẩu mới</span> <i class="fa-solid fa-check"></i>
        </button>
      </form>
    </div>
  </div>
</section>
@endsection
