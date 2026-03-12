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

    public function dashboard()
    {
        $stats = $this->weatherService->getDashboardStats();
        $allWeather = $this->weatherService->getCurrentWeather();
        
        return view('weather.dashboard', compact('stats', 'allWeather'));
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
