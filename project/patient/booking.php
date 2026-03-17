<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Get doctors and services
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();

if ($_POST) {
    $doctor_id = $_POST['doctor_id'];
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $notes = $_POST['notes'];
    
    // Check if the time slot is available
    $stmt = $pdo->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
    $stmt->execute([$doctor_id, $appointment_date, $appointment_time]);
    
    if ($stmt->fetch()) {
        $error = 'Waktu tersebut sudah dibooking. Silakan pilih waktu lain.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['patient_id'], $doctor_id, $service_id, $appointment_date, $appointment_time, $notes])) {
            $success = 'Booking berhasil! Kami akan menghubungi Anda untuk konfirmasi.';
        } else {
            $error = 'Terjadi kesalahan saat membuat booking.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Baru - Klinik Kebidanan Sehat</title>
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
                        <a class="nav-link active" href="booking.php">
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
                    <h2 class="fw-bold mb-4">Booking Baru</h2>
                    
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
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Booking Appointment</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="doctor_id" class="form-label">Pilih Dokter</label>
                                        <select class="form-select" id="doctor_id" name="doctor_id" required>
                                            <option value="">-- Pilih Dokter --</option>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <option value="<?php echo $doctor['id']; ?>">
                                                    <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialization']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="service_id" class="form-label">Pilih Layanan</label>
                                        <select class="form-select" id="service_id" name="service_id" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?php echo $service['id']; ?>">
                                                    <?php echo htmlspecialchars($service['name']); ?> - Rp <?php echo number_format($service['price'], 0, ',', '.'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="appointment_date" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="appointment_time" class="form-label">Waktu</label>
                                        <select class="form-select" id="appointment_time" name="appointment_time" required>
                                            <option value="">-- Pilih Waktu --</option>
                                            <option value="08:00:00">08:00</option>
                                            <option value="08:30:00">08:30</option>
                                            <option value="09:00:00">09:00</option>
                                            <option value="09:30:00">09:30</option>
                                            <option value="10:00:00">10:00</option>
                                            <option value="10:30:00">10:30</option>
                                            <option value="11:00:00">11:00</option>
                                            <option value="11:30:00">11:30</option>
                                            <option value="13:00:00">13:00</option>
                                            <option value="13:30:00">13:30</option>
                                            <option value="14:00:00">14:00</option>
                                            <option value="14:30:00">14:30</option>
                                            <option value="15:00:00">15:00</option>
                                            <option value="15:30:00">15:30</option>
                                            <option value="16:00:00">16:00</option>
                                            <option value="16:30:00">16:30</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Keluhan atau informasi tambahan..."></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus me-2"></i>Buat Booking
                                    </button>
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Service Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Layanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Layanan</th>
                                            <th>Deskripsi</th>
                                            <th>Harga</th>
                                            <th>Durasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($service['name']); ?></td>
                                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                                <td>Rp <?php echo number_format($service['price'], 0, ',', '.'); ?></td>
                                                <td><?php echo $service['duration']; ?> menit</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>