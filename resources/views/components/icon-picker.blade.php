@props(['name' => 'icon', 'idPrefix' => 'icon', 'selected' => 'fa-solid fa-briefcase'])

@php
  $icons = [
    'fa-solid fa-briefcase' => 'Chung',
    'fa-solid fa-code' => 'Công nghệ thông tin',
    'fa-solid fa-bullhorn' => 'Marketing',
    'fa-solid fa-chart-line' => 'Kinh doanh',
    'fa-solid fa-wallet' => 'Tài chính',
    'fa-solid fa-calculator' => 'Kế toán',
    'fa-solid fa-palette' => 'Thiết kế',
    'fa-solid fa-users-gear' => 'Nhân sự',
    'fa-solid fa-headset' => 'Chăm sóc khách hàng',
    'fa-solid fa-list-check' => 'Quản lý dự án',
    'fa-solid fa-truck-fast' => 'Logistics',
    'fa-solid fa-industry' => 'Sản xuất',
    'fa-solid fa-helmet-safety' => 'Xây dựng',
    'fa-solid fa-gears' => 'Cơ khí',
    'fa-solid fa-bolt' => 'Điện - điện tử',
    'fa-solid fa-stethoscope' => 'Y tế',
    'fa-solid fa-graduation-cap' => 'Giáo dục',
    'fa-solid fa-scale-balanced' => 'Luật',
    'fa-solid fa-building-columns' => 'Ngân hàng',
    'fa-solid fa-house' => 'Bất động sản',
    'fa-solid fa-utensils' => 'Nhà hàng - khách sạn',
    'fa-solid fa-plane' => 'Du lịch',
    'fa-solid fa-cart-shopping' => 'Bán lẻ',
    'fa-solid fa-language' => 'Biên phiên dịch',
    'fa-solid fa-camera' => 'Truyền thông - báo chí',
    'fa-solid fa-flask' => 'Khoa học',
    'fa-solid fa-seedling' => 'Nông nghiệp',
    'fa-solid fa-shield-halved' => 'An ninh - bảo vệ',
    'fa-solid fa-car' => 'Ô tô',
    'fa-solid fa-microchip' => 'Phần cứng',
  ];
  $selected = $selected ?: 'fa-solid fa-briefcase';
  if (! array_key_exists($selected, $icons)) {
    $icons = [$selected => 'Biểu tượng hiện tại'] + $icons;
  }
@endphp

<div class="icon-picker" role="radiogroup" id="{{ $idPrefix }}-picker" {{ $attributes }}>
  @foreach($icons as $iconClass => $label)
    <label title="{{ $label }}">
      <input type="radio" name="{{ $name }}" value="{{ $iconClass }}" aria-label="{{ $label }}" @checked($selected === $iconClass)>
      <i class="{{ $iconClass }}" aria-hidden="true"></i>
    </label>
  @endforeach
</div>
