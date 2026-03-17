<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $appointment_id])) {
        $success = 'Status appointment berhasil diperbarui!';
    } else {
        $error = 'Terjadi kesalahan saat memperbarui status!';
    }
}

// Handle appointment deletion
if (isset($_POST['delete_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    if ($stmt->execute([$appointment_id])) {
        $success = 'Appointment berhasil dihapus!';
    } else {
        $error = 'Terjadi kesalahan saat menghapus appointment!';
    }
}

// Get all appointments with filters
$where_clause = "1=1";
$params = [];

if (isset($_GET['status']) && $_GET['status'] != '') {
    $where_clause .= " AND a.status = ?";
    $params[] = $_GET['status'];
}

if (isset($_GET['date']) && $_GET['date'] != '') {
    $where_clause .= " AND a.appointment_date = ?";
    $params[] = $_GET['date'];
}

$stmt = $pdo->prepare("
    SELECT a.*, p.name as patient_name, p.phone as patient_phone, 
           d.name as doctor_name, s.name as service_name, s.price 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN services s ON a.service_id = s.id 
    WHERE $where_clause
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Appointments - Klinik Kebidanan Sehat</title>
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="appointments.php">
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
                        <h2 class="fw-bold">Kelola Appointments</h2>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Filter Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date" class="form-label">Filter Tanggal</label>
                                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-2"></i>Filter
                                        </button>
                                        <a href="appointments.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Appointments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Appointments (<?php echo count($appointments); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($appointments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada appointment ditemukan</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pasien</th>
                                                <th>Dokter</th>
                                                <th>Layanan</th>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Status</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <tr>
                                                    <td>#<?php echo str_pad($appointment['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($appointment['patient_phone']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                                                    <td>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                                <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                                <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                            <input type="hidden" name="update_status" value="1">
                                                        </form>
                                                    </td>
                                                    <td>Rp <?php echo number_format($appointment['price'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $appointment['id']; ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus appointment ini?')">
                                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                                <button type="submit" name="delete_appointment" class="btn btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Detail Modal -->
                                                <div class="modal fade" id="detailModal<?php echo $appointment['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Appointment #<?php echo str_pad($appointment['id'], 4, '0', STR_PAD_LEFT); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Pasien:</strong></div>
                                                                    <div class="col-6"><?php echo htmlspecialchars($appointment['patient_name']); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Telepon:</strong></div>
                                                                    <div class="col-6"><?php echo htmlspecialchars($appointment['patient_phone']); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Dokter:</strong></div>
                                                                    <div class="col-6"><?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Layanan:</strong></div>
                                                                    <div class="col-6"><?php echo htmlspecialchars($appointment['service_name']); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Tanggal:</strong></div>
                                                                    <div class="col-6"><?php echo date('d F Y', strtotime($appointment['appointment_date'])); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Waktu:</strong></div>
                                                                    <div class="col-6"><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Status:</strong></div>
                                                                    <div class="col-6">
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
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Harga:</strong></div>
                                                                    <div class="col-6">Rp <?php echo number_format($appointment['price'], 0, ',', '.'); ?></div>
                                                                </div>
                                                                <?php if ($appointment['notes']): ?>
                                                                    <div class="row">
                                                                        <div class="col-12"><strong>Catatan:</strong></div>
                                                                        <div class="col-12"><?php echo htmlspecialchars($appointment['notes']); ?></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="row">
                                                                    <div class="col-6"><strong>Dibuat:</strong></div>
                                                                    <div class="col-6"><?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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