<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get statistics
$total_patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pending_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$today_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();

// Get recent appointments
$stmt = $pdo->query("
    SELECT a.*, p.name as patient_name, d.name as doctor_name, s.name as service_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN services s ON a.service_id = s.id 
    ORDER BY a.created_at DESC 
    LIMIT 10
");
$recent_appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Klinik Kebidanan Sehat</title>
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
        .stat-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
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
                        <i class="fas fa-user-shield me-2"></i>Admin Panel
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-calendar-check me-2"></i>Appointments
                        </a>
                        <a class="nav-link" href="patients.php">
                            <i class="fas fa-users me-2"></i>Patients
                        </a>
                        <a class="nav-link" href="doctors.php">
                            <i class="fas fa-user-md me-2"></i>Doctors
                        </a>
                        <a class="nav-link" href="services.php">
                            <i class="fas fa-stethoscope me-2"></i>Services
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
                        <h2 class="fw-bold">Dashboard Admin</h2>
                        <div class="text-muted">
                            Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card text-white" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users fa-2x me-3"></i>
                                        <div>
                                            <h4 class="mb-0"><?php echo $total_patients; ?></h4>
                                            <p class="mb-0">Total Pasien</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card text-white bg-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check fa-2x me-3"></i>
                                        <div>
                                            <h4 class="mb-0"><?php echo $total_appointments; ?></h4>
                                            <p class="mb-0">Total Appointments</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card text-white bg-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x me-3"></i>
                                        <div>
                                            <h4 class="mb-0"><?php echo $pending_appointments; ?></h4>
                                            <p class="mb-0">Pending</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card text-white bg-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-day fa-2x me-3"></i>
                                        <div>
                                            <h4 class="mb-0"><?php echo $today_appointments; ?></h4>
                                            <p class="mb-0">Hari Ini</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Appointments -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Appointments Terbaru</h5>
                            <a href="appointments.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_appointments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada appointment</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Pasien</th>
                                                <th>Dokter</th>
                                                <th>Layanan</th>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
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
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="appointment_detail.php?id=<?php echo $appointment['id']; ?>" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
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