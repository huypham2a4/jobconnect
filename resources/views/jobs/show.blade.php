@extends('layouts.app')

@section('title', $job->title . ' - ' . ($job->company ? $job->company->name : 'JobConnect'))

@section('content')
<!-- Job Header Breadcrumb & Overview -->
<section style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 36px 0; color: #ffffff;">
  <div class="container">
    <div style="font-size: 0.88rem; color: #94a3b8; margin-bottom: 16px;">
      <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none;">Trang chủ</a>
      <span style="margin: 0 8px;">/</span>
      <a href="{{ route('jobs.index') }}" style="color: #94a3b8; text-decoration: none;">Việc làm</a>
      <span style="margin: 0 8px;">/</span>
      <span style="color: #cbd5e1;">{{ $job->title }}</span>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
      <div style="display: flex; gap: 20px; align-items: center;">
        <div style="width: 72px; height: 72px; border-radius: var(--radius-md); background: #2563eb; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
          {{ mb_substr($job->company ? $job->company->name : 'JC', 0, 2) }}
        </div>
        <div>
          <h1 style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: #ffffff; margin-bottom: 6px;">
            {{ $job->title }}
          </h1>
          <div style="display: flex; align-items: center; gap: 12px; color: #cbd5e1; font-size: 0.95rem;">
            <span>{{ $job->company ? $job->company->name : 'Doanh nghiệp ẩn danh' }}</span>
            @if($job->company && $job->company->is_verified)
              <i class="fa-solid fa-circle-check" style="color: #38bdf8;" title="Doanh nghiệp xác thực"></i>
            @endif
            <span>•</span>
            <span><i class="fa-solid fa-location-dot" style="margin-right: 4px; color: #f43f5e;"></i> {{ $job->location ? $job->location->name : 'Toàn quốc' }}</span>
          </div>
        </div>
      </div>

      <div style="display: flex; gap: 12px; align-items: center;">
        @if($hasApplied)
          <div style="padding: 10px 20px; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; border-radius: var(--radius-md); color: #34d399; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> Bạn đã ứng tuyển vị trí này
          </div>
        @else
          <button type="button" onclick="openApplyModal()" class="btn-search" style="padding: 12px 28px; font-size: 1rem; border-radius: var(--radius-md);">
            <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Ứng tuyển ngay
          </button>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Main Detail Content -->
