@extends('layouts.app')

@section('title', 'Đăng Tin Tuyển Dụng Mới - ' . $company->name)

@section('content')
<section class="section">
  <div class="container" style="max-width: 900px;">
    
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-sm);">
      
      <div style="margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Đăng Tin Tuyển Dụng Mới
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Doanh nghiệp: <strong>{{ $company->name }}</strong>
        </p>
      </div>

      

      <p class="moderation-note"><i class="fa-solid fa-shield-halved"></i> Tin mới hoặc nội dung chỉnh sửa cần được Admin phê duyệt trước khi hiển thị công khai.</p>
      <form action="{{ route('employer.jobs.store') }}" method="POST">
        @csrf

        <!-- Tiêu đề -->
        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Tiêu đề vị trí tuyển dụng <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Ví dụ: Senior Backend Engineer (Laravel, Node.js)"
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <!-- Ngành nghề & Địa điểm -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="category_id" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Ngành nghề <span style="color: #ef4444;">*</span>
            </label>
            <select name="category_id" id="category_id" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="">-- Chọn ngành nghề --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="location_id" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Khu vực làm việc <span style="color: #ef4444;">*</span>
            </label>
            <select name="location_id" id="location_id" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="">-- Chọn địa điểm --</option>
              @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Hình thức & Cấp bậc -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="type" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Hình thức làm việc <span style="color: #ef4444;">*</span>
            </label>
            <select name="type" id="type" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="full-time" {{ old('type') === 'full-time' ? 'selected' : '' }}>Toàn thời gian (Full-time)</option>
              <option value="part-time" {{ old('type') === 'part-time' ? 'selected' : '' }}>Bán thời gian (Part-time)</option>
              <option value="remote" {{ old('type') === 'remote' ? 'selected' : '' }}>Từ xa (Remote)</option>
              <option value="internship" {{ old('type') === 'internship' ? 'selected' : '' }}>Thực tập sinh (Internship)</option>
              <option value="hybrid" {{ old('type') === 'hybrid' ? 'selected' : '' }}>Linh hoạt (Hybrid)</option>
            </select>
          </div>

          <div>
            <label for="experience_level" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Cấp bậc yêu cầu <span style="color: #ef4444;">*</span>
            </label>
            <select name="experience_level" id="experience_level" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="middle" {{ old('experience_level') === 'middle' ? 'selected' : '' }}>Middle (1 - 3 năm)</option>
              <option value="junior" {{ old('experience_level') === 'junior' ? 'selected' : '' }}>Junior (Dưới 1 năm)</option>
              <option value="fresher" {{ old('experience_level') === 'fresher' ? 'selected' : '' }}>Fresher / Mới tốt nghiệp</option>
              <option value="intern" {{ old('experience_level') === 'intern' ? 'selected' : '' }}>Thực tập sinh</option>
              <option value="senior" {{ old('experience_level') === 'senior' ? 'selected' : '' }}>Senior (3 - 5 năm)</option>
              <option value="lead" {{ old('experience_level') === 'lead' ? 'selected' : '' }}>Trưởng nhóm / Lead</option>
              <option value="all" {{ old('experience_level') === 'all' ? 'selected' : '' }}>Không yêu cầu kinh nghiệm</option>
            </select>
          </div>
        </div>

        <!-- Mức lương & Hạn nộp -->
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 14px;">
            <i class="fa-solid fa-coins" style="color: #d97706; margin-right: 6px;"></i> Chế Độ Lương & Hạn Nộp
          </h4>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; align-items: end; margin-bottom: 12px;">
            <div>
              <label for="salary_min" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Lương tối thiểu (Triệu VNĐ)
              </label>
              <input type="number" step="0.5" name="salary_min" id="salary_min" value="{{ old('salary_min') }}" placeholder="Ví dụ: 15"
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>

            <div>
              <label for="salary_max" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Lương tối đa (Triệu VNĐ)
              </label>
              <input type="number" step="0.5" name="salary_max" id="salary_max" value="{{ old('salary_max') }}" placeholder="Ví dụ: 25"
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>

            <div>
              <label for="deadline" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Hạn chót nộp hồ sơ <span style="color: #ef4444;">*</span>
              </label>
              <input type="date" name="deadline" id="deadline" value="{{ old('deadline', now()->addDays(30)->toDateString()) }}" required
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>
          </div>

          <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); cursor: pointer;">
            <input type="checkbox" name="salary_negotiable" value="1" {{ old('salary_negotiable') ? 'checked' : '' }} style="accent-color: var(--primary);">
            Mức lương có thể thỏa thuận trực tiếp khi phỏng vấn
          </label>
        </div>

        <!-- Kỹ năng yêu cầu (Job Skills) -->
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
            <i class="fa-solid fa-code" style="color: var(--primary); margin-right: 6px;"></i> Kỹ Năng Yêu Cầu (Job Skills)
          </h4>
          <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Hệ thống sẽ dùng các kỹ năng này để gợi ý ứng viên phù hợp:</p>

          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
            @foreach($skills as $skill)
              <label style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="skills[]" value="{{ $skill->id }}" {{ in_array($skill->id, old('skills', [])) ? 'checked' : '' }} style="accent-color: var(--primary);">
                <span>{{ $skill->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <!-- Chi tiết mô tả -->
        <div style="margin-bottom: 20px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Mô tả công việc <span style="color: #ef4444;">*</span>
          </label>
          <textarea name="description" id="description" rows="5" required placeholder="Nêu rõ trách nhiệm công việc hàng ngày, dự án tham gia..."
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
          <label for="requirements" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Yêu cầu ứng viên
          </label>
          <textarea name="requirements" id="requirements" rows="4" placeholder="Kinh nghiệm cần có, bằng cấp, tinh thần làm việc..."
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('requirements') }}</textarea>
        </div>

        <div style="margin-bottom: 28px;">
          <label for="benefits" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Quyền lợi & Chế độ đãi ngộ
          </label>
          <textarea name="benefits" id="benefits" rows="4" placeholder="Thưởng hiệu suất, bảo hiểm sức khỏe, máy tính làm việc, du lịch hàng năm..."
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('benefits') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <a href="{{ route('employer.jobs.index') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
            Hủy
          </a>
          <button type="submit" class="btn-search" style="padding: 12px 32px; font-size: 1rem;">
            <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Gửi Admin duyệt
          </button>
        </div>
      </form>

    </div>

  </div>
</section>
@endsection
