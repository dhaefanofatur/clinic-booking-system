# Sistem Booking Klinik Kebidanan

Website booking klinik kebidanan dengan PHP dan MySQL yang dilengkapi dengan sistem login untuk pasien dan admin.

## Fitur Utama

### Untuk Pasien:
- Registrasi dan login pasien
- Dashboard pasien
- Booking appointment online
- Melihat jadwal appointment
- Mengelola profil

### Untuk Admin:
- Login admin
- Dashboard admin dengan statistik
- Mengelola appointment
- Mengelola data pasien
- Mengelola data dokter
- Mengelola layanan

## Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5
- **Icons**: Font Awesome 6

## Instalasi

1. **Persiapan Database**
   ```sql
   -- Jalankan script SQL di database/schema.sql
   -- untuk membuat database dan tabel yang diperlukan
   ```

2. **Konfigurasi Database**
   - Edit file `config/database.php`
   - Sesuaikan pengaturan koneksi database:
     ```php
     $host = 'localhost';
     $dbname = 'clinic_booking';
     $username = 'root';
     $password = '';
     ```

3. **Setup Web Server**
   - Pastikan PHP dan MySQL sudah terinstall
   - Letakkan semua file di direktori web server (htdocs/www)
   - Akses melalui browser: `http://localhost/clinic-booking`

## Login Credentials

### Admin
- **Username**: admin
- **Password**: password

### Pasien
- Daftar melalui halaman registrasi atau gunakan data yang sudah ada di database

## Struktur Database

### Tabel Utama:
- `patients` - Data pasien
- `admins` - Data admin
- `doctors` - Data dokter
- `services` - Data layanan
- `appointments` - Data appointment/booking

## Fitur Keamanan

- Password hashing menggunakan PHP `password_hash()`
- Session management untuk autentikasi
- Input validation dan sanitization
- Prepared statements untuk mencegah SQL injection

## Halaman Utama

1. **index.php** - Halaman beranda
2. **services.php** - Halaman layanan
3. **login.php** - Login pasien
4. **register.php** - Registrasi pasien
5. **admin/login.php** - Login admin

## Dashboard

### Pasien (`patient/`)
- `dashboard.php` - Dashboard utama
- `booking.php` - Form booking baru
- `appointments.php` - Daftar appointment
- `profile.php` - Profil pasien

### Admin (`admin/`)
- `dashboard.php` - Dashboard admin
- `appointments.php` - Manajemen appointment
- `patients.php` - Manajemen pasien
- `doctors.php` - Manajemen dokter
- `services.php` - Manajemen layanan

## Kontribusi

Silakan fork repository ini dan buat pull request untuk kontribusi.

## Lisensi

MIT License - Silakan gunakan untuk keperluan pembelajaran dan komersial.