<section class="section">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 32px; align-items: start;">
      
      <!-- Left Column: Details -->
      <div>
        <!-- Quick Meta Info Cards -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px;">
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
              <i class="fa-solid fa-dong-sign"></i>
            </div>
            <div>
              <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Mức lương</span>
              <strong style="color: #059669; font-size: 1.05rem;">{{ $job->salary_text }}</strong>
            </div>
          </div>

          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
              <i class="fa-solid fa-briefcase"></i>
            </div>
            <div>
              <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Hình thức làm việc</span>
              <strong style="color: var(--text-main); font-size: 1rem;">{{ ucfirst($job->type) }}</strong>
            </div>
          </div>

          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
              <i class="fa-regular fa-clock"></i>
            </div>
            <div>
              <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Hạn nộp hồ sơ</span>
              <strong style="color: #dc2626; font-size: 1rem;">{{ $job->days_left_text }}</strong>
            </div>
          </div>
        </div>

        <!-- Description Box -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
          <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
            <i class="fa-solid fa-file-lines" style="color: var(--primary); margin-right: 8px;"></i> Mô Tả Công Việc
          </h3>
          <div style="color: var(--text-main); line-height: 1.8; white-space: pre-line; font-size: 0.98rem;">
            {{ $job->description }}
          </div>

          @if($job->requirements)
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-top: 32px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
              <i class="fa-solid fa-list-check" style="color: var(--primary); margin-right: 8px;"></i> Yêu Cầu Ứng Viên
            </h3>
            <div style="color: var(--text-main); line-height: 1.8; white-space: pre-line; font-size: 0.98rem;">
              {{ $job->requirements }}
            </div>
          @endif

          @if($job->benefits)
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-top: 32px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
              <i class="fa-solid fa-gift" style="color: var(--primary); margin-right: 8px;"></i> Quyền Lợi & Đãi Ngộ
            </h3>
            <div style="color: var(--text-main); line-height: 1.8; white-space: pre-line; font-size: 0.98rem;">
              {{ $job->benefits }}
            </div>
          @endif

          <!-- Skills Needed -->
          <div style="margin-top: 32px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-muted); margin-bottom: 12px;">KỸ NĂNG YÊU CẦU:</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
              @foreach($job->skills as $sk)
                <span class="job-tag" style="font-size: 0.9rem; padding: 6px 14px;">{{ $sk->name }}</span>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Company Info & Related -->
      <div>
        <!-- Company Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
          <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 16px;">Về Doanh Nghiệp</h4>
          
          <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
            <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: #2563eb; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 800;">
              {{ mb_substr($job->company ? $job->company->name : 'JC', 0, 2) }}
            </div>
            <div>
              <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
                {{ $job->company ? $job->company->name : 'Doanh nghiệp' }}
              </h3>
              @if($job->company && $job->company->website)
                <a href="{{ $job->company->website }}" target="_blank" style="font-size: 0.85rem; color: var(--primary); text-decoration: none;">
                  <i class="fa-solid fa-arrow-up-right-from-square"></i> Ghé thăm website
                </a>
              @endif
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 16px;">
            @if($job->company && $job->company->size)
              <div><i class="fa-solid fa-users" style="width: 20px; color: var(--text-light);"></i> {{ $job->company->size }}</div>
            @endif
            @if($job->company && $job->company->address)
              <div><i class="fa-solid fa-location-dot" style="width: 20px; color: var(--text-light);"></i> {{ $job->company->address }}</div>
            @endif
          </div>

          @if($job->company && $job->company->description)
            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; border-top: 1px solid var(--border-color); padding-top: 12px; margin: 0;">
              {{ Str::limit($job->company->description, 160) }}
            </p>
          @endif
        </div>

        <!-- Related Jobs -->
        @if($relatedJobs->isNotEmpty())
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">Việc Làm Tương Tự</h4>
            <div style="display: flex; flex-direction: column; gap: 14px;">
              @foreach($relatedJobs as $rJob)
                <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                  <a href="{{ route('jobs.show', $rJob->id) }}" style="text-decoration: none;">
                    <h5 style="font-size: 0.92rem; font-weight: 600; color: var(--text-main); margin-bottom: 4px; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-main)'">
                      {{ $rJob->title }}
                    </h5>
                  </a>
                  <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted);">
                    <span>{{ $rJob->company ? $rJob->company->name : '' }}</span>
                    <strong style="color: #059669;">{{ $rJob->salary_text }}</strong>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>

    </div>
  </div>
</section>

<!-- ==========================================================================
     APPLY MODAL
     ========================================================================== -->
