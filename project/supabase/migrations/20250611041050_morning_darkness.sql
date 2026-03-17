-- Create database
CREATE DATABASE IF NOT EXISTS clinic_booking;
USE clinic_booking;

-- Table for patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for admins
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for doctors
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    schedule TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    duration INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for appointments
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Insert default admin
INSERT INTO admins (username, password, name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@clinic.com');

-- Insert sample doctors
INSERT INTO doctors (name, specialization, phone, email, schedule) VALUES 
('Dr. Sarah Wijaya, SpOG', 'Spesialis Kandungan', '081234567890', 'dr.sarah@clinic.com', 'Senin-Jumat: 08:00-16:00'),
('Dr. Maya Sari, SpOG', 'Spesialis Kandungan', '081234567891', 'dr.maya@clinic.com', 'Senin-Sabtu: 10:00-18:00'),
('Dr. Rina Putri', 'Bidan', '081234567892', 'dr.rina@clinic.com', 'Senin-Sabtu: 08:00-20:00');

-- Insert sample services
INSERT INTO services (name, description, price, duration) VALUES 
('Pemeriksaan Kehamilan', 'Pemeriksaan rutin kehamilan dengan USG', 150000, 30),
('Konsultasi Dokter', 'Konsultasi dengan dokter spesialis', 200000, 45),
('USG 4D', 'Pemeriksaan USG 4 dimensi', 300000, 60),
('Senam Hamil', 'Kelas senam untuk ibu hamil', 100000, 90),
('Konseling Laktasi', 'Konseling menyusui untuk ibu baru', 150000, 60);