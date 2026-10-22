<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id',
        'user_id',
        'cv_id',
        'cover_letter',
        'status',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }

    // Rules
    public function canWithdraw(): bool
    {
        return $this->status === 'submitted';
    }

    public function isContactVisible(): bool
    {
        // Ẩn SĐT/Email ứng viên cho đến khi nhà tuyển dụng xử lý hồ sơ (khác submitted)
        return in_array($this->status, ['viewed', 'interview', 'accepted']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'Đã nộp hồ sơ',
            'viewed' => 'NTD đã xem',
            'interview' => 'Mời phỏng vấn',
            'accepted' => 'Đã trúng tuyển',
            'rejected' => 'Chưa phù hợp',
            'withdrawn' => 'Đã rút hồ sơ',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'badge-blue',
            'viewed' => 'badge-purple',
            'interview' => 'badge-amber',
            'accepted' => 'badge-emerald',
            'rejected' => 'badge-rose',
            'withdrawn' => 'badge-gray',
            default => 'badge-gray',
        };
    }
}
