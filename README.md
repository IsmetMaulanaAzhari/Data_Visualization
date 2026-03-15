# Data Visualization Dashboard

Aplikasi web visualisasi data berbasis **Laravel** yang memiliki dua mode tampilan: **Database Dashboard** (data penjualan dummy) dan **Weather API Dashboard** (data cuaca real-time kota-kota di Pulau Jawa menggunakan Open-Meteo API).

---

## Fitur Utama

### Database Dashboard (Data Penjualan)
- Ringkasan statistik: Total Revenue, Total Orders, Total Customers, Total Products
- Filter data berdasarkan **periode** (30 / 90 / 180 / 365 hari / semua) dan **status order**
- Grafik Penjualan Bulanan (Line Chart)
- Grafik Penjualan per Kategori (Doughnut Chart)
- Top 10 Produk Terlaris (Bar Chart horizontal)
- Distribusi Status Order (Pie Chart)
- Grafik Penjualan Harian (Bar Chart)
- Tabel Top 5 Customers berdasarkan total spending
- Tabel Recent Orders (10 terbaru)
- CRUD lengkap untuk: **Kategori**, **Produk**, **Customers**, dan **Orders**

### Weather API Dashboard (Data Cuaca – Pulau Jawa)
- Data cuaca real-time dari **15 kota di Pulau Jawa**:
  Jakarta, Bandung, Semarang, Yogyakarta, Surabaya, Malang, Cirebon, Serang, Cilegon, Bogor, Sukabumi, Bekasi, Depok, Tangerang, Tasikmalaya
- Grafik suhu dan kelembapan per kota
- Halaman **Cities**: detail cuaca + mini forecast 3 hari tiap kota
- Halaman **7-Day Forecast**: prediksi cuaca 7 hari pilihan kota + line chart tren suhu
- Halaman **Compare Cities**: perbandingan cuaca 2–5 kota secara side-by-side
- Sumber data: [Open-Meteo API](https://open-meteo.com/) (gratis, tanpa API key)

### UI / UX
- Sidebar collapsible (buka/tutup, state tersimpan di localStorage)
- Toggle mode **Database ↔ Weather API** di sidebar
- Responsif (Tailwind CSS)

---

## Tech Stack

| Komponen | Detail |
|---|---|
| Backend Framework | Laravel 11 (PHP 8.2+) |
| Frontend | Blade Templates, Tailwind CSS (CDN), Chart.js |
| Icons | Font Awesome 6 |
| Database | MySQL |
| External API | [Open-Meteo](https://open-meteo.com/) (Weather) |
| Caching | Laravel Cache (10 menit untuk data cuaca) |

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/IsmetMaulanaAzhari/Data_Visualization.git
cd Data_Visualization
```

### 2. Install dependencies

```bash
composer install
```

### 3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_visualization
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi dan Seeder

```bash
php artisan migrate
php artisan db:seed --class=SalesDataSeeder
```

Seeder akan membuat:
- 6 kategori produk
- ±41 produk
- 50 customers
- 500 orders dengan item detail

### 5. Jalankan server

```bash
php artisan serve
```

Akses di: `http://localhost:8000`

---

## Halaman & Routes

### Database Mode

| URL | Halaman |
|---|---|
| `/` | Dashboard utama (charts + statistik) |
| `/categories` | CRUD Kategori |
| `/products` | CRUD Produk |
| `/customers` | CRUD Customers |
| `/orders` | CRUD Orders |

### Weather API Mode

| URL | Halaman |
|---|---|
| `/weather` | Dashboard cuaca (suhu & kelembapan semua kota) |
| `/weather/cities` | Detail cuaca per kota |
| `/weather/forecast?city=Jakarta` | Forecast 7 hari kota tertentu |
| `/weather/comparison?cities[]=Jakarta&cities[]=Bandung` | Perbandingan cuaca antar kota |
| `/weather/refresh` | Reset cache data cuaca |

---

## Struktur Proyek

```
app/
├── Http/Controllers/
│   ├── DashboardController.php       # Dashboard penjualan + filter
│   ├── CategoryController.php        # CRUD Kategori
│   ├── ProductController.php         # CRUD Produk
│   ├── CustomerController.php        # CRUD Customers
│   ├── OrderController.php           # CRUD Orders
│   └── WeatherController.php         # Dashboard cuaca (API)
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── Customer.php
│   ├── Order.php
│   └── OrderItem.php
└── Services/
    └── WeatherService.php            # Fetch + cache data Open-Meteo

resources/views/
├── layouts/app.blade.php             # Layout utama + sidebar
├── dashboard/index.blade.php         # Dashboard penjualan
├── weather/
│   ├── dashboard.blade.php           # Weather overview
│   ├── cities.blade.php              # Detail tiap kota
│   ├── forecast.blade.php            # Forecast 7 hari
│   └── comparison.blade.php          # Perbandingan kota
└── [categories|products|customers|orders]/  # CRUD views
```

---

## Sumber Data

- **Data Penjualan**: Data dummy yang di-generate menggunakan Laravel Seeder (`SalesDataSeeder`)
- **Data Cuaca**: Diambil secara real-time dari [Open-Meteo API](https://open-meteo.com/) — gratis, tidak memerlukan API key, dikache selama 10 menit

---

## Lisensi

MIT License
