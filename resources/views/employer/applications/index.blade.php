@extends('layouts.app')

@section('title', 'Quản Lý Hồ Sơ Ứng Viên - ' . $company->name)

@section('content')
<section class="section">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Danh Sách Ứng Viên Tuyển Dụng
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Sàng lọc CV, xem xét năng lực, bảo mật thông tin liên lạc và lên lịch phỏng vấn
        </p>
      </div>

      <!-- Quick Filter -->
      <form action="{{ route('employer.applications') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <select name="job_id" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; background: var(--bg-card); outline: none;">
          <option value="">Tất cả vị trí tuyển dụng</option>
          @foreach($companyJobs as $j)
            <option value="{{ $j->id }}" {{ $job_id == $j->id ? 'selected' : '' }}>{{ $j->title }}</option>
          @endforeach
        </select>

        <select name="status" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.88rem; background: var(--bg-card); outline: none;">
          <option value="">Tất cả trạng thái</option>
          <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Chưa duyệt (Mới nộp)</option>
          <option value="viewed" {{ request('status') === 'viewed' ? 'selected' : '' }}>Đã xem xét</option>
          <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Mời phỏng vấn</option>
          <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Trúng tuyển</option>
          <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Chưa phù hợp</option>
          <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Đã rút hồ sơ</option>
        </select>

        <a href="{{ route('employer.applications.export', array_filter(['job_id' => $job_id, 'status' => request('status')])) }}" class="btn-login" style="padding: 8px 14px; font-size: 0.85rem; border: 1px solid var(--border-color); display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-file-csv" style="color: #059669;"></i> Xuất CSV
        </a>
      </form>
    </div>

    <!-- Security Policy Alert Box -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px; color: #1e40af; font-size: 0.88rem;">
      <i class="fa-solid fa-shield-halved" style="font-size: 1.3rem;"></i>
      <div>
        <strong>Chính sách bảo mật hồ sơ:</strong> Số điện thoại và Email của ứng viên được ẩn khi ở trạng thái <em>"Đã nộp hồ sơ"</em>. Thông tin liên hệ sẽ tự động mở khóa ngay khi Nhà tuyển dụng thực hiện thao tác duyệt (Xem xét, Mời phỏng vấn, Trúng tuyển).
      </div>
    </div>

    @if($applications->isEmpty())
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center;">
        <i class="fa-solid fa-user-xmark" style="font-size: 3rem; color: var(--text-light); margin-bottom: 16px;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Chưa tìm thấy hồ sơ nào</h3>
        <p style="color: var(--text-muted); margin: 0;">Hiện tại chưa có ứng viên nào phù hợp với bộ lọc tìm kiếm đã chọn.</p>
      </div>
    @else
      <div style="display: flex; flex-direction: column; gap: 20px;">
        @foreach($applications as $app)
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
              <div style="display: flex; gap: 16px; align-items: center;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 700;">
                  {{ mb_substr($app->user->name, 0, 1) }}
                </div>
                <div>
                  <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px;">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">
                      {{ $app->user->name }}
                    </h3>
                    @if($app->match_analysis && $app->match_analysis['total_required'] > 0)
                      <span class="match-badge {{ $app->match_analysis['tier_class'] }}" title="Khớp {{ $app->match_analysis['matched']->count() }}/{{ $app->match_analysis['total_required'] }} kỹ năng yêu cầu">
                        Khớp {{ $app->match_analysis['score'] }}% kỹ năng
                      </span>
                    @endif
                  </div>
                  <div style="font-size: 0.88rem; color: var(--text-muted);">
                    Ứng tuyển vị trí: <a href="{{ route('jobs.show', $app->job_post_id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">{{ $app->jobPost->title }}</a>
                    • Nộp ngày {{ $app->applied_at ? $app->applied_at->format('d/m/Y H:i') : '' }}
                  </div>
                </div>
              </div>

              <!-- Status Action Form & Button -->
              <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                @if($app->status === 'withdrawn')
                  <span class="job-tag badge-gray" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 600;">
                    <i class="fa-solid fa-ban" style="margin-right: 4px;"></i> Đã rút hồ sơ
                  </span>
                @else
                  <form action="{{ route('employer.applications.status', $app->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <select name="status" onchange="this.form.submit()" style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; background: #f8fafc; outline: none;">
                      @if($app->status === 'submitted')
                        <option value="submitted" selected>Chưa duyệt</option>
                      @endif
                      <option value="viewed" {{ $app->status === 'viewed' ? 'selected' : '' }}>Đã xem xét</option>
                      @if($app->interview)
                        <option value="interview" {{ $app->status === 'interview' ? 'selected' : '' }}>Mời phỏng vấn</option>
                      @endif
                      <option value="accepted" {{ $app->status === 'accepted' ? 'selected' : '' }}>Trúng tuyển</option>
                      <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Chưa phù hợp</option>
                    </select>
                  </form>

                  @if(! in_array($app->status, ['accepted', 'rejected'], true))
                  <button type="button"
                          data-app-id="{{ $app->id }}"
                          data-candidate-name="{{ $app->user->name }}"
                          data-job-title="{{ $app->jobPost->title }}"
                          data-interview-date="{{ $app->interview?->interview_date?->format('Y-m-d\TH:i') ?? '' }}"
                          data-interview-type="{{ $app->interview?->interview_type ?? 'online' }}"
                          data-interview-location="{{ $app->interview?->location_or_link ?? '' }}"
                          data-interview-note="{{ $app->interview?->note ?? '' }}"
                          onclick="openInterviewModalFromBtn(this)"
                          class="btn-search" style="padding: 6px 14px; font-size: 0.82rem;">
                    <i class="fa-solid fa-calendar-plus" style="margin-right: 4px;"></i> {{ $app->interview ? 'Cập nhật lịch PV' : 'Lên lịch phỏng vấn' }}
                  </button>
                  @endif
                @endif
              </div>
            </div>

            <!-- Contact Details: Masked vs Unmasked according to Business Rule -->
            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; font-size: 0.9rem;">
              <div>
                <strong style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 2px;">SỐ ĐIỆN THOẠI:</strong>
                @if($app->isContactVisible())
                  <span style="color: var(--text-main); font-weight: 600;">
                    <i class="fa-solid fa-lock-open" style="color: #059669; margin-right: 6px;"></i>
                    {{ $app->user->phone ?? 'Chưa cập nhật' }}
                  </span>
                @else
                  <span style="color: #94a3b8; font-weight: 500;">
                    <i class="fa-solid fa-lock" style="color: #ef4444; margin-right: 6px;"></i>
                    {{ $app->user->phone ? Str::mask($app->user->phone, '*', 3) : 'Chưa cập nhật' }} <span style="font-size: 0.75rem; color: #f59e0b;">(Duyệt để xem)</span>
                  </span>
                @endif
              </div>

              <div>
                <strong style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 2px;">ĐỊA CHỈ EMAIL:</strong>
                @if($app->isContactVisible())
                  <span style="color: var(--text-main); font-weight: 600;">
                    <i class="fa-solid fa-lock-open" style="color: #059669; margin-right: 6px;"></i>
                    {{ $app->user->email }}
                  </span>
                @else
                  <span style="color: #94a3b8; font-weight: 500;">
                    <i class="fa-solid fa-lock" style="color: #ef4444; margin-right: 6px;"></i>
                    {{ Str::mask($app->user->email, '*', 3, 6) }} <span style="font-size: 0.75rem; color: #f59e0b;">(Duyệt để xem)</span>
                  </span>
                @endif
              </div>

              <div>
                <strong style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 2px;">TỆP HỒ SƠ (CV):</strong>
                @if($app->status === 'withdrawn')
                  <span style="color: var(--text-muted);">Ứng viên đã rút hồ sơ</span>
                @elseif($app->cv)
                  <a href="{{ route('cvs.download', $app->cv->id) }}" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                    <i class="fa-regular fa-file-pdf" style="margin-right: 4px;"></i> {{ $app->cv->title }}
                  </a>
                @else
                  <span style="color: var(--text-muted);">Không đính kèm tệp</span>
                @endif
              </div>
            </div>

            <!-- Skills of Candidate -->
            @if($app->user->skills->isNotEmpty())
              <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">KỸ NĂNG ỨNG VIÊN:</span>
                @foreach($app->user->skills as $sk)
                  <span class="job-tag" style="font-size: 0.78rem; padding: 2px 8px;">{{ $sk->name }}</span>
                @endforeach
              </div>
            @endif

            <!-- Cover Letter if any -->
            @if($app->cover_letter)
              <div style="background: #ffffff; border: 1px dashed var(--border-color); border-radius: var(--radius-sm); padding: 12px 16px; font-size: 0.88rem; color: var(--text-main); line-height: 1.6;">
                <strong style="color: var(--text-muted); display: block; margin-bottom: 4px; font-size: 0.8rem;">THƯ NGỎ ỨNG VIÊN:</strong>
                {{ $app->cover_letter }}
              </div>
            @endif

            <!-- Existing Interview Schedule Info -->
            @if($app->interview)
              <div style="margin-top: 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-sm); padding: 12px 16px; font-size: 0.88rem; color: #92400e; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                  <i class="fa-solid fa-calendar-check" style="color: #d97706; margin-right: 6px;"></i>
                  <strong>Lịch phỏng vấn đã xếp:</strong> {{ $app->interview->interview_date ? $app->interview->interview_date->format('H:i, d/m/Y') : '' }} ({{ $app->interview->interview_type === 'online' ? 'Online' : 'Offline' }})
                  • <em>{{ $app->interview->location_or_link }}</em>
                </div>
                <span style="font-size: 0.8rem; background: #ffffff; padding: 2px 8px; border-radius: var(--radius-full); border: 1px solid #fef3c7;">
                  {{ match($app->interview->status) { 'scheduled' => 'Đã lên lịch', 'completed' => 'Đã phỏng vấn', 'cancelled' => 'Đã hủy', default => $app->interview->status } }}
                </span>
              </div>
            @endif

          </div>
        @endforeach

        <div style="display: flex; justify-content: center; margin-top: 20px;">
          {{ $applications->links() }}
        </div>
      </div>
    @endif

  </div>
