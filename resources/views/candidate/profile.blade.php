@extends('layouts.app')

@section('title', 'Hồ Sơ & Kỹ Năng - JobConnect')

@section('content')
<section class="section">
  <div class="container" style="max-width: 800px;">
    
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-sm);">
      <div style="margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Cập Nhật Hồ Sơ & Kỹ Năng Ứng Viên
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Hoàn thiện thông tin cá nhân và khai báo kỹ năng để tăng cơ hội kết nối với nhà tuyển dụng
        </p>
      </div>

      <form action="{{ route('candidate.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="name" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Họ và tên <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
          </div>

          <div>
            <label for="phone" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Số điện thoại liên hệ
            </label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="0901234567"
                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Email đăng nhập (Không thể thay đổi)
          </label>
          <input type="email" value="{{ $user->email }}" disabled
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; background: #f8fafc; color: var(--text-muted);">
        </div>

        <div style="margin-bottom: 28px;">
          <label for="bio" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Giới thiệu bản thân & Mục tiêu nghề nghiệp
          </label>
          <textarea name="bio" id="bio" rows="4" placeholder="Tóm tắt ngắn gọn số năm kinh nghiệm, thế mạnh chuyên môn và định hướng phát triển..."
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <!-- Skills Selection Grid -->
        <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
          <div style="margin-bottom: 14px;">
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
              <i class="fa-solid fa-code" style="color: var(--primary); margin-right: 6px;"></i> Kỹ Năng Chuyên Môn (User Skills)
            </h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
              Chọn các kỹ năng bạn thành thạo để hệ thống đối sánh và gợi ý công việc phù hợp:
            </p>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px;">
            @foreach($skills as $skill)
              <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; cursor: pointer; transition: var(--transition);">
                <input type="checkbox" name="skills[]" value="{{ $skill->id }}" {{ in_array($skill->id, old('skills', $userSkillIds)) ? 'checked' : '' }} style="accent-color: var(--primary);">
                <span>{{ $skill->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <a href="{{ route('candidate.dashboard') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
            Quay lại
          </a>
          <button type="submit" class="btn-search" style="padding: 12px 28px; font-size: 0.95rem;">
            <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Lưu thay đổi
          </button>
        </div>
      </form>
    </div>

  </div>
</section>
@endsection
