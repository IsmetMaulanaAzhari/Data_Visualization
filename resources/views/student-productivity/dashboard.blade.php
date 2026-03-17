@extends('layouts.app')

@section('title', 'Student Productivity Dashboard')

@section('content')
@php
    $stats = $dashboardData['stats'];
    $academicSummary = $dashboardData['academic_summary'];
    $genderDistribution = $dashboardData['gender_distribution'];
    $internetQualityDistribution = $dashboardData['internet_quality_distribution'];
    $studyPerformance = $dashboardData['study_performance'];
    $partTimeImpact = $dashboardData['part_time_impact'];
    $scatterPoints = $dashboardData['scatter_points'];
    $topAcademicInsights = $dashboardData['top_academic_insights'];
@endphp

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-user-graduate text-indigo-500 mr-3"></i>Student Productivity Dashboard
    </h1>
    <p class="text-gray-600">Visualisasi dataset CSV yang kamu upload tanpa ditaruh di dashboard utama.</p>
    <p class="text-sm text-gray-500 mt-1">Source file: <span class="font-medium text-indigo-600">storage/app/datasets/ultimate_student_productivity_dataset_5000.csv</span></p>
</div>

<div class="bg-white rounded-xl shadow-lg p-5 mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <p class="text-sm font-semibold text-gray-700 mb-2">Upload Dataset CSV Baru</p>
            <form method="POST" action="{{ route('student-productivity.upload') }}" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3">
                @csrf
                <input type="file" name="dataset_file" accept=".csv" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                    <i class="fas fa-upload mr-2"></i>Upload & Replace
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Maksimal file 50MB. Setelah upload, cache dataset otomatis diperbarui.</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-sm">
            <p class="font-semibold text-gray-700 mb-2">Dataset Info</p>
            <p class="text-gray-600">Status: <span class="font-medium {{ $datasetMeta['exists'] ? 'text-emerald-600' : 'text-red-600' }}">{{ $datasetMeta['exists'] ? 'Available' : 'Not Found' }}</span></p>
            <p class="text-gray-600">Size: <span class="font-medium">{{ number_format($datasetMeta['size_kb'], 2) }} KB</span></p>
            <p class="text-gray-600">Updated: <span class="font-medium">{{ $datasetMeta['updated_at'] ? date('d M Y H:i', $datasetMeta['updated_at']) : '-' }}</span></p>
            <div class="mt-3 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('student-productivity.refresh') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition">
                        <i class="fas fa-rotate mr-1"></i>Refresh Cache
                    </button>
                </form>
                <a href="{{ route('student-productivity.api', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-slate-700 text-white rounded-md hover:bg-slate-800 transition">
                    <i class="fas fa-code mr-1"></i>Open API JSON
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-5 mb-6">
    <form method="GET" action="{{ route('student-productivity.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="academic_level" class="block text-sm font-medium text-gray-700 mb-1">Academic Level</label>
            <select id="academic_level" name="academic_level" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">Semua level</option>
                @foreach($availableFilters['academic_levels'] as $level)
                    <option value="{{ $level }}" {{ $filters['academic_level'] === $level ? 'selected' : '' }}>{{ $level }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select id="gender" name="gender" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">Semua gender</option>
                @foreach($availableFilters['genders'] as $gender)
                    <option value="{{ $gender }}" {{ $filters['gender'] === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="internet_quality" class="block text-sm font-medium text-gray-700 mb-1">Internet Quality</label>
            <select id="internet_quality" name="internet_quality" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">Semua kualitas</option>
                @foreach($availableFilters['internet_qualities'] as $quality)
                    <option value="{{ $quality }}" {{ $filters['internet_quality'] === $quality ? 'selected' : '' }}>{{ $quality }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-filter mr-2"></i>Terapkan
            </button>
            <a href="{{ route('student-productivity.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Reset</a>
        </div>
    </form>
    <p class="text-xs text-gray-500 mt-3">Menampilkan {{ number_format($stats['total_students']) }} dari {{ number_format($dashboardData['total_rows_before_filter']) }} baris dataset.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <p class="text-sm text-gray-500">Total Students</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_students']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <p class="text-sm text-gray-500">Avg Productivity</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['avg_productivity'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <p class="text-sm text-gray-500">Avg Exam Score</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['avg_exam_score'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <p class="text-sm text-gray-500">Avg Burnout</p>
        <p class="text-3xl font-bold text-rose-600 mt-1">{{ $stats['avg_burnout'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-layer-group text-indigo-500 mr-2"></i>Rata-rata Productivity dan Exam per Academic Level
        </h3>
        <div class="h-80">
            <canvas id="academicLevelChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-venus-mars text-pink-500 mr-2"></i>Distribusi Gender
        </h3>
        <div class="h-80">
            <canvas id="genderChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-signal text-cyan-500 mr-2"></i>Kualitas Internet pada Dataset
        </h3>
        <div class="h-80">
            <canvas id="internetQualityChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-book-open text-amber-500 mr-2"></i>Study Hours Bucket vs Performance
        </h3>
        <div class="h-80">
            <canvas id="studyHoursChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6 xl:col-span-2">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-scatter text-indigo-500 mr-2"></i>Study Hours vs Productivity Score
        </h3>
        <div class="h-96">
            <canvas id="scatterChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 mt-3">Scatter chart dibatasi ke 250 titik pertama agar rendering tetap ringan.</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-briefcase text-slate-500 mr-2"></i>Dampak Part-time Job
        </h3>
        <div class="space-y-4">
            @foreach($partTimeImpact as $label => $impact)
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="font-semibold text-gray-800">{{ $label }}</p>
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <p>Avg Productivity: <span class="font-medium text-indigo-600">{{ $impact['avg_productivity'] }}</span></p>
                        <p>Avg Exam Score: <span class="font-medium text-emerald-600">{{ $impact['avg_exam_score'] }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-ranking-star text-yellow-500 mr-2"></i>Academic Level Insight
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3">Academic Level</th>
                    <th class="py-3">Students</th>
                    <th class="py-3">Avg Productivity</th>
                    <th class="py-3">Avg Exam Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topAcademicInsights as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 font-medium text-gray-800">{{ $item['level'] }}</td>
                        <td class="py-3">{{ number_format($item['count']) }}</td>
                        <td class="py-3 text-indigo-600 font-semibold">{{ $item['avg_productivity'] }}</td>
                        <td class="py-3 text-emerald-600 font-semibold">{{ $item['avg_exam_score'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-table text-sky-500 mr-2"></i>Preview Data (8 baris pertama)
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-xs md:text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2 pr-3">ID</th>
                    <th class="py-2 pr-3">Level</th>
                    <th class="py-2 pr-3">Gender</th>
                    <th class="py-2 pr-3">Study Hours</th>
                    <th class="py-2 pr-3">Focus</th>
                    <th class="py-2 pr-3">Burnout</th>
                    <th class="py-2 pr-3">Productivity</th>
                    <th class="py-2 pr-3">Exam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($previewRows as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 pr-3">{{ $row['student_id'] }}</td>
                        <td class="py-2 pr-3">{{ $row['academic_level'] }}</td>
                        <td class="py-2 pr-3">{{ $row['gender'] }}</td>
                        <td class="py-2 pr-3">{{ $row['study_hours'] }}</td>
                        <td class="py-2 pr-3">{{ $row['focus_index'] }}</td>
                        <td class="py-2 pr-3">{{ $row['burnout_level'] }}</td>
                        <td class="py-2 pr-3 text-indigo-600 font-medium">{{ $row['productivity_score'] }}</td>
                        <td class="py-2 pr-3 text-emerald-600 font-medium">{{ $row['exam_score'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">Dataset belum tersedia atau gagal dibaca.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const academicLevelLabels = {!! json_encode($academicSummary->keys()->values()) !!};
    const academicProductivity = {!! json_encode($academicSummary->pluck('avg_productivity')->values()) !!};
    const academicExamScores = {!! json_encode($academicSummary->pluck('avg_exam_score')->values()) !!};

    new Chart(document.getElementById('academicLevelChart'), {
        type: 'bar',
        data: {
            labels: academicLevelLabels,
            datasets: [{
                label: 'Avg Productivity',
                data: academicProductivity,
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderRadius: 8
            }, {
                label: 'Avg Exam Score',
                data: academicExamScores,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderDistribution->keys()->values()) !!},
            datasets: [{
                data: {!! json_encode($genderDistribution->values()) !!},
                backgroundColor: [
                    'rgba(244, 114, 182, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('internetQualityChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($internetQualityDistribution->keys()->values()) !!},
            datasets: [{
                data: {!! json_encode($internetQualityDistribution->values()) !!},
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('studyHoursChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($studyPerformance->pluck('label')->values()) !!},
            datasets: [{
                label: 'Avg Productivity',
                data: {!! json_encode($studyPerformance->pluck('avg_productivity')->values()) !!},
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                fill: true,
                tension: 0.35
            }, {
                label: 'Avg Exam Score',
                data: {!! json_encode($studyPerformance->pluck('avg_exam_score')->values()) !!},
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('scatterChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Students',
                data: {!! json_encode($scatterPoints) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.35)',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Study Hours'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Productivity Score'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
