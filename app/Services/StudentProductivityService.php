<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class StudentProductivityService
{
    protected string $datasetPath = 'datasets/ultimate_student_productivity_dataset_5000.csv';

    public function getAvailableFilters(): array
    {
        $rows = $this->loadRows();

        return [
            'academic_levels' => $rows->pluck('academic_level')->filter()->unique()->sort()->values()->all(),
            'genders' => $rows->pluck('gender')->filter()->unique()->sort()->values()->all(),
            'internet_qualities' => $rows->pluck('internet_quality')->filter()->unique()->sort()->values()->all(),
        ];
    }

    public function getDashboardData(array $filters = []): array
    {
        $rows = $this->getFilteredRows($filters);

        $stats = [
            'total_students' => $rows->count(),
            'avg_productivity' => round($rows->avg('productivity_score') ?? 0, 2),
            'avg_exam_score' => round($rows->avg('exam_score') ?? 0, 2),
            'avg_focus_index' => round($rows->avg('focus_index') ?? 0, 2),
            'avg_burnout' => round($rows->avg('burnout_level') ?? 0, 2),
        ];

        $academicSummary = $rows
            ->groupBy('academic_level')
            ->map(function (Collection $group) {
                return [
                    'count' => $group->count(),
                    'avg_productivity' => round($group->avg('productivity_score') ?? 0, 2),
                    'avg_exam_score' => round($group->avg('exam_score') ?? 0, 2),
                ];
            })
            ->sortKeys();

        $genderDistribution = $rows
            ->groupBy('gender')
            ->map(fn (Collection $group) => $group->count())
            ->sortKeys();

        $internetQualityDistribution = $rows
            ->groupBy('internet_quality')
            ->map(fn (Collection $group) => $group->count())
            ->sortKeys();

        $studyBuckets = [
            '0-2 jam' => [0, 2],
            '2-4 jam' => [2, 4],
            '4-6 jam' => [4, 6],
            '6-8 jam' => [6, 8],
            '8+ jam' => [8, null],
        ];

        $studyPerformance = collect($studyBuckets)->map(function (array $range, string $label) use ($rows) {
            [$min, $max] = $range;

            $group = $rows->filter(function (array $row) use ($min, $max) {
                if ($max === null) {
                    return $row['study_hours'] >= $min;
                }

                return $row['study_hours'] >= $min && $row['study_hours'] < $max;
            });

            return [
                'label' => $label,
                'avg_productivity' => round($group->avg('productivity_score') ?? 0, 2),
                'avg_exam_score' => round($group->avg('exam_score') ?? 0, 2),
            ];
        });

        $partTimeImpact = $rows
            ->groupBy(function (array $row) {
                return (int) $row['part_time_job'] === 1 ? 'Punya Part-time' : 'Tanpa Part-time';
            })
            ->map(function (Collection $group) {
                return [
                    'avg_productivity' => round($group->avg('productivity_score') ?? 0, 2),
                    'avg_exam_score' => round($group->avg('exam_score') ?? 0, 2),
                ];
            });

        $scatterPoints = $rows
            ->take(250)
            ->map(function (array $row) {
                return [
                    'x' => $row['study_hours'],
                    'y' => $row['productivity_score'],
                ];
            })
            ->values();

        $topAcademicInsights = $academicSummary
            ->map(function (array $summary, string $level) {
                return array_merge(['level' => $level], $summary);
            })
            ->sortByDesc('avg_productivity')
            ->values();

        return [
            'stats' => $stats,
            'academic_summary' => $academicSummary,
            'gender_distribution' => $genderDistribution,
            'internet_quality_distribution' => $internetQualityDistribution,
            'study_performance' => $studyPerformance,
            'part_time_impact' => $partTimeImpact,
            'scatter_points' => $scatterPoints,
            'top_academic_insights' => $topAcademicInsights,
            'total_rows_before_filter' => $this->loadRows()->count(),
        ];
    }

    protected function getFilteredRows(array $filters = []): Collection
    {
        $rows = $this->loadRows();

        return $rows->filter(function (array $row) use ($filters) {
            if (($filters['academic_level'] ?? 'all') !== 'all' && $row['academic_level'] !== $filters['academic_level']) {
                return false;
            }

            if (($filters['gender'] ?? 'all') !== 'all' && $row['gender'] !== $filters['gender']) {
                return false;
            }

            if (($filters['internet_quality'] ?? 'all') !== 'all' && $row['internet_quality'] !== $filters['internet_quality']) {
                return false;
            }

            return true;
        })->values();
    }

    protected function loadRows(): Collection
    {
        return Cache::remember('student_productivity_dataset_rows', 600, function () {
            $path = Storage::disk('local')->path($this->datasetPath);
            $handle = fopen($path, 'r');

            if ($handle === false) {
                return collect();
            }

            $headers = fgetcsv($handle);
            $rows = [];

            while (($record = fgetcsv($handle)) !== false) {
                if (!$headers || count($headers) !== count($record)) {
                    continue;
                }

                $row = array_combine($headers, $record);

                $rows[] = [
                    'student_id' => (int) $row['student_id'],
                    'age' => (int) $row['age'],
                    'gender' => $row['gender'],
                    'academic_level' => $row['academic_level'],
                    'study_hours' => (float) $row['study_hours'],
                    'self_study_hours' => (float) $row['self_study_hours'],
                    'online_classes_hours' => (float) $row['online_classes_hours'],
                    'social_media_hours' => (float) $row['social_media_hours'],
                    'gaming_hours' => (float) $row['gaming_hours'],
                    'sleep_hours' => (float) $row['sleep_hours'],
                    'screen_time_hours' => (float) $row['screen_time_hours'],
                    'exercise_minutes' => (float) $row['exercise_minutes'],
                    'caffeine_intake_mg' => (float) $row['caffeine_intake_mg'],
                    'part_time_job' => (int) $row['part_time_job'],
                    'upcoming_deadline' => (int) $row['upcoming_deadline'],
                    'internet_quality' => $row['internet_quality'],
                    'mental_health_score' => (float) $row['mental_health_score'],
                    'focus_index' => (float) $row['focus_index'],
                    'burnout_level' => (float) $row['burnout_level'],
                    'productivity_score' => (float) $row['productivity_score'],
                    'exam_score' => (float) $row['exam_score'],
                ];
            }

            fclose($handle);

            return collect($rows);
        });
    }
}
