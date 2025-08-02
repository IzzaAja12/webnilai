<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login_guru.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$id_mapel = $_SESSION['id_mapel'];

// Ambil data guru dan mata pelajaran
$guru = $pdo->query("SELECT * FROM guru WHERE id = $guru_id")->fetch();
$mapel = $pdo->query("SELECT * FROM mata_pelajaran WHERE id = $id_mapel")->fetch();

// Statistik
$total_siswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$total_nilai = $pdo->query("SELECT COUNT(*) FROM nilai WHERE id_mapel = $id_mapel")->fetchColumn();
$siswa_belum_dinilai = $total_siswa - $total_nilai;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Guru - SMK Negeri 2 Magelang</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-light-blue {
            background-color: #DEEBF7;
        }
        .text-primary-blue {
            color: #1E40AF;
        }
        .bg-primary-blue {
            background-color: #1E40AF;
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.1);
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
        }
        .welcome-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-light-blue min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chalkboard-teacher text-2xl text-primary-blue"></i>
                    <div>
                        <h1 class="text-xl font-bold text-primary-blue">Dashboard Guru</h1>
                        <p class="text-gray-600 text-sm">SMK Negeri 2 Magelang</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="nilai_siswaa.php" class="flex items-center text-primary-blue hover:text-blue-700 transition-all duration-300 bg-blue-50 px-4 py-2 rounded-lg">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Nilai
                    </a>
                    <a href="logout.php" class="flex items-center text-red-600 hover:text-red-700 transition-all duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Welcome Section -->
            <div class="welcome-gradient rounded-2xl p-8 text-white mb-8 card-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">
                            <i class="fas fa-hand-wave mr-3"></i>
                            Selamat Datang, <?php echo $guru['nama']; ?>!
                        </h2>
                        <p class="text-blue-100 text-lg">
                            Semoga hari Anda menyenangkan dan produktif
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-user-tie text-6xl opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Info Guru Card -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100 mb-8">
                <h3 class="text-2xl font-bold text-primary-blue mb-6 flex items-center">
                    <i class="fas fa-id-card mr-3"></i>
                    Informasi Guru
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-user w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800"><?php echo $guru['nama']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-id-badge w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">NIP</p>
                                <p class="font-semibold text-gray-800"><?php echo $guru['email'] ?? 'Tidak tersedia'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-semibold text-gray-800"><?php echo $guru['email'] ?? 'Tidak tersedia'; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-book w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Mata Pelajaran</p>
                                <p class="font-semibold text-gray-800"><?php echo $mapel['nama_mapel']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Login</p>
                                <p class="font-semibold text-gray-800"><?php echo date('d F Y, H:i'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-users text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Siswa</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo $total_siswa; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Sudah Dinilai</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $total_nilai; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-orange-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Belum Dinilai</p>
                            <p class="text-2xl font-bold text-orange-600"><?php echo $siswa_belum_dinilai; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-percentage text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Persentase</p>
                            <p class="text-2xl font-bold text-purple-600"><?php echo $total_siswa > 0 ? round(($total_nilai / $total_siswa) * 100) : 0; ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-primary-blue mb-6 flex items-center">
                    <i class="fas fa-bolt mr-3"></i>
                    Aksi Cepat
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <a href="nilai_siswaa.php" class="bg-primary-blue text-white p-6 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover block">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-bold text-lg">Kelola Nilai</h4>
                                <p class="text-blue-100 text-sm">Lihat, input, dan edit nilai siswa</p>
                            </div>
                        </div>
                    </a>
                    <a href="dashboard_guru.php" class="bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 transition-all duration-300 btn-hover block">
                        <div class="flex items-center">
                            <i class="fas fa-plus-circle text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-bold text-lg">Input Nilai Baru</h4>
                                <p class="text-green-100 text-sm">Tambah nilai untuk siswa</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>