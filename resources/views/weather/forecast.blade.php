@extends('layouts.app')

@section('title', '7-Day Forecast - ' . $selectedCity)

@section('content')
<div class="mb-8">
    <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-600 bg-clip-text text-transparent mb-2">
        Prakiraan Cuaca 7 Hari
    </h1>
    <p class="text-gray-600 text-lg">Prediksi cuaca mingguan untuk {{ $selectedCity }}</p>
</div>

<!-- City Selector -->
<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mb-6">
    <form method="GET" action="{{ route('weather.forecast') }}" class="flex items-center gap-4">
        <label class="font-semibold text-gray-700">
            <i class="fas fa-location-dot mr-2"></i>Pilih Kota:
        </label>
        <select name="city" onchange="this.form.submit()" class="flex-1 max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @foreach($cities as $city)
            <option value="{{ $city }}" {{ $selectedCity == $city ? 'selected' : '' }}>{{ $city }}</option>
            @endforeach
        </select>
    </form>
</div>

@if($weatherData['success'])
<!-- Current Weather Header -->
<div class="bg-gradient-to-br from-blue-600 via-cyan-500 to-teal-600 rounded-xl shadow-md p-8 text-white mb-8 card-hover">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold mb-3">{{ $selectedCity }}</h2>
            @if($weatherData['current'])
            <p class="text-7xl font-bold">{{ round($weatherData['current']['temperature']) }}°C</p>
            <p class="text-xl mt-3 text-blue-100 capitalize">{{ $weatherData['current']['weather_description'] }}</p>
            @endif
        </div>
        <div class="text-right">
            @php
                $code = $weatherData['current']['weather_code'] ?? 0;
                $icon = match(true) {
                    $code == 0 => 'fa-sun text-amber-300',
                    $code <= 3 => 'fa-cloud-sun text-orange-200',
                    $code <= 48 => 'fa-smog text-slate-200',
                    $code <= 65 => 'fa-cloud-rain text-blue-200',
                    $code <= 77 => 'fa-snowflake text-cyan-200',
                    $code <= 82 => 'fa-cloud-showers-heavy text-indigo-200',
                    default => 'fa-bolt text-yellow-200',
                };
            @endphp
            <span class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/10 border border-white/30">
                <i class="fas {{ $icon }} text-5xl"></i>
            </span>
        </div>
    </div>
</div>

<!-- 7-Day Forecast -->
<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6">
    <h3 class="text-2xl font-semibold text-gray-800 mb-6">
        <i class="fas fa-calendar-days mr-2 text-blue-600"></i>Prakiraan 7 Hari Ke Depan
    </h3>
    
    <div class="space-y-3">
        @foreach($weatherData['daily'] as $index => $day)
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between p-5 {{ $index == 0 ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-gray-50 hover:bg-gray-100' }} rounded-lg transition-colors">
            <div class="flex items-center md:w-1/4">
                <div class="w-28">
                    <p class="font-semibold text-gray-800">{{ $day['day_name'] }}</p>
                    <p class="text-sm text-gray-500">{{ date('d M', strtotime($day['date'])) }}</p>
                </div>
            </div>
            
            <div class="flex items-center md:justify-center flex-1 gap-3">
                @php
                    $dayCode = $day['weather_code'];
                    $dayIcon = match(true) {
                        $dayCode == 0 => 'fa-sun text-amber-500',
                        $dayCode <= 3 => 'fa-cloud-sun text-orange-400',
                        $dayCode <= 48 => 'fa-smog text-slate-400',
                        $dayCode <= 65 => 'fa-cloud-rain text-blue-500',
                        $dayCode <= 77 => 'fa-snowflake text-cyan-400',
                        $dayCode <= 82 => 'fa-cloud-showers-heavy text-indigo-500',
                        default => 'fa-bolt text-yellow-500',
                    };
                @endphp
                <span class="text-3xl"><i class="fas {{ $dayIcon }}"></i></span>
                <span class="text-gray-700 capitalize">{{ $day['weather_description'] }}</span>
            </div>
            
            <div class="flex items-center md:justify-end md:w-1/4 gap-6">
                <div class="text-center">
                    <p class="text-xs text-gray-600 font-medium">Tertinggi</p>
                    <p class="text-2xl font-bold text-red-600">{{ round($day['temp_max']) }}°</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 font-medium">Terendah</p>
                    <p class="text-2xl font-bold text-blue-600">{{ round($day['temp_min']) }}°</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 font-medium">Hujan</p>
                    <p class="text-sm text-gray-700 font-medium">{{ $day['precipitation'] }} mm</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Temperature Trend Chart -->
<div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-line mr-2 text-green-500"></i>Temperature Trend
    </h3>
    <canvas id="forecastChart" height="100"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('forecastChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(function($d) { return $d['day_name']; }, $weatherData['daily'])) !!},
            datasets: [{
                label: 'Max Temperature',
                data: {!! json_encode(array_map(function($d) { return $d['temp_max']; }, $weatherData['daily'])) !!},
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Min Temperature',
                data: {!! json_encode(array_map(function($d) { return $d['temp_min']; }, $weatherData['daily'])) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
});
</script>
@else
<div class="bg-white rounded-xl shadow-lg p-8 text-center">
    <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
    <p class="text-xl text-gray-600">Unable to fetch forecast data for {{ $selectedCity }}</p>
    <a href="{{ route('weather.refresh') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
        <i class="fas fa-sync-alt mr-2"></i>Try Again
    </a>
</div>
@endif
@endsection
