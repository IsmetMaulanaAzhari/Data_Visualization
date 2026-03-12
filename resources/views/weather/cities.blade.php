@extends('layouts.app')

@section('title', 'Weather - All Cities')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-city text-blue-500 mr-3"></i>All Cities Weather
    </h1>
    <p class="text-gray-600">Detailed weather information for all Indonesian cities</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($allWeather as $city => $data)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
            <h3 class="text-xl font-semibold">
                <i class="fas fa-map-marker-alt mr-2"></i>{{ $city }}
            </h3>
            <p class="text-sm text-blue-100">
                Lat: {{ $cities[$city]['lat'] }}, Lon: {{ $cities[$city]['lon'] }}
            </p>
        </div>
        
        @if($data['success'] && $data['current'])
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
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
                    <i class="fas {{ $icon }} text-5xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-gray-800">{{ round($data['current']['temperature']) }}°C</p>
                    <p class="text-gray-500">{{ $data['current']['weather_description'] }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500"><i class="fas fa-temperature-low mr-1"></i>Feels Like</p>
                    <p class="font-semibold text-gray-800">{{ round($data['current']['apparent_temperature']) }}°C</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500"><i class="fas fa-tint mr-1"></i>Humidity</p>
                    <p class="font-semibold text-gray-800">{{ $data['current']['humidity'] }}%</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500"><i class="fas fa-wind mr-1"></i>Wind Speed</p>
                    <p class="font-semibold text-gray-800">{{ round($data['current']['wind_speed']) }} km/h</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500"><i class="fas fa-cloud-rain mr-1"></i>Precipitation</p>
                    <p class="font-semibold text-gray-800">{{ $data['current']['precipitation'] }} mm</p>
                </div>
            </div>

            <!-- Mini 3-Day Forecast -->
            @if(count($data['daily']) >= 3)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-500 mb-2">Next 3 Days</p>
                <div class="flex justify-between">
                    @for($i = 1; $i < 4 && $i < count($data['daily']); $i++)
                    <div class="text-center">
                        <p class="text-xs text-gray-500">{{ substr($data['daily'][$i]['day_name'], 0, 3) }}</p>
                        @php
                            $dayCode = $data['daily'][$i]['weather_code'];
                            $dayIcon = match(true) {
                                $dayCode == 0 => 'fa-sun text-yellow-500',
                                $dayCode <= 3 => 'fa-cloud-sun text-gray-500',
                                $dayCode <= 48 => 'fa-smog text-gray-400',
                                $dayCode <= 65 => 'fa-cloud-rain text-blue-500',
                                default => 'fa-bolt text-yellow-600',
                            };
                        @endphp
                        <i class="fas {{ $dayIcon }} my-1"></i>
                        <p class="text-xs font-semibold">{{ round($data['daily'][$i]['temp_max']) }}°</p>
                    </div>
                    @endfor
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="p-6 text-center text-red-500">
            <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
            <p>Weather data unavailable</p>
        </div>
        @endif
    </div>
    @endforeach
</div>

<div class="mt-6 text-center">
    <a href="{{ route('weather.refresh') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        <i class="fas fa-sync-alt mr-2"></i>Refresh All Data
    </a>
</div>
@endsection
