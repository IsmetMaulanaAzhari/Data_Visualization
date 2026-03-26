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

<div class="mb-8">
    <h1 class="text-5xl font-bold bg-gradient-to-r from-violet-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
        Analisis Produktivitas Mahasiswa
    </h1>
    <p class="text-gray-600 text-lg">Eksplorasi mendalam tentang pola belajar, kualitas fokus, dan performa akademik mahasiswa.</p>
    <div class="mt-3 inline-block bg-gradient-to-r from-violet-100 to-purple-100 text-violet-700 px-4 py-2 rounded-lg text-sm font-medium">
        {{ number_format($dashboardData['total_rows_before_filter']) }} data mahasiswa dalam dataset
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mb-6 card-hover">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-lightbulb mr-2 text-violet-600"></i>Ringkasan Visualisasi
            </h2>
            <p class="text-sm text-gray-600 mb-3">
                Dashboard ini menyoroti hubungan antara jam belajar, produktivitas, burnout, dan hasil ujian untuk membantu melihat pola belajar yang paling efektif.
            </p>
            <p class="text-sm text-gray-600 mb-3">
                Gunakan filter tingkat akademik, gender, dan kualitas internet untuk membaca tren spesifik pada kelompok mahasiswa tertentu.
            </p>
            <p class="text-sm text-gray-600">
                Insight cepat: rata-rata produktivitas saat ini <span class="font-semibold text-purple-600">{{ $stats['avg_productivity'] }}</span>,
                rata-rata nilai ujian <span class="font-semibold text-emerald-600">{{ $stats['avg_exam_score'] }}</span>,
                dan rata-rata burnout <span class="font-semibold text-rose-600">{{ $stats['avg_burnout'] }}</span>.
            </p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-5 border border-purple-100">
            <h3 class="font-semibold text-gray-800 mb-4">Dataset Info</h3>
            <div class="space-y-2 text-sm mb-4">
                <p class="text-gray-700">Status: <span class="font-medium {{ $datasetMeta['exists'] ? 'text-emerald-600' : 'text-red-600' }}">{{ $datasetMeta['exists'] ? '✓ Tersedia' : '✗ Tidak Ditemukan' }}</span></p>
                <p class="text-gray-700">Ukuran: <span class="font-medium">{{ number_format($datasetMeta['size_kb'], 2) }} KB</span></p>
                <p class="text-gray-700">Update Terakhir: <span class="font-medium">{{ $datasetMeta['updated_at'] ? date('d M Y H:i', $datasetMeta['updated_at']) : '-' }}</span></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('student-productivity.refresh') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-md transition-colors text-sm font-medium">
                        <i class="fas fa-rotate-right mr-1"></i>Segarkan Cache
                    </button>
                </form>
                <a href="{{ route('student-productivity.api', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-md transition-colors text-sm font-medium">
                    <i class="fas fa-code mr-1"></i>JSON API
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mb-6 card-hover">
    <h2 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-filter mr-2 text-violet-600"></i>Filter Data</h2>
    <form method="GET" action="{{ route('student-productivity.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="academic_level" class="block text-sm font-medium text-gray-700 mb-2">Tingkat Akademik</label>
            <select id="academic_level" name="academic_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="all">Semua level</option>
                @foreach($availableFilters['academic_levels'] as $level)
                    <option value="{{ $level }}" {{ $filters['academic_level'] === $level ? 'selected' : '' }}>{{ $level }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="all">Semua gender</option>
                @foreach($availableFilters['genders'] as $gender)
                    <option value="{{ $gender }}" {{ $filters['gender'] === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="internet_quality" class="block text-sm font-medium text-gray-700 mb-2">Kualitas Internet</label>
            <select id="internet_quality" name="internet_quality" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="all">Semua kualitas</option>
                @foreach($availableFilters['internet_qualities'] as $quality)
                    <option value="{{ $quality }}" {{ $filters['internet_quality'] === $quality ? 'selected' : '' }}>{{ $quality }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
                Terapkan Filter
            </button>
            <a href="{{ route('student-productivity.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors font-medium">Reset</a>
        </div>
    </form>
    <p class="text-xs text-gray-500 mt-3">Menampilkan <span class="font-semibold">{{ number_format($stats['total_students']) }}</span> dari <span class="font-semibold">{{ number_format($dashboardData['total_rows_before_filter']) }}</span> record dataset.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Mahasiswa</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_students']) }}</h3>
            </div>
            <div class="text-4xl text-purple-300"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Rata-rata Produktivitas</p>
                <h3 class="text-4xl font-bold text-purple-600 mt-2">{{ $stats['avg_productivity'] }}</h3>
            </div>
            <div class="text-4xl text-purple-300"><i class="fas fa-bolt"></i></div>
        </div>
    </div>
    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Rata-rata Nilai Ujian</p>
                <h3 class="text-4xl font-bold text-emerald-600 mt-2">{{ $stats['avg_exam_score'] }}</h3>
            </div>
            <div class="text-4xl text-emerald-300"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Rata-rata Burnout</p>
                <h3 class="text-4xl font-bold text-rose-600 mt-2">{{ $stats['avg_burnout'] }}</h3>
            </div>
            <div class="text-4xl text-rose-300"><i class="fas fa-fire"></i></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-column mr-2 text-violet-600"></i>Produktivitas vs Nilai per Tingkat Akademik
        </h3>
        <div class="h-80">
            <canvas id="academicLevelChart"></canvas>
        </div>
    </div>
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-venus-mars mr-2 text-pink-500"></i>Distribusi Jenis Kelamin
        </h3>
        <div class="h-80">
            <canvas id="genderChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-wifi mr-2 text-cyan-500"></i>Kualitas Internet
        </h3>
        <div class="h-80">
            <canvas id="internetQualityChart"></canvas>
        </div>
    </div>
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-clock mr-2 text-amber-500"></i>Jam Belajar vs Performa
        </h3>
        <div class="h-80">
            <canvas id="studyHoursChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover xl:col-span-2">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-circle-dot mr-2 text-indigo-500"></i>Jam Belajar vs Skor Produktivitas
        </h3>
        <div class="h-96">
            <canvas id="scatterChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 mt-3">*Data sebanyak 250 titik pertama untuk performa rendering yang optimal.</p>
    </div>
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-briefcase mr-2 text-slate-500"></i>Efek Pekerjaan Part-time
        </h3>
        <div class="space-y-4">
            @foreach($partTimeImpact as $label => $impact)
                <div class="rounded-lg bg-gradient-to-br from-pink-50 to-orange-50 p-4 border border-pink-100">
                    <p class="font-semibold text-gray-800">{{ $label }}</p>
                    <div class="mt-3 text-sm space-y-2">
                        <div>
                            <p class="text-gray-600">Produktivitas</p>
                            <p class="font-medium text-purple-600">{{ $impact['avg_productivity'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Nilai Ujian</p>
                            <p class="font-medium text-emerald-600">{{ $impact['avg_exam_score'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-bullseye mr-2 text-yellow-500"></i>Wawasan per Tingkat Akademik
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-600 border-b border-gray-200">
                    <th class="py-3 font-semibold">Tingkat Akademik</th>
                    <th class="py-3 font-semibold">Total Mahasiswa</th>
                    <th class="py-3 font-semibold">Rata-rata Produktivitas</th>
                    <th class="py-3 font-semibold">Rata-rata Nilai Ujian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topAcademicInsights as $item)
                    <tr class="border-b border-gray-100 hover:bg-white/50 transition">
                        <td class="py-3 font-medium text-gray-800">{{ $item['level'] }}</td>
                        <td class="py-3 text-gray-600">{{ number_format($item['count']) }}</td>
                        <td class="py-3 text-purple-600 font-semibold">{{ $item['avg_productivity'] }}</td>
                        <td class="py-3 text-emerald-600 font-semibold">{{ $item['avg_exam_score'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-table mr-2 text-sky-500"></i>Preview Data
    </h3>
    <p class="text-sm text-gray-600 mb-4">Menampilkan 8 baris pertama dari dataset yang dipilih.</p>
    <div class="overflow-x-auto">
        <table class="w-full text-xs md:text-sm">
            <thead>
                <tr class="text-left text-gray-600 border-b border-gray-200 bg-gray-50">
                    <th class="py-2.5 px-3 font-semibold">ID</th>
                    <th class="py-2.5 px-3 font-semibold">Tingkat</th>
                    <th class="py-2.5 px-3 font-semibold">Gender</th>
                    <th class="py-2.5 px-3 font-semibold">Jam</th>
                    <th class="py-2.5 px-3 font-semibold">Fokus</th>
                    <th class="py-2.5 px-3 font-semibold">Burnout</th>
                    <th class="py-2.5 px-3 font-semibold">Produktivitas</th>
                    <th class="py-2.5 px-3 font-semibold">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($previewRows as $row)
                    <tr class="border-b border-gray-100 hover:bg-indigo-50 transition">
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['student_id'] }}</td>
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['academic_level'] }}</td>
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['gender'] }}</td>
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['study_hours'] }}</td>
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['focus_index'] }}</td>
                        <td class="py-2.5 px-3 text-gray-700">{{ $row['burnout_level'] }}</td>
                        <td class="py-2.5 px-3 text-purple-600 font-semibold">{{ $row['productivity_score'] }}</td>
                        <td class="py-2.5 px-3 text-emerald-600 font-semibold">{{ $row['exam_score'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">Dataset belum tersedia atau gagal dibaca.</td>
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
                label: 'Rata-rata Produktivitas',
                data: academicProductivity,
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                borderRadius: 8
            }, {
                label: 'Rata-rata Nilai Ujian',
                data: academicExamScores,
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
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
