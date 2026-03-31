<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function dashboard(Request $request)
    {
        $filters = [
            'city_search' => $request->query('city_search', ''),
            'city_letter' => $request->query('city_letter', ''),
            'humidity_level' => $request->query('humidity_level', ''),
        ];

        $stats = $this->weatherService->getDashboardStats();
        $allWeather = $this->weatherService->getCurrentWeather();
        $filteredWeather = $this->applyFiltersToWeather($allWeather, $filters);
        
        return view('weather.dashboard', compact('stats', 'allWeather', 'filteredWeather', 'filters'));
    }

    protected function applyFiltersToWeather($allWeather, $filters)
    {
        $filtered = [];

        foreach ($allWeather as $city => $data) {
            $include = true;

            if (!empty($filters['city_search'])) {
                $search = strtolower($filters['city_search']);
                if (strpos(strtolower($city), $search) === false) {
                    $include = false;
                }
            }

            if (!empty($filters['city_letter'])) {
                if (strtolower($city[0]) !== strtolower($filters['city_letter'])) {
                    $include = false;
                }
            }

            if (!empty($filters['humidity_level']) && $data['success'] && $data['current']) {
                $humidity = $data['current']['humidity'];
                switch ($filters['humidity_level']) {
                    case 'low':
                        if ($humidity > 60) $include = false;
                        break;
                    case 'medium':
                        if ($humidity <= 60 || $humidity > 80) $include = false;
                        break;
                    case 'high':
                        if ($humidity <= 80) $include = false;
                        break;
                }
            }

            if ($include) {
                $filtered[$city] = $data;
            }
        }

        return $filtered;
    }

    public function cities()
    {
        $allWeather = $this->weatherService->getCurrentWeather();
        $cities = $this->weatherService->getCities();
        
        return view('weather.cities', compact('allWeather', 'cities'));
    }

    public function forecast(Request $request)
    {
        $selectedCity = $request->get('city', 'Jakarta');
        $weatherData = $this->weatherService->getCurrentWeather($selectedCity);
        $cities = array_keys($this->weatherService->getCities());
        
        return view('weather.forecast', compact('weatherData', 'cities', 'selectedCity'));
    }

    public function comparison(Request $request)
    {
        $selectedCities = $request->get('cities', ['Jakarta', 'Surabaya', 'Bandung']);
        
        if (is_string($selectedCities)) {
            $selectedCities = explode(',', $selectedCities);
        }
        
        $comparisonData = [];
        foreach ($selectedCities as $city) {
            $comparisonData[$city] = $this->weatherService->getCurrentWeather($city);
        }
        
        $allCities = array_keys($this->weatherService->getCities());
        
        return view('weather.comparison', compact('comparisonData', 'allCities', 'selectedCities'));
    }

    public function refresh()
    {
        $this->weatherService->refreshCache();
        return redirect()->route('weather.dashboard')->with('success', 'Weather data refreshed successfully!');
    }
}
