<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get all appointments for this patient
$stmt = $pdo->prepare("
    SELECT a.*, d.name as doctor_name, s.name as service_name, s.price 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN services s ON a.service_id = s.id 
    WHERE a.patient_id = ? 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$_SESSION['patient_id']]);
$appointments = $stmt->fetchAll();

// Handle appointment cancellation
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
    if ($stmt->execute([$appointment_id, $_SESSION['patient_id']])) {
        header('Location: appointments.php?success=cancelled');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Saya - Klinik Kebidanan Sehat</title>
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
        .appointment-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .appointment-card:hover {
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
                        <i class="fas fa-heartbeat me-2"></i>Klinik Sehat
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-calendar-plus me-2"></i>Booking Baru
                        </a>
                        <a class="nav-link active" href="appointments.php">
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
                        <h2 class="fw-bold">Jadwal Saya</h2>
                        <a href="booking.php" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Booking Baru
                        </a>
                    </div>
                    
                    <?php if (isset($_GET['success']) && $_GET['success'] == 'cancelled'): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            Appointment berhasil dibatalkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($appointments)): ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted">Belum Ada Appointment</h4>
                                <p class="text-muted mb-4">Anda belum memiliki jadwal appointment. Buat booking baru sekarang!</p>
                                <a href="booking.php" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>Buat Booking Baru
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($appointments as $appointment): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card appointment-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($appointment['service_name']); ?></h6>
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
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <i class="fas fa-user-md text-primary me-2"></i>
                                                <strong><?php echo htmlspecialchars($appointment['doctor_name']); ?></strong>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-calendar text-primary me-2"></i>
                                                <?php echo date('d F Y', strtotime($appointment['appointment_date'])); ?>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-clock text-primary me-2"></i>
                                                <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-money-bill text-primary me-2"></i>
                                                Rp <?php echo number_format($appointment['price'], 0, ',', '.'); ?>
                                            </div>
                                            <?php if ($appointment['notes']): ?>
                                                <div class="mb-2">
                                                    <i class="fas fa-sticky-note text-primary me-2"></i>
                                                    <small class="text-muted"><?php echo htmlspecialchars($appointment['notes']); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed'): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan appointment ini?')">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                    <button type="submit" name="cancel_appointment" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-times me-1"></i>Batalkan
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <small class="text-muted float-end">
                                                Dibuat: <?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>