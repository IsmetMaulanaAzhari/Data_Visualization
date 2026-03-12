@extends('layouts.app')

@section('title', '7-Day Forecast - ' . $selectedCity)

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-calendar-week text-blue-500 mr-3"></i>7-Day Weather Forecast
    </h1>
    <p class="text-gray-600">Weekly weather prediction for {{ $selectedCity }}</p>
</div>

<!-- City Selector -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('weather.forecast') }}" class="flex items-center gap-4">
        <label class="font-semibold text-gray-700">
            <i class="fas fa-map-marker-alt mr-2"></i>Select City:
        </label>
        <select name="city" onchange="this.form.submit()" class="flex-1 max-w-xs border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @foreach($cities as $city)
            <option value="{{ $city }}" {{ $selectedCity == $city ? 'selected' : '' }}>{{ $city }}</option>
            @endforeach
        </select>
    </form>
</div>

@if($weatherData['success'])
<!-- Current Weather Header -->
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-8 text-white mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">{{ $selectedCity }}</h2>
            @if($weatherData['current'])
            <p class="text-6xl font-bold">{{ round($weatherData['current']['temperature']) }}°C</p>
            <p class="text-xl mt-2 text-blue-100">{{ $weatherData['current']['weather_description'] }}</p>
            @endif
        </div>
        <div class="text-right">
            @php
                $code = $weatherData['current']['weather_code'] ?? 0;
                $icon = match(true) {
                    $code == 0 => 'fa-sun',
                    $code <= 3 => 'fa-cloud-sun',
                    $code <= 48 => 'fa-smog',
                    $code <= 65 => 'fa-cloud-rain',
                    $code <= 77 => 'fa-snowflake',
                    $code <= 82 => 'fa-cloud-showers-heavy',
                    default => 'fa-bolt',
                };
            @endphp
            <i class="fas {{ $icon }} text-8xl opacity-80"></i>
        </div>
    </div>
</div>

<!-- 7-Day Forecast -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-6">
        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>7-Day Forecast
    </h3>
    
    <div class="space-y-4">
        @foreach($weatherData['daily'] as $index => $day)
        <div class="flex items-center justify-between p-4 {{ $index == 0 ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-gray-50' }} rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center w-1/3">
                <div class="w-24">
                    <p class="font-semibold text-gray-800">{{ $day['day_name'] }}</p>
                    <p class="text-sm text-gray-500">{{ date('M d', strtotime($day['date'])) }}</p>
                </div>
            </div>
            
            <div class="flex items-center justify-center w-1/3">
                @php
                    $dayCode = $day['weather_code'];
                    $dayIcon = match(true) {
                        $dayCode == 0 => 'fa-sun text-yellow-500',
                        $dayCode <= 3 => 'fa-cloud-sun text-gray-500',
                        $dayCode <= 48 => 'fa-smog text-gray-400',
                        $dayCode <= 65 => 'fa-cloud-rain text-blue-500',
                        $dayCode <= 77 => 'fa-snowflake text-blue-300',
                        $dayCode <= 82 => 'fa-cloud-showers-heavy text-blue-600',
                        default => 'fa-bolt text-yellow-600',
                    };
                @endphp
                <i class="fas {{ $dayIcon }} text-3xl mr-4"></i>
                <span class="text-gray-600">{{ $day['weather_description'] }}</span>
            </div>
            
            <div class="flex items-center justify-end w-1/3 gap-6">
                <div class="text-center">
                    <p class="text-xs text-gray-500">High</p>
                    <p class="text-xl font-bold text-red-500">{{ round($day['temp_max']) }}°</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Low</p>
                    <p class="text-xl font-bold text-blue-500">{{ round($day['temp_min']) }}°</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500"><i class="fas fa-tint"></i></p>
                    <p class="text-sm text-gray-600">{{ $day['precipitation'] }} mm</p>
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
