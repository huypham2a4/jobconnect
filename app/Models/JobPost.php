<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    use HasFactory;

    protected $table = 'job_posts';

    protected $fillable = [
        'company_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'salary_negotiable',
        'type',
        'experience_level',
        'status',
        'rejection_note',
        'is_featured',
        'deadline',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'salary_negotiable' => 'boolean',
            'is_featured' => 'boolean',
            'deadline' => 'date',
            'views_count' => 'integer',
        ];
    }

    // Scopes
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('deadline', '>=', now()->toDateString());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->approved()->notExpired();
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_post_skill');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return [
            'full-time' => 'Toàn thời gian',
            'part-time' => 'Bán thời gian',
            'remote' => 'Từ xa',
            'internship' => 'Thực tập',
            'hybrid' => 'Kết hợp',
        ][$this->type] ?? (string) $this->type;
    }

    public function getSalaryTextAttribute(): string
    {
        if ($this->salary_negotiable || (! $this->salary_min && ! $this->salary_max)) {
            return 'Thỏa thuận';
        }

        if ($this->salary_min && $this->salary_max) {
            $min = round($this->salary_min / 1000000);
            $max = round($this->salary_max / 1000000);

            return "{$min} - {$max} Triệu";
        }

        if ($this->salary_min) {
            $min = round($this->salary_min / 1000000);

            return "Từ {$min} Triệu";
        }

        $max = round($this->salary_max / 1000000);

        return "Lên đến {$max} Triệu";
    }

    public function getDaysLeftTextAttribute(): string
    {
        if (! $this->deadline) {
            return '';
        }

        $today = Carbon::today();
        $deadline = Carbon::parse($this->deadline)->startOfDay();

        if ($today->gt($deadline)) {
            return 'Đã hết hạn';
        }

        $days = $today->diffInDays($deadline);
        if ($days === 0) {
            return 'Hạn chót hôm nay';
        }

        return "Còn {$days} ngày";
    }

    public function getIsExpiredAttribute(): bool
    {
        if (! $this->deadline) {
            return false;
        }

        return Carbon::today()->gt(Carbon::parse($this->deadline));
    }
}
