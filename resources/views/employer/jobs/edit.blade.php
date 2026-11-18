@extends('layouts.app')

@section('title', 'Chỉnh Sửa Tin - ' . $job->title)

@section('content')
<section class="section">
  <div class="container" style="max-width: 900px;">
    
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-sm);">
      
      <div style="margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
            Chỉnh Sửa Tin Tuyển Dụng
          </h2>
          <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
            {{ $job->title }}
          </p>
        </div>

        <span class="job-tag" style="font-size: 0.82rem; padding: 6px 14px;">
          Trạng thái: {{ ucfirst($job->status) }}
        </span>
      </div>

      <p class="moderation-note"><i class="fa-solid fa-shield-halved"></i> Tin mới hoặc nội dung chỉnh sửa cần được Admin phê duyệt trước khi hiển thị công khai.</p>
      <form action="{{ route('employer.jobs.update', $job->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Tiêu đề vị trí tuyển dụng <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="title" id="title" value="{{ old('title', $job->title) }}" required
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="category_id" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Ngành nghề <span style="color: #ef4444;">*</span>
            </label>
            <select name="category_id" id="category_id" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $job->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="location_id" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Khu vực làm việc <span style="color: #ef4444;">*</span>
            </label>
            <select name="location_id" id="location_id" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ old('location_id', $job->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="type" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Hình thức làm việc <span style="color: #ef4444;">*</span>
            </label>
            <select name="type" id="type" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="full-time" {{ old('type', $job->type) === 'full-time' ? 'selected' : '' }}>Toàn thời gian (Full-time)</option>
              <option value="part-time" {{ old('type', $job->type) === 'part-time' ? 'selected' : '' }}>Bán thời gian (Part-time)</option>
              <option value="remote" {{ old('type', $job->type) === 'remote' ? 'selected' : '' }}>Từ xa (Remote)</option>
              <option value="internship" {{ old('type', $job->type) === 'internship' ? 'selected' : '' }}>Thực tập sinh (Internship)</option>
              <option value="hybrid" {{ old('type', $job->type) === 'hybrid' ? 'selected' : '' }}>Linh hoạt (Hybrid)</option>
            </select>
          </div>

          <div>
            <label for="experience_level" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Cấp bậc yêu cầu <span style="color: #ef4444;">*</span>
            </label>
            <select name="experience_level" id="experience_level" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              <option value="middle" {{ old('experience_level', $job->experience_level) === 'middle' ? 'selected' : '' }}>Middle (1 - 3 năm)</option>
              <option value="junior" {{ old('experience_level', $job->experience_level) === 'junior' ? 'selected' : '' }}>Junior (Dưới 1 năm)</option>
              <option value="fresher" {{ old('experience_level', $job->experience_level) === 'fresher' ? 'selected' : '' }}>Fresher / Mới tốt nghiệp</option>
              <option value="intern" {{ old('experience_level', $job->experience_level) === 'intern' ? 'selected' : '' }}>Thực tập sinh</option>
              <option value="senior" {{ old('experience_level', $job->experience_level) === 'senior' ? 'selected' : '' }}>Senior (3 - 5 năm)</option>
              <option value="lead" {{ old('experience_level', $job->experience_level) === 'lead' ? 'selected' : '' }}>Trưởng nhóm / Lead</option>
              <option value="all" {{ old('experience_level', $job->experience_level) === 'all' ? 'selected' : '' }}>Không yêu cầu kinh nghiệm</option>
            </select>
          </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; align-items: end; margin-bottom: 12px;">
            <div>
              <label for="salary_min" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Lương tối thiểu (Triệu VNĐ)
              </label>
              <input type="number" step="0.5" name="salary_min" id="salary_min" value="{{ old('salary_min', $job->salary_min ? $job->salary_min / 1000000 : '') }}"
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>

            <div>
              <label for="salary_max" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Lương tối đa (Triệu VNĐ)
              </label>
              <input type="number" step="0.5" name="salary_max" id="salary_max" value="{{ old('salary_max', $job->salary_max ? $job->salary_max / 1000000 : '') }}"
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>

            <div>
              <label for="deadline" style="display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-main); margin-bottom: 6px;">
                Hạn chót nộp hồ sơ <span style="color: #ef4444;">*</span>
              </label>
              <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $job->deadline ? $job->deadline->toDateString() : '') }}" required
                     style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
            </div>
          </div>

          <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); cursor: pointer;">
            <input type="checkbox" name="salary_negotiable" value="1" {{ old('salary_negotiable', $job->salary_negotiable) ? 'checked' : '' }} style="accent-color: var(--primary);">
            Mức lương có thể thỏa thuận trực tiếp khi phỏng vấn
          </label>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">
            <i class="fa-solid fa-code" style="color: var(--primary); margin-right: 6px;"></i> Kỹ Năng Yêu Cầu
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
            @foreach($skills as $skill)
              <label style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="skills[]" value="{{ $skill->id }}" {{ in_array($skill->id, old('skills', $jobSkillIds)) ? 'checked' : '' }} style="accent-color: var(--primary);">
                <span>{{ $skill->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Mô tả công việc <span style="color: #ef4444;">*</span>
          </label>
          <textarea name="description" id="description" rows="5" required
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('description', $job->description) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
          <label for="requirements" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Yêu cầu ứng viên
          </label>
          <textarea name="requirements" id="requirements" rows="4"
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('requirements', $job->requirements) }}</textarea>
        </div>

        <div style="margin-bottom: 28px;">
          <label for="benefits" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Quyền lợi & Chế độ đãi ngộ
          </label>
          <textarea name="benefits" id="benefits" rows="4"
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('benefits', $job->benefits) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <a href="{{ route('employer.jobs.index') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
            Quay lại
          </a>
          <button type="submit" class="btn-search" style="padding: 12px 32px; font-size: 1rem;">
            <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Lưu và gửi duyệt lại
          </button>
        </div>
      </form>

    </div>

  </div>
</section>
@endsection
