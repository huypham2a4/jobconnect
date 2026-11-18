<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Support\Collection;

class JobMatchingService
{
    /**
     * Calculate skill compatibility between a candidate and a job post.
     *
     * @return array{
     *     score: int,
     *     matched: Collection<int, \App\Models\Skill>,
     *     missing: Collection<int, \App\Models\Skill>,
     *     total_required: int,
     *     tier: string,
     *     tier_class: string
     * }
     */
    public function analyze(User $candidate, JobPost $job): array
    {
        $candidateSkills = $candidate->relationLoaded('skills')
            ? $candidate->skills
            : $candidate->skills()->get();

        $jobSkills = $job->relationLoaded('skills')
            ? $job->skills
            : $job->skills()->get();

        $candidateSkillIds = $candidateSkills->pluck('id')->flip();
        $totalRequired = $jobSkills->count();

        if ($totalRequired === 0) {
            return [
                'score' => $candidateSkills->isNotEmpty() ? 100 : 0,
                'matched' => collect(),
                'missing' => collect(),
                'total_required' => 0,
                'tier' => $candidateSkills->isNotEmpty() ? 'Sẵn sàng ứng tuyển' : 'Chưa cập nhật kỹ năng',
                'tier_class' => $candidateSkills->isNotEmpty() ? 'match-badge--mid' : 'match-badge--low',
            ];
        }

        $matched = $jobSkills->filter(fn ($skill) => $candidateSkillIds->has($skill->id))->values();
        $missing = $jobSkills->reject(fn ($skill) => $candidateSkillIds->has($skill->id))->values();
        $score = (int) round(($matched->count() / $totalRequired) * 100);

        [$tier, $tierClass] = match (true) {
            $score >= 75 => ['Rất phù hợp', 'match-badge--high'],
            $score >= 45 => ['Phù hợp', 'match-badge--mid'],
            $score > 0 => ['Có liên quan', 'match-badge--soft'],
            default => ['Cần bổ sung kỹ năng', 'match-badge--low'],
        };

        return [
            'score' => $score,
            'matched' => $matched,
            'missing' => $missing,
            'total_required' => $totalRequired,
            'tier' => $tier,
            'tier_class' => $tierClass,
        ];
    }

    /**
     * Recommend top active job posts ranked by candidate skill match.
     *
     * @return Collection<int, array{job: JobPost, analysis: array}>
     */
    public function recommendForCandidate(User $candidate, int $limit = 4): Collection
    {
        $candidate->loadMissing('skills');

        if ($candidate->skills->isEmpty()) {
            return collect();
        }

        $candidateSkillIds = $candidate->skills->pluck('id')->all();

        $jobs = JobPost::with(['company', 'category', 'location', 'skills'])
            ->active()
            ->whereHas('skills', fn ($q) => $q->whereIn('skills.id', $candidateSkillIds))
            ->latest()
            ->take(40)
            ->get();

        return $jobs->map(fn (JobPost $job) => [
            'job' => $job,
            'analysis' => $this->analyze($candidate, $job),
        ])
            ->filter(fn (array $item) => $item['analysis']['matched']->isNotEmpty())
            ->sortByDesc(fn (array $item) => ($item['analysis']['score'] * 1000) + ($item['job']->is_featured ? 100 : 0) + $item['analysis']['matched']->count())
            ->take($limit)
            ->values();
    }
}
