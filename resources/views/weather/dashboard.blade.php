@extends('layouts.app')

@section('title', 'Weather Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-cloud-sun text-blue-500 mr-3"></i>Weather Dashboard
    </h1>
    <p class="text-gray-600">Real-time weather data from cities in Java Island</p>
    <p class="text-sm text-gray-500 mt-1">
        Source:
        <a
            href="https://open-meteo.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="text-blue-600 hover:text-blue-700 underline"
        >
            Open-Meteo API
        </a>
    </p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Cities</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_cities'] }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-city text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Hottest City</p>
                <h3 class="text-2xl font-bold text-red-600">{{ $stats['hottest_city'] }}</h3>
                <p class="text-sm text-gray-500">{{ $stats['hottest_temp'] }}°C</p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-temperature-high text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Coolest City</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $stats['coolest_city'] }}</h3>
                <p class="text-sm text-gray-500">{{ $stats['coolest_temp'] }}°C</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-temperature-low text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Average Temperature</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['avg_temperature'] }}°C</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-thermometer-half text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Temperature Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-thermometer-half text-red-500 mr-2"></i>Temperature by City (°C)
        </h3>
        <div class="h-80">
            <canvas id="temperatureChart"></canvas>
        </div>
    </div>

    <!-- Humidity Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-tint text-blue-500 mr-2"></i>Humidity by City (%)
        </h3>
        <div class="h-80">
            <canvas id="humidityChart"></canvas>
        </div>
    </div>
</div>

<!-- City Weather Cards -->
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Current Weather by City
    </h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
    @foreach($allWeather as $city => $data)
    <div class="bg-white rounded-xl shadow-lg p-4 hover:shadow-xl transition">
        <div class="text-center">
            <h4 class="font-semibold text-gray-800">{{ $city }}</h4>
            @if($data['success'] && $data['current'])
                <div class="my-3">
                    @php
                        $code = $data['current']['weather_code'];
                        $icon = match(true) {
                            $code == 0 => 'fa-sun text-yellow-500',
                            $code <= 3 => 'fa-cloud-sun text-gray-500',
                            $code <= 48 => 'fa-smog text-gray-400',
                            $code <= 65 => 'fa-cloud-rain text-blue-500',
                            $code <= 77 => 'fa-snowflake text-blue-300',
                            $code <= 82 => 'fa-cloud-showers-heavy text-blue-600',
                            default => 'fa-bolt text-yellow-600',
                        };
                    @endphp
                    <i class="fas {{ $icon }} text-4xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
                <p class="text-sm text-gray-500">{{ $data['current']['weather_description'] }}</p>
                <div class="mt-2 text-xs text-gray-400">
                    <span><i class="fas fa-tint mr-1"></i>{{ $data['current']['humidity'] }}%</span>
                    <span class="ml-2"><i class="fas fa-wind mr-1"></i>{{ round($data['current']['wind_speed']) }} km/h</span>
                </div>
            @else
                <p class="text-red-500 text-sm my-4">Data unavailable</p>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Refresh Button -->
<div class="mt-6 text-center">
    <a href="{{ route('weather.refresh') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        <i class="fas fa-sync-alt mr-2"></i>Refresh Weather Data
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

    // Humidity Chart
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');
    new Chart(humidityCtx, {
        type: 'doughnut',
        data: {
            labels: cityLabels,
            datasets: [{
                data: humidities,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(20, 184, 166, 0.8)',
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 162, 158, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: { boxWidth: 12 }
                }
            }
        }
    });
});
</script>
@endsection
