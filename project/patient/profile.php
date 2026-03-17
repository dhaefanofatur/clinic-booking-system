<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Get patient data
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch();

// Handle profile update
if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    
    // Check if email is already used by another patient
    $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['patient_id']]);
    
    if ($stmt->fetch()) {
        $error = 'Email sudah digunakan oleh pasien lain!';
    } else {
        $stmt = $pdo->prepare("UPDATE patients SET name = ?, email = ?, phone = ?, address = ?, date_of_birth = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $phone, $address, $date_of_birth, $_SESSION['patient_id']])) {
            $success = 'Profil berhasil diperbarui!';
            $_SESSION['patient_name'] = $name;
            // Refresh patient data
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$_SESSION['patient_id']]);
            $patient = $stmt->fetch();
        } else {
            $error = 'Terjadi kesalahan saat memperbarui profil!';
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!password_verify($current_password, $patient['password'])) {
        $error = 'Password lama tidak benar!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE patients SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashed_password, $_SESSION['patient_id']])) {
            $success = 'Password berhasil diubah!';
        } else {
            $error = 'Terjadi kesalahan saat mengubah password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Klinik Kebidanan Sehat</title>
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
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
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
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-calendar-check me-2"></i>Jadwal Saya
                        </a>
                        <a class="nav-link active" href="profile.php">
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
                    <h2 class="fw-bold mb-4">Profil Saya</h2>
                    
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
                    
                    <div class="row">
                        <!-- Profile Information -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Informasi Profil</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="name" name="name" 
                                                       value="<?php echo htmlspecialchars($patient['name']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($patient['email']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">No. Telepon</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" 
                                                       value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="date_of_birth" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                       value="<?php echo $patient['date_of_birth']; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($patient['address']); ?></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Informasi Akun</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">ID Pasien</label>
                                        <input type="text" class="form-control" value="<?php echo str_pad($patient['id'], 6, '0', STR_PAD_LEFT); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Terdaftar Sejak</label>
                                        <input type="text" class="form-control" value="<?php echo date('d F Y', strtotime($patient['created_at'])); ?>" readonly>
                                    </div>
                                    <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>