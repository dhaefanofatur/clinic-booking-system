<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get patient info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch();

// Get upcoming appointments
$stmt = $pdo->prepare("
    SELECT a.*, d.name as doctor_name, s.name as service_name 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN services s ON a.service_id = s.id 
    WHERE a.patient_id = ? AND a.appointment_date >= CURDATE() 
    ORDER BY a.appointment_date, a.appointment_time
");
$stmt->execute([$_SESSION['patient_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - Klinik Kebidanan Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .stat-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h5 class="text-white mb-4">
                        <i class="fas fa-heartbeat me-2"></i>Klinik Sehat
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-calendar-plus me-2"></i>Booking Baru
                        </a>
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-calendar-check me-2"></i>Jadwal Saya
                        </a>
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profil
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold">Dashboard</h2>
                        <div class="text-muted">
                            Selamat datang, <?php echo htmlspecialchars($patient['name']); ?>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check fa-2x me-3"></i>
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo count($appointments); ?></h5>
                                            <p class="card-text">Jadwal Mendatang</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-md fa-2x text-primary me-3"></i>
                                        <div>
                                            <h5 class="card-title mb-0">3</h5>
                                            <p class="card-text text-muted">Dokter Tersedia</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-stethoscope fa-2x text-success me-3"></i>
                                        <div>
                                            <h5 class="card-title mb-0">5</h5>
                                            <p class="card-text text-muted">Layanan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Aksi Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <a href="booking.php" class="btn btn-primary w-100">
                                                <i class="fas fa-calendar-plus me-2"></i>Booking Baru
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="appointments.php" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-calendar-check me-2"></i>Lihat Jadwal
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="profile.php" class="btn btn-outline-secondary w-100">
                                                <i class="fas fa-user me-2"></i>Edit Profil
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="../index.php" class="btn btn-outline-info w-100">
                                                <i class="fas fa-home me-2"></i>Beranda
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Appointments -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Jadwal Mendatang</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($appointments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada jadwal mendatang</p>
                                    <a href="booking.php" class="btn btn-primary">Buat Booking Baru</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Dokter</th>
                                                <th>Layanan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                                    <td>
                                                        <?php
                                                        $status_class = '';
                                                        switch($appointment['status']) {
                                                            case 'pending': $status_class = 'warning'; break;
                                                            case 'confirmed': $status_class = 'success'; break;
                                                            case 'completed': $status_class = 'info'; break;
                                                            case 'cancelled': $status_class = 'danger'; break;
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $status_class; ?>">
                                                            <?php echo ucfirst($appointment['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>