</section>

<!-- ==========================================================================
     INTERVIEW SCHEDULE MODAL
     ========================================================================== -->
<div id="interview-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div style="background: var(--bg-card); border-radius: var(--radius-lg); width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); overflow: hidden;">
    
    <div style="padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Gửi Lời Mời Phỏng Vấn</h3>
        <p id="modal-candidate-name" style="font-size: 0.85rem; color: var(--text-muted); margin: 2px 0 0;"></p>
      </div>
      <button type="button" onclick="closeInterviewModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">&times;</button>
    </div>

    <form id="interview-form" action="" method="POST" style="padding: 24px;">
      @csrf

      <div style="margin-bottom: 16px;">
        <label for="interview_date" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
          Thời gian phỏng vấn <span style="color: #ef4444;">*</span>
        </label>
        <input type="datetime-local" name="interview_date" id="interview_date" required
               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
      </div>

      <div style="margin-bottom: 16px;">
        <label for="interview_type" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
          Hình thức phỏng vấn <span style="color: #ef4444;">*</span>
        </label>
        <select name="interview_type" id="interview_type" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
          <option value="online">Trực tuyến qua Google Meet / Zoom</option>
          <option value="offline">Trực tiếp tại văn phòng công ty</option>
        </select>
      </div>

      <div style="margin-bottom: 16px;">
        <label for="location_or_link" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
          Đường dẫn Meet hoặc Địa chỉ văn phòng <span style="color: #ef4444;">*</span>
        </label>
        <input type="text" name="location_or_link" id="location_or_link" required placeholder="https://meet.google.com/abc-xyz hoặc Tầng 5..."
               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;">
      </div>

      <div style="margin-bottom: 24px;">
        <label for="note" style="display: block; font-weight: 600; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
          Ghi chú hoặc lời dặn ứng viên
        </label>
        <textarea name="note" id="note" rows="3" placeholder="Ví dụ: Vui lòng chuẩn bị máy tính, chuẩn bị bài tập kỹ thuật..."
                  style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeInterviewModal()" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
          Hủy
        </button>
        <button type="submit" class="btn-search" style="padding: 10px 24px; font-size: 0.9rem;">
          <i class="fa-solid fa-paper-plane" style="margin-right: 4px;"></i> Gửi lời mời
        </button>
      </div>
    </form>

  </div>
</div>

<script>
function openInterviewModalFromBtn(btn) {
  openInterviewModal(
    btn.dataset.appId,
    btn.dataset.candidateName,
    btn.dataset.jobTitle,
    btn.dataset.interviewDate || '',
    btn.dataset.interviewType || 'online',
    btn.dataset.interviewLocation || '',
    btn.dataset.interviewNote || ''
  );
}

function openInterviewModal(appId, candidateName, jobTitle, dateVal = '', typeVal = 'online', locVal = '', noteVal = '') {
  const modal = document.getElementById('interview-modal');
  const form = document.getElementById('interview-form');
  const modalTitle = document.getElementById('modal-candidate-name');

  form.action = "{{ url('/employer/applications') }}/" + appId + "/invite";
  modalTitle.innerText = "Ứng viên: " + candidateName + " • " + jobTitle;
  document.getElementById('interview_date').value = dateVal;
  document.getElementById('interview_type').value = typeVal;
  document.getElementById('location_or_link').value = locVal;
  document.getElementById('note').value = noteVal;
  modal.style.display = 'flex';
}

function closeInterviewModal() {
  document.getElementById('interview-modal').style.display = 'none';
}
</script>
@endsection
