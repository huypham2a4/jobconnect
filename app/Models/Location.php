<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    public const REMOTE_NAME = 'Làm việc từ xa (Remote)';

    public const REMOTE_SLUG = 'remote';

    /**
     * Slug ngắn đã dùng từ trước (đường dẫn lọc /jobs?location=hcm vẫn chạy).
     *
     * @var array<string, string>
     */
    private const LEGACY_SLUGS = [
        'TP. Hồ Chí Minh' => 'hcm',
        'Hà Nội' => 'hn',
        'Đà Nẵng' => 'dn',
    ];

    /**
     * Địa danh cũ không còn là tỉnh riêng sau sắp xếp 01/07/2025 => tỉnh mới tương ứng.
     *
     * @var array<string, string>
     */
    public const MERGED_INTO = [
        'Bình Dương' => 'TP. Hồ Chí Minh',
        'Nha Trang' => 'Khánh Hòa',
    ];

    protected $fillable = [
        'name',
        'slug',
    ];

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    public static function slugForProvince(string $name): string
    {
        return self::LEGACY_SLUGS[$name] ?? Str::slug($name);
    }

    /**
     * Bảo đảm bảng địa điểm có đủ 34 tỉnh thành + "Làm việc từ xa", rồi chuyển tin và công ty
     * đang gắn với địa danh cũ (Bình Dương, Nha Trang) sang tỉnh mới. Chạy lại nhiều lần vẫn an toàn.
     */
    public static function syncProvinces(): void
    {
        DB::transaction(function (): void {
            foreach ([...config('provinces'), self::REMOTE_NAME] as $name) {
                $slug = $name === self::REMOTE_NAME ? self::REMOTE_SLUG : self::slugForProvince($name);
                self::firstOrCreate(['name' => $name], ['slug' => $slug]);
            }

            foreach (self::MERGED_INTO as $oldName => $newName) {
                $newLocation = self::where('name', $newName)->firstOrFail();
                $oldLocation = self::where('name', $oldName)->first();

                if ($oldLocation) {
                    JobPost::where('location_id', $oldLocation->id)->update(['location_id' => $newLocation->id]);
                    $oldLocation->delete();
                }

                Company::where('city', $oldName)->update(['city' => $newName]);
            }
        });
    }

    /**
     * Danh sách cho ô chọn: 34 tỉnh thành theo đúng thứ tự trong config, "Làm việc từ xa" ở cuối,
     * địa điểm do người khác thêm (nếu có) xếp sau cùng.
     *
     * @return Collection<int, Location>
     */
    public static function orderedForSelect(): Collection
    {
        $order = array_flip([...config('provinces'), self::REMOTE_NAME]);

        return self::all()
            ->sortBy(fn (Location $location) => [$order[$location->name] ?? PHP_INT_MAX, $location->name])
            ->values();
    }
}
