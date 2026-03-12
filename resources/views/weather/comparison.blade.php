@extends('layouts.app')

@section('title', 'Compare Cities Weather')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-balance-scale text-blue-500 mr-3"></i>Compare Cities Weather
    </h1>
    <p class="text-gray-600">Side-by-side weather comparison</p>
</div>

<!-- City Selector -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('weather.comparison') }}" id="compareForm">
        <label class="font-semibold text-gray-700 block mb-3">
            <i class="fas fa-check-square mr-2"></i>Select Cities to Compare (2-5 cities):
        </label>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
            @foreach($allCities as $city)
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="cities[]" value="{{ $city }}" 
                    {{ in_array($city, $selectedCities) ? 'checked' : '' }}
                    class="rounded text-blue-500 focus:ring-blue-500">
                <span class="text-gray-700">{{ $city }}</span>
            </label>
            @endforeach
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-sync-alt mr-2"></i>Compare
        </button>
    </form>
</div>

<!-- Comparison Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($selectedCities), 5) }} gap-4 mb-6">
    @foreach($comparisonData as $city => $data)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white text-center">
            <h3 class="text-xl font-bold">{{ $city }}</h3>
        </div>
        
        @if($data['success'] && $data['current'])
        <div class="p-6 text-center">
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
            <i class="fas {{ $icon }} text-5xl mb-3"></i>
            <p class="text-4xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
            <p class="text-gray-500 mb-4">{{ $data['current']['weather_description'] }}</p>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between bg-gray-50 p-2 rounded">
                    <span class="text-gray-500">Feels Like</span>
                    <span class="font-semibold">{{ round($data['current']['apparent_temperature']) }}°C</span>
                </div>
                <div class="flex justify-between bg-gray-50 p-2 rounded">
                    <span class="text-gray-500">Humidity</span>
                    <span class="font-semibold">{{ $data['current']['humidity'] }}%</span>
                </div>
                <div class="flex justify-between bg-gray-50 p-2 rounded">
                    <span class="text-gray-500">Wind</span>
                    <span class="font-semibold">{{ round($data['current']['wind_speed']) }} km/h</span>
                </div>
                <div class="flex justify-between bg-gray-50 p-2 rounded">
                    <span class="text-gray-500">Precipitation</span>
                    <span class="font-semibold">{{ $data['current']['precipitation'] }} mm</span>
                </div>
            </div>
        </div>
        @else
        <div class="p-6 text-center text-red-500">
            <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
            <p>Data unavailable</p>
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Comparison Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Temperature Comparison -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-thermometer-half text-red-500 mr-2"></i>Temperature Comparison
        </h3>
        <canvas id="tempCompareChart" height="200"></canvas>
    </div>

    <!-- Humidity Comparison -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-tint text-blue-500 mr-2"></i>Humidity Comparison
        </h3>
        <canvas id="humidityCompareChart" height="200"></canvas>
    </div>
</div>

<!-- Forecast Comparison Table -->
<div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-table text-green-500 mr-2"></i>3-Day Forecast Comparison
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-3 text-left">Day</th>
                    @foreach($selectedCities as $city)
                    <th class="p-3 text-center">{{ $city }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 3; $i++)
                <tr class="border-t">
                    <td class="p-3 font-semibold">
                        @if(isset($comparisonData[array_key_first($comparisonData)]['daily'][$i]))
                            {{ $comparisonData[array_key_first($comparisonData)]['daily'][$i]['day_name'] }}
                        @endif
                    </td>
                    @foreach($selectedCities as $city)
                    <td class="p-3 text-center">
                        @if(isset($comparisonData[$city]['daily'][$i]))
                            @php $day = $comparisonData[$city]['daily'][$i]; @endphp
                            <span class="text-red-500 font-bold">{{ round($day['temp_max']) }}°</span>
                            <span class="text-gray-400">/</span>
                            <span class="text-blue-500">{{ round($day['temp_min']) }}°</span>
                        @else
                            -
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cities = {!! json_encode($selectedCities) !!};
    const temps = [];
    const humidities = [];
    
    @foreach($comparisonData as $city => $data)
        @if($data['success'] && $data['current'])
        temps.push({{ $data['current']['temperature'] }});
        humidities.push({{ $data['current']['humidity'] }});
        @else
        temps.push(0);
        humidities.push(0);
        @endif
    @endforeach

    // Temperature Chart
    new Chart(document.getElementById('tempCompareChart'), {
        type: 'bar',
        data: {
            labels: cities,
            datasets: [{
                label: 'Temperature (°C)',
                data: temps,
                backgroundColor: temps.map(t => t >= 30 ? 'rgba(239, 68, 68, 0.8)' : 'rgba(59, 130, 246, 0.8)'),
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: false, min: 20 } }
        }
    });

    // Humidity Chart
    new Chart(document.getElementById('humidityCompareChart'), {
        type: 'bar',
        data: {
            labels: cities,
            datasets: [{
                label: 'Humidity (%)',
                data: humidities,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
});
</script>
@endsection
