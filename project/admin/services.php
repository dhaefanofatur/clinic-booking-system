<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Handle service addition
if (isset($_POST['add_service'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    
    $stmt = $pdo->prepare("INSERT INTO services (name, description, price, duration) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $description, $price, $duration])) {
        $success = 'Layanan berhasil ditambahkan!';
    } else {
        $error = 'Terjadi kesalahan saat menambahkan layanan!';
    }
}

// Handle service update
if (isset($_POST['update_service'])) {
    $id = $_POST['service_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    
    $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration = ? WHERE id = ?");
    if ($stmt->execute([$name, $description, $price, $duration, $id])) {
        $success = 'Data layanan berhasil diperbarui!';
    } else {
        $error = 'Terjadi kesalahan saat memperbarui data layanan!';
    }
}

// Handle service deletion
if (isset($_POST['delete_service'])) {
    $service_id = $_POST['service_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt->execute([$service_id])) {
            $success = 'Layanan berhasil dihapus!';
        } else {
            $error = 'Terjadi kesalahan saat menghapus layanan!';
        }
    } catch (Exception $e) {
        $error = 'Tidak dapat menghapus layanan karena masih digunakan dalam appointment!';
    }
}

// Get all services
$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Layanan - Klinik Kebidanan Sehat</title>
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
                        <a class="nav-link" href="doctors.php">
                            <i class="fas fa-user-md me-2"></i>Doctors
                        </a>
                        <a class="nav-link active" href="services.php">
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
                        <h2 class="fw-bold">Kelola Layanan</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                            <i class="fas fa-plus me-2"></i>Tambah Layanan
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
                    
                    <!-- Services Cards -->
                    <div class="row">
                        <?php foreach ($services as $service): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editServiceModal<?php echo $service['id']; ?>">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                                                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                            <button type="submit" name="delete_service" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-primary fw-bold fs-5">
                                                Rp <?php echo number_format($service['price'], 0, ',', '.'); ?>
                                            </span>
                                            <span class="text-muted">
                                                <i class="fas fa-clock me-1"></i><?php echo $service['duration']; ?> menit
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Service Modal -->
                            <div class="modal fade" id="editServiceModal<?php echo $service['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Layanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Layanan</label>
                                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" name="description" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="price" class="form-label">Harga (Rp)</label>
                                                        <input type="number" class="form-control" name="price" value="<?php echo $service['price']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="duration" class="form-label">Durasi (menit)</label>
                                                        <input type="number" class="form-control" name="duration" value="<?php echo $service['duration']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="update_service" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($services)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-stethoscope fa-4x text-muted mb-4"></i>
                                    <h4 class="text-muted">Belum Ada Layanan</h4>
                                    <p class="text-muted mb-4">Tambahkan layanan pertama untuk memulai</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                        <i class="fas fa-plus me-2"></i>Tambah Layanan
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Layanan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Layanan</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Durasi (menit)</label>
                                <input type="number" class="form-control" name="duration" value="30" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_service" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Layanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>