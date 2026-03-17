<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Handle patient deletion
if (isset($_POST['delete_patient'])) {
    $patient_id = $_POST['patient_id'];
    
    try {
        // Delete appointments first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        
        // Then delete patient
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        if ($stmt->execute([$patient_id])) {
            $success = 'Pasien berhasil dihapus!';
        } else {
            $error = 'Terjadi kesalahan saat menghapus pasien!';
        }
    } catch (Exception $e) {
        $error = 'Tidak dapat menghapus pasien karena masih memiliki appointment aktif!';
    }
}

// Get all patients with search
$search = $_GET['search'] ?? '';
$where_clause = "1=1";
$params = [];

if ($search) {
    $where_clause .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE $where_clause ORDER BY created_at DESC");
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pasien - Klinik Kebidanan Sehat</title>
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
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-calendar-check me-2"></i>Appointments
                        </a>
                        <a class="nav-link active" href="patients.php">
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
                        <h2 class="fw-bold">Kelola Pasien</h2>
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
                    
                    <!-- Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan nama, email, atau telepon..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Cari
                                    </button>
                                    <a href="patients.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Patients Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Pasien (<?php echo count($patients); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($patients)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada pasien ditemukan</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Telepon</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Terdaftar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($patients as $patient): ?>
                                                <tr>
                                                    <td>#<?php echo str_pad($patient['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($patient['name']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($patient['date_of_birth']) {
                                                            echo date('d/m/Y', strtotime($patient['date_of_birth']));
                                                            $age = date_diff(date_create($patient['date_of_birth']), date_create('today'))->y;
                                                            echo " ($age tahun)";
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($patient['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $patient['id']; ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pasien ini? Semua appointment terkait akan ikut terhapus!')">
                                                                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                                                                <button type="submit" name="delete_patient" class="btn btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Detail Modal -->
                                                <div class="modal fade" id="detailModal<?php echo $patient['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Pasien #<?php echo str_pad($patient['id'], 4, '0', STR_PAD_LEFT); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Nama Lengkap:</strong></div>
                                                                    <div class="col-8"><?php echo htmlspecialchars($patient['name']); ?></div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Email:</strong></div>
                                                                    <div class="col-8"><?php echo htmlspecialchars($patient['email']); ?></div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Telepon:</strong></div>
                                                                    <div class="col-8"><?php echo htmlspecialchars($patient['phone']); ?></div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Tanggal Lahir:</strong></div>
                                                                    <div class="col-8">
                                                                        <?php 
                                                                        if ($patient['date_of_birth']) {
                                                                            echo date('d F Y', strtotime($patient['date_of_birth']));
                                                                            $age = date_diff(date_create($patient['date_of_birth']), date_create('today'))->y;
                                                                            echo " ($age tahun)";
                                                                        } else {
                                                                            echo '-';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Alamat:</strong></div>
                                                                    <div class="col-8"><?php echo $patient['address'] ? htmlspecialchars($patient['address']) : '-'; ?></div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4"><strong>Terdaftar:</strong></div>
                                                                    <div class="col-8"><?php echo date('d F Y H:i', strtotime($patient['created_at'])); ?></div>
                                                                </div>
                                                                
                                                                <!-- Patient's Appointments -->
                                                                <?php
                                                                $stmt_appointments = $pdo->prepare("
                                                                    SELECT a.*, d.name as doctor_name, s.name as service_name 
                                                                    FROM appointments a 
                                                                    JOIN doctors d ON a.doctor_id = d.id 
                                                                    JOIN services s ON a.service_id = s.id 
                                                                    WHERE a.patient_id = ? 
                                                                    ORDER BY a.appointment_date DESC 
                                                                    LIMIT 5
                                                                ");
                                                                $stmt_appointments->execute([$patient['id']]);
                                                                $patient_appointments = $stmt_appointments->fetchAll();
                                                                ?>
                                                                
                                                                <h6 class="mt-4 mb-3">Riwayat Appointment (5 Terakhir)</h6>
                                                                <?php if (empty($patient_appointments)): ?>
                                                                    <p class="text-muted">Belum ada appointment</p>
                                                                <?php else: ?>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Tanggal</th>
                                                                                    <th>Dokter</th>
                                                                                    <th>Layanan</th>
                                                                                    <th>Status</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($patient_appointments as $apt): ?>
                                                                                    <tr>
                                                                                        <td><?php echo date('d/m/Y', strtotime($apt['appointment_date'])); ?></td>
                                                                                        <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                                                                        <td><?php echo htmlspecialchars($apt['service_name']); ?></td>
                                                                                        <td>
                                                                                            <?php
                                                                                            $status_class = '';
                                                                                            switch($apt['status']) {
                                                                                                case 'pending': $status_class = 'warning'; break;
                                                                                                case 'confirmed': $status_class = 'success'; break;
                                                                                                case 'completed': $status_class = 'info'; break;
                                                                                                case 'cancelled': $status_class = 'danger'; break;
                                                                                            }
                                                                                            ?>
                                                                                            <span class="badge bg-<?php echo $status_class; ?>">
                                                                                                <?php echo ucfirst($apt['status']); ?>
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