@extends('layouts.app')

@section('title', 'Compare Cities Weather')

@section('content')
<div class="mb-8">
    <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-600 bg-clip-text text-transparent mb-2">
        Perbandingan Cuaca Kota
    </h1>
    <p class="text-gray-600 text-lg">Lihat perbandingan cuaca antar kota secara berdampingan</p>
</div>

<!-- City Selector -->
<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mb-6">
    <form method="GET" action="{{ route('weather.comparison') }}" id="compareForm">
        <label class="font-semibold text-gray-700 block mb-4 text-lg">
            <i class="fas fa-list-check mr-2 text-blue-600"></i>Pilih Kota untuk Dibandingkan (2-5 kota):
        </label>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            @foreach($allCities as $city)
            <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-lg hover:bg-blue-50 transition">
                <input type="checkbox" name="cities[]" value="{{ $city }}" 
                    {{ in_array($city, $selectedCities) ? 'checked' : '' }}
                    class="w-5 h-5 rounded text-blue-500 focus:ring-blue-500 focus:ring-2">
                <span class="text-gray-700 font-medium">{{ $city }}</span>
            </label>
            @endforeach
        </div>
        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
            <i class="fas fa-rotate-right mr-2"></i>Bandingkan
        </button>
    </form>
</div>

<!-- Comparison Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($selectedCities), 5) }} gap-5 mb-6">
    @foreach($comparisonData as $city => $data)
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white overflow-hidden card-hover">
        <div class="bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 p-5 text-white text-center">
            <h3 class="text-2xl font-bold"><i class="fas fa-location-dot mr-2"></i>{{ $city }}</h3>
        </div>
        
        @if($data['success'] && $data['current'])
        <div class="p-6 text-center">
            @php
                $code = $data['current']['weather_code'];
                $icon = match(true) {
                    $code == 0 => 'fa-sun text-amber-500',
                    $code <= 3 => 'fa-cloud-sun text-orange-400',
                    $code <= 48 => 'fa-smog text-slate-400',
                    $code <= 65 => 'fa-cloud-rain text-blue-500',
                    $code <= 77 => 'fa-snowflake text-cyan-400',
                    $code <= 82 => 'fa-cloud-showers-heavy text-indigo-500',
                    default => 'fa-bolt text-yellow-500',
                };
            @endphp
            <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-sm border border-slate-100 mb-4">
                <i class="fas {{ $icon }} text-4xl"></i>
            </span>
            <p class="text-5xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
            <p class="text-gray-600 mb-6 capitalize">{{ $data['current']['weather_description'] }}</p>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between bg-blue-50 p-3 rounded-lg border border-blue-100">
                    <span class="text-gray-700">Terasa Seperti</span>
                    <span class="font-semibold text-blue-700">{{ round($data['current']['apparent_temperature']) }}°C</span>
                </div>
                <div class="flex justify-between bg-cyan-50 p-3 rounded-lg border border-cyan-100">
                    <span class="text-gray-700">Kelembaban</span>
                    <span class="font-semibold text-cyan-700">{{ $data['current']['humidity'] }}%</span>
                </div>
                <div class="flex justify-between bg-teal-50 p-3 rounded-lg border border-teal-100">
                    <span class="text-gray-700">Kecepatan Angin</span>
                    <span class="font-semibold text-teal-700">{{ round($data['current']['wind_speed']) }} km/h</span>
                </div>
                <div class="flex justify-between bg-purple-50 p-3 rounded-lg border border-purple-100">
                    <span class="text-gray-700">Presipitasi</span>
                    <span class="font-semibold text-purple-700">{{ $data['current']['precipitation'] }} mm</span>
                </div>
            </div>
        </div>
        @else
        <div class="p-6 text-center text-red-600">
            <p class="text-2xl mb-2"><i class="fas fa-triangle-exclamation"></i></p>
            <p class="font-medium">Data tidak tersedia</p>
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Comparison Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Temperature Comparison -->
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-temperature-half mr-2 text-red-500"></i>Perbandingan Suhu
        </h3>
        <canvas id="tempCompareChart" height="200"></canvas>
    </div>

    <!-- Humidity Comparison -->
    <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 card-hover">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-droplet mr-2 text-cyan-500"></i>Perbandingan Kelembaban
        </h3>
        <canvas id="humidityCompareChart" height="200"></canvas>
    </div>
</div>

<!-- Forecast Comparison Table -->
<div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md border border-white p-6 mt-8 card-hover">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-calendar-days mr-2 text-blue-600"></i>Perbandingan Prakiraan 3 Hari
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-blue-50 border-b-2 border-blue-200">
                    <th class="p-3 text-left font-semibold text-gray-700">Hari</th>
                    @foreach($selectedCities as $city)
                    <th class="p-3 text-center font-semibold text-gray-700">{{ $city }}</th>
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
