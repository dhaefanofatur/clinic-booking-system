<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Handle doctor addition
if (isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $schedule = $_POST['schedule'];
    
    $stmt = $pdo->prepare("INSERT INTO doctors (name, specialization, phone, email, schedule) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $specialization, $phone, $email, $schedule])) {
        $success = 'Dokter berhasil ditambahkan!';
    } else {
        $error = 'Terjadi kesalahan saat menambahkan dokter!';
    }
}

// Handle doctor update
if (isset($_POST['update_doctor'])) {
    $id = $_POST['doctor_id'];
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $schedule = $_POST['schedule'];
    
    $stmt = $pdo->prepare("UPDATE doctors SET name = ?, specialization = ?, phone = ?, email = ?, schedule = ? WHERE id = ?");
    if ($stmt->execute([$name, $specialization, $phone, $email, $schedule, $id])) {
        $success = 'Data dokter berhasil diperbarui!';
    } else {
        $error = 'Terjadi kesalahan saat memperbarui data dokter!';
    }
}

// Handle doctor deletion
if (isset($_POST['delete_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
        if ($stmt->execute([$doctor_id])) {
            $success = 'Dokter berhasil dihapus!';
        } else {
            $error = 'Terjadi kesalahan saat menghapus dokter!';
        }
    } catch (Exception $e) {
        $error = 'Tidak dapat menghapus dokter karena masih memiliki appointment aktif!';
    }
}

// Get all doctors
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dokter - Klinik Kebidanan Sehat</title>
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
                        <a class="nav-link" href="patients.php">
                            <i class="fas fa-users me-2"></i>Patients
                        </a>
                        <a class="nav-link active" href="doctors.php">
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
                        <h2 class="fw-bold">Kelola Dokter</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-plus me-2"></i>Tambah Dokter
                        </button>
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
                    
                    <!-- Doctors Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Dokter (<?php echo count($doctors); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($doctors)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada dokter terdaftar</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Spesialisasi</th>
                                                <th>Telepon</th>
                                                <th>Email</th>
                                                <th>Jadwal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <tr>
                                                    <td>#<?php echo str_pad($doctor['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                                    <td><strong><?php echo htmlspecialchars($doctor['name']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                                    <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                                                    <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($doctor['schedule']); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDoctorModal<?php echo $doctor['id']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dokter ini?')">
                                                                <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                                                <button type="submit" name="delete_doctor" class="btn btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Edit Doctor Modal -->
                                                <div class="modal fade" id="editDoctorModal<?php echo $doctor['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Dokter</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="name" class="form-label">Nama Lengkap</label>
                                                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="specialization" class="form-label">Spesialisasi</label>
                                                                        <input type="text" class="form-control" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="phone" class="form-label">Telepon</label>
                                                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="email" class="form-label">Email</label>
                                                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="schedule" class="form-label">Jadwal Praktik</label>
                                                                        <textarea class="form-control" name="schedule" rows="3"><?php echo htmlspecialchars($doctor['schedule']); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="update_doctor" class="btn btn-primary">
                                                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                                                    </button>
                                                                </div>
                                                            </form>
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
    
    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dokter Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="specialization" class="form-label">Spesialisasi</label>
                            <input type="text" class="form-control" name="specialization" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="schedule" class="form-label">Jadwal Praktik</label>
                            <textarea class="form-control" name="schedule" rows="3" placeholder="Contoh: Senin-Jumat: 08:00-16:00"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_doctor" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Dokter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>