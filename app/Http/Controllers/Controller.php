<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class Controller
{
    /**
     * Tạo slug không trùng trong bảng của model (cột slug có ràng buộc unique).
     *
     * Cách cũ đếm số slug "giống" rồi cộng thêm số thứ tự nên có thể sinh trùng
     * (ví dụ sau khi xóa bớt bản ghi), gây lỗi 500 do vi phạm unique.
     *
     * @param  class-string<Model>  $modelClass
     */
    protected function uniqueSlug(string $modelClass, string $source, ?int $ignoreId = null, string $fallback = 'muc'): string
    {
        $base = Str::slug($source) ?: $fallback;
        $base = Str::limit($base, 200, '');
        $slug = $base;
        $suffix = 2;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
