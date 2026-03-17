<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Kebidanan Sehat - Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .service-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .service-card:hover {
            transform: translateY(-5px);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #5a6fd8, #6a4190);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="index.php">
                <i class="fas fa-heartbeat me-2"></i>Klinik Kebidanan Sehat
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">Login Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Layanan Kebidanan Terpercaya</h1>
                    <p class="lead mb-4">Memberikan pelayanan kesehatan ibu dan anak dengan standar medis terbaik dan teknologi modern.</p>
                    <a href="register.php" class="btn btn-primary btn-lg me-3">Daftar Sekarang</a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">Login</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.pexels.com/photos/3845810/pexels-photo-3845810.jpeg?auto=compress&cs=tinysrgb&w=600" 
                         alt="Klinik Kebidanan" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Layanan Kami</h2>
                <p class="text-muted">Pelayanan kesehatan komprehensif untuk ibu dan anak</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-baby fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Pemeriksaan Kehamilan</h5>
                            <p class="card-text">Pemeriksaan rutin kehamilan dengan teknologi USG terkini untuk memantau perkembangan janin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Konsultasi Dokter</h5>
                            <p class="card-text">Konsultasi dengan dokter spesialis kandungan berpengalaman untuk kesehatan ibu dan anak.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Booking Online</h5>
                            <p class="card-text">Sistem booking online yang mudah dan praktis untuk mengatur jadwal kunjungan Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">Hubungi Kami</h3>
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-3"></i>
                        <span>Jl. Kesehatan No. 123, Jakarta Selatan</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone text-primary me-3"></i>
                        <span>(021) 1234-5678</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope text-primary me-3"></i>
                        <span>info@klinikkebidanansehat.com</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-clock text-primary me-3"></i>
                        <span>Senin - Sabtu: 08:00 - 20:00</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">Jam Operasional</h3>
                    <table class="table">
                        <tr>
                            <td>Senin - Jumat</td>
                            <td>08:00 - 20:00</td>
                        </tr>
                        <tr>
                            <td>Sabtu</td>
                            <td>08:00 - 16:00</td>
                        </tr>
                        <tr>
                            <td>Minggu</td>
                            <td>Tutup</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Klinik Kebidanan Sehat. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>