<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Bổ sung đủ 34 tỉnh thành vào bảng địa điểm, gộp Bình Dương vào TP. Hồ Chí Minh và
     * Nha Trang vào Khánh Hòa (kể cả tin tuyển dụng và tỉnh/thành của công ty).
     */
    public function up(): void
    {
        Location::syncProvinces();
    }

    /**
     * Không tách lại địa danh cũ vì không còn biết tin nào từng thuộc Bình Dương/Nha Trang.
     */
    public function down(): void
    {
        //
    }
};
