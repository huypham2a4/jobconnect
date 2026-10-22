@extends('layouts.app')

@section('title', 'Quản Lý Ngành Nghề - Admin')

@section('content')
<section class="section">
  <div class="container">
    
    <div style="margin-bottom: 24px;">
      <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
        Quản Lý Danh Mục Ngành Nghề
      </h2>
      <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
        Danh mục ngành nghề phục vụ phân loại tin tuyển dụng và tìm kiếm trên hệ thống
      </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 28px; align-items: start;">
      
      <!-- Categories Table -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
          <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
              <th style="padding: 14px 20px;">Ngành nghề</th>
              <th style="padding: 14px 16px;">Đường dẫn (Slug)</th>
              <th style="padding: 14px 16px; text-align: center;">Số lượng tin</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categories as $cat)
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 16px 20px;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                      <i class="{{ $cat->icon ?? 'fa-solid fa-briefcase' }}"></i>
                    </div>
                    <div>
                      <strong style="color: var(--text-main); display: block;">{{ $cat->name }}</strong>
                      <span style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($cat->description, 60) }}</span>
                    </div>
                  </div>
                </td>

                <td style="padding: 16px; color: var(--text-muted); font-family: monospace;">
                  {{ $cat->slug }}
                </td>

                <td style="padding: 16px; text-align: center;">
                  <span style="font-weight: 700; color: var(--primary); background: #eff6ff; padding: 4px 12px; border-radius: var(--radius-full);">
                    {{ $cat->job_posts_count }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Add Category Form -->
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
          <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 6px;"></i> Thêm Danh Mục Mới
        </h3>

        <form action="{{ route('admin.categories.store') }}" method="POST">
          @csrf

          <div style="margin-bottom: 16px;">
            <label for="name" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Tên ngành nghề <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="name" required placeholder="Ví dụ: Logistics & Chuỗi cung ứng"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>

          <div style="margin-bottom: 16px;">
            <label for="icon" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              FontAwesome Icon Class
            </label>
            <input type="text" name="icon" id="icon" value="fa-solid fa-briefcase" placeholder="fa-solid fa-truck-fast"
                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
          </div>

          <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
              Mô tả ngắn
            </label>
            <textarea name="description" id="description" rows="3" placeholder="Mô tả ngành nghề này..."
                      style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;"></textarea>
          </div>

          <button type="submit" class="btn-search" style="width: 100%; justify-content: center; padding: 10px; font-size: 0.92rem;">
            <i class="fa-solid fa-plus" style="margin-right: 6px;"></i> Thêm mới
          </button>
        </form>
      </div>

    </div>

  </div>
</section>
@endsection
