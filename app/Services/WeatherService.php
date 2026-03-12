<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1/forecast';
    
    // Indonesian cities with coordinates
    protected $cities = [
        'Jakarta' => ['lat' => -6.2088, 'lon' => 106.8456],
        'Surabaya' => ['lat' => -7.2575, 'lon' => 112.7521],
        'Bandung' => ['lat' => -6.9175, 'lon' => 107.6191],
        'Medan' => ['lat' => 3.5952, 'lon' => 98.6722],
        'Semarang' => ['lat' => -6.9666, 'lon' => 110.4196],
        'Makassar' => ['lat' => -5.1477, 'lon' => 119.4327],
        'Palembang' => ['lat' => -2.9761, 'lon' => 104.7754],
        'Denpasar' => ['lat' => -8.6705, 'lon' => 115.2126],
        'Yogyakarta' => ['lat' => -7.7956, 'lon' => 110.3695],
        'Malang' => ['lat' => -7.9666, 'lon' => 112.6326],
    ];

    public function getCities()
    {
        return $this->cities;
    }

    public function getCurrentWeather($city = null)
    {
        if ($city && isset($this->cities[$city])) {
            return $this->fetchWeatherForCity($city);
        }

        return Cache::remember('weather_all_cities', 600, function () {
            $weatherData = [];
            foreach ($this->cities as $cityName => $coords) {
                $weatherData[$cityName] = $this->fetchWeatherForCity($cityName);
            }
            return $weatherData;
        });
    }

    protected function fetchWeatherForCity($cityName)
    {
        $coords = $this->cities[$cityName];
        
        $cacheKey = "weather_{$cityName}";
        
        return Cache::remember($cacheKey, 600, function () use ($coords, $cityName) {
            try {
                $response = Http::get($this->baseUrl, [
                    'latitude' => $coords['lat'],
                    'longitude' => $coords['lon'],
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m',
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weather_code,sunrise,sunset',
                    'timezone' => 'Asia/Jakarta',
                    'forecast_days' => 7
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'city' => $cityName,
                        'coordinates' => $this->cities[$cityName],
                        'current' => [
                            'temperature' => $data['current']['temperature_2m'] ?? null,
                            'humidity' => $data['current']['relative_humidity_2m'] ?? null,
                            'apparent_temperature' => $data['current']['apparent_temperature'] ?? null,
                            'precipitation' => $data['current']['precipitation'] ?? null,
                            'weather_code' => $data['current']['weather_code'] ?? null,
                            'weather_description' => $this->getWeatherDescription($data['current']['weather_code'] ?? 0),
                            'wind_speed' => $data['current']['wind_speed_10m'] ?? null,
                            'wind_direction' => $data['current']['wind_direction_10m'] ?? null,
                        ],
                        'daily' => $this->formatDailyData($data['daily'] ?? []),
                        'success' => true
                    ];
                }
                
                return $this->getErrorResponse($cityName);
            } catch (\Exception $e) {
                return $this->getErrorResponse($cityName, $e->getMessage());
            }
        });
    }

    protected function formatDailyData($daily)
    {
        $formatted = [];
        $dates = $daily['time'] ?? [];
        
        for ($i = 0; $i < count($dates); $i++) {
            $formatted[] = [
                'date' => $dates[$i],
                'day_name' => date('l', strtotime($dates[$i])),
                'temp_max' => $daily['temperature_2m_max'][$i] ?? null,
                'temp_min' => $daily['temperature_2m_min'][$i] ?? null,
                'precipitation' => $daily['precipitation_sum'][$i] ?? null,
                'weather_code' => $daily['weather_code'][$i] ?? null,
                'weather_description' => $this->getWeatherDescription($daily['weather_code'][$i] ?? 0),
                'sunrise' => $daily['sunrise'][$i] ?? null,
                'sunset' => $daily['sunset'][$i] ?? null,
            ];
        }
        
        return $formatted;
    }

    protected function getWeatherDescription($code)
    {
        $descriptions = [
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing rime fog',
            51 => 'Light drizzle',
            53 => 'Moderate drizzle',
            55 => 'Dense drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            71 => 'Slight snow',
            73 => 'Moderate snow',
            75 => 'Heavy snow',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with hail',
            99 => 'Thunderstorm with heavy hail',
        ];
        
        return $descriptions[$code] ?? 'Unknown';
    }

    public function getWeatherIcon($code)
    {
        $icons = [
            0 => 'fa-sun',
            1 => 'fa-sun',
            2 => 'fa-cloud-sun',
            3 => 'fa-cloud',
            45 => 'fa-smog',
            48 => 'fa-smog',
            51 => 'fa-cloud-rain',
            53 => 'fa-cloud-rain',
            55 => 'fa-cloud-showers-heavy',
            61 => 'fa-cloud-rain',
            63 => 'fa-cloud-rain',
            65 => 'fa-cloud-showers-heavy',
            71 => 'fa-snowflake',
            73 => 'fa-snowflake',
            75 => 'fa-snowflake',
            80 => 'fa-cloud-sun-rain',
            81 => 'fa-cloud-rain',
            82 => 'fa-cloud-showers-heavy',
            95 => 'fa-bolt',
            96 => 'fa-bolt',
            99 => 'fa-bolt',
        ];
        
        return $icons[$code] ?? 'fa-question';
    }

    protected function getErrorResponse($cityName, $message = 'Failed to fetch data')
    {
        return [
            'city' => $cityName,
            'coordinates' => $this->cities[$cityName] ?? null,
            'current' => null,
            'daily' => [],
            'success' => false,
            'error' => $message
        ];
    }

    public function refreshCache()
    {
        Cache::forget('weather_all_cities');
        foreach (array_keys($this->cities) as $city) {
            Cache::forget("weather_{$city}");
        }
        return $this->getCurrentWeather();
    }

    public function getDashboardStats()
    {
        $allWeather = $this->getCurrentWeather();
        
        $temperatures = [];
        $humidities = [];
        $precipitations = [];
        
        foreach ($allWeather as $city => $data) {
            if ($data['success'] && $data['current']) {
                $temperatures[$city] = $data['current']['temperature'];
                $humidities[$city] = $data['current']['humidity'];
                $precipitations[$city] = $data['current']['precipitation'];
            }
        }
        
        return [
            'total_cities' => count($this->cities),
            'hottest_city' => array_search(max($temperatures), $temperatures),
            'hottest_temp' => max($temperatures),
            'coolest_city' => array_search(min($temperatures), $temperatures),
            'coolest_temp' => min($temperatures),
            'avg_temperature' => round(array_sum($temperatures) / count($temperatures), 1),
            'avg_humidity' => round(array_sum($humidities) / count($humidities), 1),
            'temperatures' => $temperatures,
            'humidities' => $humidities,
            'precipitations' => $precipitations,
        ];
    }
}
