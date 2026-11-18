@extends('layouts.app')

@section('title', 'Đổi Mật Khẩu - JobConnect')

@section('content')
<section class="section">
  <div class="container" style="max-width: 560px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 34px 32px; box-shadow: var(--shadow-sm);">
      <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          <i class="fa-solid fa-lock" style="color: var(--primary); margin-right: 8px;"></i> Đổi Mật Khẩu Tài Khoản
        </h2>
        <p style="color: var(--text-muted); font-size: 0.92rem; margin: 0;">Cập nhật mật khẩu định kỳ để bảo vệ tài khoản JobConnect của bạn.</p>
      </div>

      <form action="{{ route('account.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 18px;">
          <label for="current_password" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Mật khẩu hiện tại</label>
          <input type="password" name="current_password" id="current_password" required
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="margin-bottom: 18px;">
          <label for="password" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Mật khẩu mới (tối thiểu 6 ký tự)</label>
          <input type="password" name="password" id="password" required minlength="6"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="margin-bottom: 24px;">
          <label for="password_confirmation" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">Xác nhận mật khẩu mới</label>
          <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <a href="{{ url()->previous() }}" class="btn-login" style="border: 1px solid var(--border-color);">Quay lại</a>
          <button type="submit" class="btn-search" style="padding: 12px 24px;"><i class="fa-solid fa-floppy-disk"></i> Cập nhật mật khẩu</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
