@extends('layouts.app')

@section('title', 'Thông Tin Doanh Nghiệp - ' . $company->name)

@section('content')
<section class="section">
  <div class="container" style="max-width: 800px;">
    
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 36px 32px; box-shadow: var(--shadow-sm);">
      
      <div style="margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
          Hồ Sơ Doanh Nghiệp
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
          Cập nhật thông tin thương hiệu để tạo dựng niềm tin và thu hút nhân tài
        </p>
      </div>

      <form action="{{ route('employer.company.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
          <label for="name" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Tên công ty / Doanh nghiệp <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                 style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="city" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Tỉnh / Thành phố <span style="color: #ef4444;">*</span>
            </label>
            @php
              $cityOptions = config('provinces');
              $currentCity = old('city', $company->city);
              if ($currentCity && !in_array($currentCity, $cityOptions, true)) {
                array_unshift($cityOptions, $currentCity);
              }
            @endphp
            <select name="city" id="city" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              @foreach($cityOptions as $cityOpt)
                <option value="{{ $cityOpt }}" {{ $currentCity === $cityOpt ? 'selected' : '' }}>{{ $cityOpt }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="size" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Quy mô nhân sự
            </label>
            @php
              $sizeOptions = [
                '10-50 nhân viên' => '10 - 50 nhân viên',
                '50-100 nhân viên' => '50 - 100 nhân viên',
                '100-500 nhân viên' => '100 - 500 nhân viên',
                '500-1000 nhân viên' => '500 - 1000 nhân viên',
                '1000+ nhân viên' => '1000+ nhân viên',
              ];
              $currentSize = old('size', $company->size);
              if ($currentSize && !array_key_exists($currentSize, $sizeOptions)) {
                $sizeOptions = [$currentSize => $currentSize] + $sizeOptions;
              }
            @endphp
            <select name="size" id="size" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.92rem; outline: none; background: #ffffff;">
              @foreach($sizeOptions as $sizeVal => $sizeLabel)
                <option value="{{ $sizeVal }}" {{ $currentSize === $sizeVal ? 'selected' : '' }}>{{ $sizeLabel }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
          <div>
            <label for="address" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Địa chỉ trụ sở chính
            </label>
            <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}" placeholder="Ví dụ: Tòa nhà Bitexco, Q.1"
                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
          </div>

          <div>
            <label for="website" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
              Trang web công ty (Website)
            </label>
            <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com"
                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">
          </div>
        </div>

        <div style="margin-bottom: 28px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            Giới thiệu tổng quan về doanh nghiệp
          </label>
          <textarea name="description" id="description" rows="5" placeholder="Lịch sử hình thành, văn hóa doanh nghiệp, lĩnh vực hoạt động chính..."
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; outline: none;">{{ old('description', $company->description) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <a href="{{ route('employer.dashboard') }}" class="btn-login" style="background: #f8fafc; border: 1px solid var(--border-color);">
            Quay lại
          </a>
          <button type="submit" class="btn-search" style="padding: 12px 32px; font-size: 1rem;">
            <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Lưu hồ sơ
          </button>
        </div>
      </form>

    </div>

  </div>
</section>
@endsection
