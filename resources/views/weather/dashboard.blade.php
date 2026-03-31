@extends('layouts.app')

@section('title', 'Weather Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-600 bg-clip-text text-transparent mb-2">
        Cuaca di Jawa
    </h1>
    <p class="text-gray-600 text-lg">Pantau kondisi real-time cuaca di 15 kota di Pulau Jawa dengan data terkini.</p>
    <div class="mt-3 inline-block bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
        Data real-time dari Open-Meteo API
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Kota</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['total_cities'] }}</h3>
            </div>
            <div class="text-4xl text-blue-400"><i class="fas fa-city"></i></div>
        </div>
    </div>

    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Kota Terpanas</p>
                <h3 class="text-2xl font-bold text-red-600 mt-2">{{ $stats['hottest_city'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['hottest_temp'] }}°C</p>
            </div>
            <div class="text-4xl text-red-400"><i class="fas fa-temperature-high"></i></div>
        </div>
    </div>

    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Kota Terdingin</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['coolest_city'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['coolest_temp'] }}°C</p>
            </div>
            <div class="text-4xl text-cyan-400"><i class="fas fa-snowflake"></i></div>
        </div>
    </div>

    <div class="stat-card card-hover rounded-xl shadow-md p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Suhu Rata-rata</p>
                <h3 class="text-4xl font-bold text-teal-600 mt-2">{{ $stats['avg_temperature'] }}°C</h3>
            </div>
            <div class="text-4xl text-teal-400"><i class="fas fa-thermometer-half"></i></div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter mr-2 text-blue-600"></i>Filter Data Cuaca
    </h2>
    <form method="GET" action="{{ route('weather.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="city_search" class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Kota</label>
            <input type="text" id="city_search" name="city_search" value="{{ $filters['city_search'] }}" placeholder="Misal: Bandung" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label for="city_letter" class="block text-sm font-medium text-gray-700 mb-2">Filter Abjad</label>
            <select id="city_letter" name="city_letter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Kota</option>
                @php
                    $letters = [];
                    foreach(array_keys($allWeather) as $city) {
                        $letters[] = strtoupper($city[0]);
                    }
                    $letters = array_unique($letters);
                    sort($letters);
                @endphp
                @foreach($letters as $letter)
                    <option value="{{ $letter }}" {{ $filters['city_letter'] === $letter ? 'selected' : '' }}>{{ $letter }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="humidity_level" class="block text-sm font-medium text-gray-700 mb-2">Tingkat Kelembaban</label>
            <select id="humidity_level" name="humidity_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Level</option>
                <option value="low" {{ $filters['humidity_level'] === 'low' ? 'selected' : '' }}>Rendah (&lt; 60%)</option>
                <option value="medium" {{ $filters['humidity_level'] === 'medium' ? 'selected' : '' }}>Sedang (60% - 80%)</option>
                <option value="high" {{ $filters['humidity_level'] === 'high' ? 'selected' : '' }}>Tinggi (&gt; 80%)</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            <a href="{{ route('weather.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors font-medium">Reset</a>
        </div>
    </form>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Temperature Chart -->
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Suhu per Kota (°C)
        </h3>
        <div class="h-80">
            <canvas id="temperatureChart"></canvas>
        </div>
    </div>

    <!-- Humidity Chart (Changed to Line Chart) -->
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Kelembaban Udara (%)
        </h3>
        <div class="h-80">
            <canvas id="humidityChart"></canvas>
        </div>
    </div>
</div>

<!-- City Weather Cards -->
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Cuaca Terkini per Kota
    </h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
    @foreach($filteredWeather as $city => $data)
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 hover:shadow-lg transition-all duration-300 card-hover">
        <div class="text-center">
            <h4 class="font-semibold text-gray-800 text-lg mb-4">{{ $city }}</h4>
            @if($data['success'] && $data['current'])
                <div class="my-4 text-6xl">
                    @php
                        $code = $data['current']['weather_code'];
                        $emoji = match(true) {
                            $code == 0 => '☀️',
                            $code <= 3 => '⛅',
                            $code <= 48 => '🌫️',
                            $code <= 65 => '🌧️',
                            $code <= 77 => '❄️',
                            $code <= 82 => '⛈️',
                            default => '⚡',
                        };
                    @endphp
                    {{ $emoji }}
                </div>
                <p class="text-4xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
                <p class="text-sm text-gray-600 mt-2 capitalize">{{ $data['current']['weather_description'] }}</p>
                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-blue-50 rounded-lg p-2">
                        <p class="text-gray-600">Kelembaban</p>
                        <p class="font-semibold text-blue-700">{{ $data['current']['humidity'] }}%</p>
                    </div>
                    <div class="bg-cyan-50 rounded-lg p-2">
                        <p class="text-gray-600">Angin</p>
                        <p class="font-semibold text-cyan-700">{{ round($data['current']['wind_speed']) }} km/h</p>
                    </div>
                </div>
            @else
                <p class="text-red-500 text-sm my-6 font-medium">Data tidak tersedia</p>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Refresh Button -->
<div class="mt-8 text-center">
    <a href="{{ route('weather.refresh') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
        <i class="fas fa-rotate-right mr-2"></i>Perbarui Data Cuaca
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityLabels = {!! json_encode(array_keys($stats['temperatures'])) !!};
    const temperatures = {!! json_encode(array_values($stats['temperatures'])) !!}.map(Number);
    const humidities = {!! json_encode(array_values($stats['humidities'])) !!}.map(Number);

    // Temperature Chart
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    new Chart(tempCtx, {
        type: 'bar',
        data: {
            labels: cityLabels,
            datasets: [{
                label: 'Temperature (°C)',
                data: temperatures,
                backgroundColor: temperatures.map(temp => {
                    if (temp >= 32) return 'rgba(239, 68, 68, 0.8)';
                    if (temp >= 28) return 'rgba(249, 115, 22, 0.8)';
                    if (temp >= 24) return 'rgba(234, 179, 8, 0.8)';
                    return 'rgba(34, 197, 94, 0.8)';
                }),
                borderRadius: 8,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: Math.max(...temperatures, 35) + 2
                }
            }
        }
    });

    // Humidity Chart (Line Chart)
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');
    new Chart(humidityCtx, {
        type: 'line',
        data: {
            labels: cityLabels,
            datasets: [{
                label: 'Humidity (%)',
                data: humidities,
                borderColor: 'rgba(59, 130, 246, 0.8)',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(59, 130, 246, 0.8)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
