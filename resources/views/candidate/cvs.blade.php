@extends('layouts.app')

@section('title', 'Quản Lý CV - JobConnect')

@section('content')
<section class="section">
  <div class="container" style="max-width: 900px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Quản Lý Hồ Sơ Tuyển Dụng (CV)
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Lưu trữ các phiên bản CV để ứng tuyển nhanh chóng vào các vị trí mong muốn
        </p>
      </div>

      <button type="button" onclick="document.getElementById('upload-cv-card').scrollIntoView({behavior: 'smooth'})" class="btn-search" style="padding: 10px 20px; font-size: 0.9rem;">
        <i class="fa-solid fa-cloud-arrow-up"></i> Tải lên CV mới
      </button>
    </div>

    <!-- CVs List -->
    <div style="margin-bottom: 36px;">
      <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
        Danh Sách CV Đã Lưu ({{ $cvs->count() }})
      </h3>

      @if($cvs->isEmpty())
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px 20px; text-align: center;">
          <i class="fa-regular fa-file-pdf" style="font-size: 3rem; color: var(--text-light); margin-bottom: 12px;"></i>
          <p style="color: var(--text-muted); margin-bottom: 0;">Bạn chưa có CV nào. Hãy sử dụng biểu mẫu bên dưới để tải lên CV đầu tiên của bạn!</p>
        </div>
      @else
        <div style="display: flex; flex-direction: column; gap: 16px;">
          @foreach($cvs as $cv)
            <div style="background: var(--bg-card); border: 1px solid {{ $cv->is_primary ? 'var(--primary)' : 'var(--border-color)' }}; border-radius: var(--radius-lg); padding: 20px 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
              <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                  <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div>
                  <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin: 0;">
                      {{ $cv->title }}
                    </h4>
                    @if($cv->is_primary)
                      <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: var(--radius-full);">
                        MẶC ĐỊNH
                      </span>
                    @endif
                  </div>
                  <span style="font-size: 0.82rem; color: var(--text-muted);">
                    Định dạng: {{ strtoupper($cv->file_type ?? 'PDF') }} • Cập nhật: {{ $cv->updated_at ? $cv->updated_at->format('d/m/Y') : '' }}
                  </span>
                </div>
              </div>

              <div style="display: flex; align-items: center; gap: 10px;">
                @if($cv->file_path)
                  <a href="{{ asset('storage/' . $cv->file_path) }}" target="_blank" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color); color: var(--primary);" title="Xem hoặc tải về CV">
                    <i class="fa-solid fa-download"></i> Xem CV
                  </a>
                @endif

                <!-- Sửa tên CV -->
                <button type="button" onclick="openEditCvModal('{{ $cv->id }}', '{{ addslashes($cv->title) }}')" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color);" title="Đổi tên hồ sơ">
                  <i class="fa-solid fa-pen-to-square"></i> Sửa tên
                </button>

                @if(!$cv->is_primary)
                  <form action="{{ route('candidate.cvs.primary', $cv->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-login" style="padding: 6px 12px; font-size: 0.82rem; background: #f8fafc; border: 1px solid var(--border-color);">
                      Đặt làm mặc định
                    </button>
                  </form>
                @endif

                <form action="{{ route('candidate.cvs.delete', $cv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa CV này?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" style="background: none; border: 1px solid #fecaca; color: #ef4444; border-radius: var(--radius-sm); padding: 6px 12px; font-size: 0.82rem; cursor: pointer;">
                    <i class="fa-solid fa-trash-can"></i> Xóa
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Upload New CV Form Card -->
    <div id="upload-cv-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
      <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
        <i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary); margin-right: 8px;"></i> Tải Lên CV Mới
      </h3>
      <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">Hỗ trợ định dạng PDF, DOC, DOCX. Kích thước tối đa 5MB.</p>

      <form action="{{ route('candidate.cvs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Tên hồ sơ / Chức danh chuyên môn <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="title" id="title" required placeholder="Ví dụ: CV Backend Developer - 2026"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="margin-bottom: 24px;">
          <label for="cv_file" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Chọn tệp tin CV <span style="color: #ef4444;">*</span>
          </label>
          <div style="border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 24px; text-align: center; background: #f8fafc;">
            <input type="file" name="cv_file" id="cv_file" required accept=".pdf,.doc,.docx" style="font-size: 0.9rem;">
          </div>
        </div>

        <button type="submit" class="btn-search" style="padding: 12px 28px; font-size: 0.95rem;">
          <i class="fa-solid fa-arrow-up-from-bracket" style="margin-right: 6px;"></i> Lưu hồ sơ CV
        </button>
      </form>
    </div>

  </div>
</section>

<!-- Edit CV Title Modal -->
<div id="edit-cv-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div style="background: var(--bg-card); border-radius: var(--radius-lg); width: 100%; max-width: 480px; margin: 20px; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 0;">Đổi Tên Hồ Sơ CV</h3>
      <button type="button" onclick="closeEditCvModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">&times;</button>
    </div>
    <form id="edit-cv-form" method="POST" style="padding: 24px;">
      @csrf
      @method('PUT')
      <div style="margin-bottom: 20px;">
        <label for="edit_cv_title" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
          Tên hồ sơ mới <span style="color: #ef4444;">*</span>
        </label>
        <input type="text" name="title" id="edit_cv_title" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeEditCvModal()" class="btn-login" style="padding: 8px 16px; background: #f8fafc; border: 1px solid var(--border-color);">Hủy</button>
        <button type="submit" class="btn-search" style="padding: 8px 20px;">Lưu thay đổi</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditCvModal(id, title) {
  const form = document.getElementById('edit-cv-form');
  form.action = "{{ url('/candidate/cvs') }}/" + id;
  document.getElementById('edit_cv_title').value = title;
  document.getElementById('edit-cv-modal').style.display = 'flex';
}
function closeEditCvModal() {
  document.getElementById('edit-cv-modal').style.display = 'none';
}
</script>
@endsection
