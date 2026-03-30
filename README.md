# Data Visualization Dashboard

Project ini sekarang hanya memiliki **2 sumber data non-dummy**:
- Weather API (Open-Meteo)
- Student Dataset CSV (file lokal di storage)

Seluruh modul data dummy (dashboard penjualan, CRUD product/customer/order/category, dan DummyJSON API) sudah dihapus.

## Fitur Aktif

### 1) Weather API Dashboard
- Dashboard cuaca kota-kota Pulau Jawa
- Halaman cities, 7-day forecast, dan comparison
- Refresh cache cuaca
- Data real-time dari Open-Meteo API

Routes:
- `/weather`
- `/weather/cities`
- `/weather/forecast`
- `/weather/comparison`
- `/weather/refresh`

Sumber API:
- Base URL: `https://api.open-meteo.com/v1/forecast`
- Dokumentasi: `https://open-meteo.com/en/docs`

Contoh request (Jakarta):
- `https://api.open-meteo.com/v1/forecast?latitude=-6.2088&longitude=106.8456&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,weather_code,sunrise,sunset&timezone=Asia/Jakarta&forecast_days=7`

Parameter utama yang dipakai aplikasi:
- `latitude`, `longitude`
- `current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m`
- `daily=temperature_2m_max,temperature_2m_min,precipitation_sum,weather_code,sunrise,sunset`
- `timezone=Asia/Jakarta`
- `forecast_days=7`

### 2) Student Dataset Dashboard
- Visualisasi dari CSV `storage/app/datasets/ultimate_student_productivity_dataset_5000.csv`
- Filter berdasarkan academic level, gender, internet quality
- Refresh cache dataset
- Endpoint JSON untuk konsumsi seperti API

Routes:
- `/student-productivity`
- `/student-productivity/upload` (POST)
- `/student-productivity/refresh` (POST)
- `/student-productivity/api`

Catatan modul Student Dataset:
- Upload CSV **tidak ditampilkan di UI dashboard**.
- Endpoint upload (`/student-productivity/upload`) masih tersedia bila ingin dipakai secara internal.

## Instalasi

```bash
git clone https://github.com/IsmetMaulanaAzhari/Data_Visualization.git
cd Data_Visualization
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Akses aplikasi:
- `http://localhost:8000` (redirect ke weather)

## Struktur Inti

```text
app/
  Http/Controllers/
    StudentProductivityController.php
    WeatherController.php
  Services/
    StudentProductivityService.php
    WeatherService.php

resources/views/
  layouts/app.blade.php
  student-productivity/dashboard.blade.php
  weather/
    dashboard.blade.php
    cities.blade.php
    forecast.blade.php
    comparison.blade.php
```

## Catatan

- Dataset CSV disimpan di disk `local` (`storage/app/...`) sehingga tidak bisa diakses langsung dari URL publik.
- Weather service menggunakan cache 10 menit (600 detik) per kota untuk mengurangi API calls.
- Bila ingin mengganti dataset, gunakan endpoint POST `/student-productivity/upload`.

## License

MIT
