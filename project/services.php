<?php
require_once 'config/database.php';

// Get all services
$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - Klinik Kebidanan Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
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
                        <a class="nav-link active" href="services.php">Layanan</a>
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
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-4">Layanan Kami</h1>
                <p class="lead">Pelayanan kesehatan komprehensif untuk ibu dan anak dengan standar medis terbaik</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <i class="fas fa-stethoscope fa-3x text-primary"></i>
                                </div>
                                <h5 class="card-title text-center"><?php echo htmlspecialchars($service['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold">
                                        Rp <?php echo number_format($service['price'], 0, ',', '.'); ?>
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i><?php echo $service['duration']; ?> menit
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="login.php" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center">
                <h3 class="fw-bold mb-4">Siap untuk Memulai?</h3>
                <p class="lead mb-4">Daftar sekarang dan dapatkan pelayanan kesehatan terbaik untuk Anda dan keluarga</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </a>
                    <a href="login.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
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