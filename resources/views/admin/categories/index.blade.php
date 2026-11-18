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
              <th style="padding: 14px 20px; text-align: right;">Hành động</th>
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

                <td style="padding: 16px 20px; text-align: right;">
                  <div style="display: inline-flex; gap: 6px; align-items: center;">
                    <button type="button" class="btn-login edit-cat-btn"
                            data-id="{{ $cat->id }}"
                            data-name="{{ $cat->name }}"
                            data-icon="{{ $cat->icon }}"
                            data-description="{{ $cat->description }}"
                            style="padding: 6px 10px; font-size: 0.8rem; border: 1px solid var(--border-color);">
                      <i class="fa-solid fa-pen-to-square"></i> Sửa
                    </button>
                    @if($cat->job_posts_count === 0)
                      <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xóa ngành nghề này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-login" style="padding: 6px 10px; font-size: 0.8rem; background: #fff1f2; border: 1px solid #fecaca; color: #e11d48;">
                          <i class="fa-solid fa-trash-can"></i> Xóa
                        </button>
                      </form>
                    @endif
                  </div>
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
            <span style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 8px;">
              Biểu tượng
            </span>
            <x-icon-picker name="icon" id-prefix="new-icon" :selected="old('icon', 'fa-solid fa-briefcase')" />
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

<div id="edit-cat-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div style="background: var(--bg-card); border-radius: var(--radius-lg); width: 100%; max-width: 460px; margin: 20px; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 0;">Cập Nhật Ngành Nghề</h3>
      <button type="button" onclick="closeEditCatModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">&times;</button>
    </div>
    <form id="edit-cat-form" method="POST" style="padding: 24px;">
      @csrf
      @method('PUT')
      <div style="margin-bottom: 16px;">
        <label for="edit_cat_name" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">Tên ngành nghề <span style="color:#ef4444;">*</span></label>
        <input type="text" name="name" id="edit_cat_name" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
      </div>
      <div style="margin-bottom: 16px;">
        <span style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 8px;">Biểu tượng</span>
        <x-icon-picker name="icon" id-prefix="edit-icon" />
      </div>
      <div style="margin-bottom: 20px;">
        <label for="edit_cat_desc" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">Mô tả ngắn</label>
        <textarea name="description" id="edit_cat_desc" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;"></textarea>
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeEditCatModal()" class="btn-login" style="padding: 8px 16px; border: 1px solid var(--border-color);">Hủy</button>
        <button type="submit" class="btn-search" style="padding: 8px 18px;">Lưu cập nhật</button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.edit-cat-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit-cat-form').action = "{{ url('/admin/categories') }}/" + btn.dataset.id;
    document.getElementById('edit_cat_name').value = btn.dataset.name || '';
    selectEditIcon(btn.dataset.icon || 'fa-solid fa-briefcase');
    document.getElementById('edit_cat_desc').value = btn.dataset.description || '';
    document.getElementById('edit-cat-modal').style.display = 'flex';
  });
});
function selectEditIcon(iconClass) {
  const picker = document.getElementById('edit-icon-picker');
  let radio = Array.from(picker.querySelectorAll('input[type="radio"]')).find(input => input.value === iconClass);
  if (!radio) {
    // Biểu tượng cũ không có trong bộ chọn: thêm tạm vào đầu danh sách để không bị mất khi lưu.
    picker.querySelector('[data-custom-icon]')?.remove();
    const label = document.createElement('label');
    label.dataset.customIcon = 'true';
    label.title = 'Biểu tượng hiện tại';
    radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = 'icon';
    radio.value = iconClass;
    radio.setAttribute('aria-label', 'Biểu tượng hiện tại');
    const icon = document.createElement('i');
    icon.className = iconClass;
    label.append(radio, icon);
    picker.prepend(label);
  }
  radio.checked = true;
}
function closeEditCatModal() {
  document.getElementById('edit-cat-modal').style.display = 'none';
}
</script>
@endsection