<div id="apply-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div style="background: var(--bg-card); border-radius: var(--radius-lg); width: 100%; max-width: 540px; margin: 20px; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); overflow: hidden; animation: fadeInModal 0.2s ease-out;">
    
    <div style="padding: 20px 24px; background: #f8fafc; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Nộp Hồ Sơ Ứng Tuyển</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin: 2px 0 0;">{{ $job->title }}</p>
      </div>
      <button type="button" onclick="closeApplyModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">&times;</button>
    </div>

    @auth
      @if(Auth::user()->isCandidate())
        <form action="{{ route('jobs.apply', $job->id) }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
          @csrf

          <!-- Chọn CV -->
          <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 10px;">
              Chọn hồ sơ ứng tuyển (CV) <span style="color: #ef4444;">*</span>
            </label>

            @if($userCvs->isNotEmpty())
              <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 0.88rem; cursor: pointer;">
                <input type="radio" name="cv_option" value="existing" checked onchange="toggleCvInput()" style="accent-color: var(--primary);">
                Sử dụng CV có sẵn trong tài khoản
              </label>

              <div id="existing-cv-box" style="margin-bottom: 14px; padding-left: 24px;">
                <select name="existing_cv_id" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.9rem; outline: none;">
                  @foreach($userCvs as $cv)
                    <option value="{{ $cv->id }}" {{ $cv->is_primary ? 'selected' : '' }}>
                      {{ $cv->title }} {{ $cv->is_primary ? '(Mặc định)' : '' }}
                    </option>
                  @endforeach
                </select>
              </div>

              <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 0.88rem; cursor: pointer;">
                <input type="radio" name="cv_option" value="new" onchange="toggleCvInput()" style="accent-color: var(--primary);">
                Tải lên CV mới từ máy tính (.pdf, .doc, .docx)
              </label>

              <div id="new-cv-box" style="display: none; padding-left: 24px; margin-bottom: 14px;">
                <input type="file" name="cv_file" accept=".pdf,.doc,.docx" style="font-size: 0.88rem;">
              </div>
            @else
              <input type="hidden" name="cv_option" value="new">
              <div style="background: #f8fafc; border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 20px; text-align: center;">
                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: var(--primary); margin-bottom: 8px;"></i>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 10px;">Bạn chưa lưu CV nào trên hệ thống. Hãy tải lên tệp CV của bạn:</p>
                <input type="file" name="cv_file" required accept=".pdf,.doc,.docx" style="font-size: 0.88rem;">
              </div>
            @endif
          </div>

          <!-- Cover letter -->
          <div style="margin-bottom: 24px;">
            <label for="cover_letter" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 6px;">
              Thư ngỏ / Giới thiệu ngắn về bản thân (Tùy chọn)
            </label>
            <textarea name="cover_letter" id="cover_letter" rows="4" placeholder="Nêu lý do bạn phù hợp với công việc này và kinh nghiệm liên quan..."
                      style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.9rem; outline: none;"></textarea>
          </div>

          <!-- Submit Buttons -->
          <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeApplyModal()" class="btn-login" style="border: 1px solid var(--border-color); background: #f8fafc;">
              Hủy
            </button>
            <button type="submit" class="btn-search" style="padding: 10px 24px;">
              <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Xác nhận nộp hồ sơ
            </button>
          </div>
        </form>
      @else
        <div style="padding: 32px 24px; text-align: center;">
          <i class="fa-solid fa-circle-exclamation" style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 12px;"></i>
          <p style="color: var(--text-main); font-weight: 600; margin-bottom: 6px;">Bạn đang đăng nhập với tài khoản {{ Auth::user()->role === 'employer' ? 'Nhà tuyển dụng' : 'Quản trị viên' }}.</p>
          <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">Vui lòng chuyển sang tài khoản Ứng viên để thực hiện nộp hồ sơ.</p>
          <button type="button" onclick="closeApplyModal()" class="btn-search" style="padding: 8px 20px;">Đã hiểu</button>
        </div>
      @endif
    @else
      <div style="padding: 32px 24px; text-align: center;">
        <i class="fa-solid fa-lock" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 12px;"></i>
        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Yêu Cầu Đăng Nhập</h4>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Bạn cần đăng nhập tài khoản Ứng viên để gửi CV trực tiếp đến nhà tuyển dụng.</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
          <a href="{{ route('login') }}" class="btn-search" style="padding: 10px 24px;">Đăng nhập ngay</a>
          <a href="{{ route('register') }}" class="btn-login" style="background: #f1f5f9; border: 1px solid var(--border-color);">Đăng ký tài khoản</a>
        </div>
      </div>
    @endauth

  </div>
</div>

<script>
function openApplyModal() {
  const modal = document.getElementById('apply-modal');
  modal.style.display = 'flex';
}

function closeApplyModal() {
  const modal = document.getElementById('apply-modal');
  modal.style.display = 'none';
}

function toggleCvInput() {
  const cvOption = document.querySelector('input[name="cv_option"]:checked').value;
  const existingBox = document.getElementById('existing-cv-box');
  const newBox = document.getElementById('new-cv-box');

  if (existingBox && newBox) {
    if (cvOption === 'existing') {
      existingBox.style.display = 'block';
      newBox.style.display = 'none';
    } else {
      existingBox.style.display = 'none';
      newBox.style.display = 'block';
    }
  }
}
</script>
@endsection
