<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['walikelas_id'])) {
    header("Location: login_walikelas.php");
    exit;
}

$walikelas_id = $_SESSION['walikelas_id'];

// Ambil data wali kelas
$walikelas = $pdo->query("SELECT * FROM guru WHERE id = $walikelas_id AND role = 'walikelas'")->fetch();

// Statistik untuk kelas XII PPLG 1
$total_siswa = $pdo->query("SELECT COUNT(*) FROM siswa WHERE kelas = 'XII PPLG 1'")->fetchColumn();
$total_nilai = $pdo->query("SELECT COUNT(DISTINCT nis) FROM nilai WHERE id_mapel IN (1, 2, 3) AND nis IN (SELECT nis FROM siswa WHERE kelas = 'XII PPLG 1')")->fetchColumn();
$siswa_belum_dinilai = $total_siswa - $total_nilai;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Wali Kelas - SMK Negeri 2 Magelang</title>
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
                    <i class="fas fa-user-tie text-2xl text-primary-blue"></i>
                    <div>
                        <h1 class="text-xl font-bold text-primary-blue">Dashboard Wali Kelas</h1>
                        <p class="text-gray-600 text-sm">SMK Negeri 2 Magelang</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="nilai_walikelas.php" class="flex items-center text-primary-blue hover:text-blue-700 transition-all duration-300 bg-blue-50 px-4 py-2 rounded-lg">
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

    <!-- Konten Utama -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Bagian Selamat Datang -->
            <div class="welcome-gradient rounded-2xl p-8 text-white mb-8 card-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">
                            <i class="fas fa-hand-wave mr-3"></i>
                            Selamat Datang, <?php echo $walikelas['nama']; ?>!
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

            <!-- Kartu Informasi Wali Kelas -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100 mb-8">
                <h3 class="text-2xl font-bold text-primary-blue mb-6 flex items-center">
                    <i class="fas fa-id-card mr-3"></i>
                    Informasi Wali Kelas
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-user w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800"><?php echo $walikelas['nama']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-id-badge w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">NIP</p>
                                <p class="font-semibold text-gray-800"><?php echo $walikelas['email'] ?? 'Tidak tersedia'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-semibold text-gray-800"><?php echo $walikelas['email'] ?? 'Tidak tersedia'; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-school w-6 text-primary-blue mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Kelas</p>
                                <p class="font-semibold text-gray-800">XII PPLG 1</p>
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

            <!-- Kartu Statistik -->
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

            <!-- Aksi Cepat -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-primary-blue mb-6 flex items-center">
                    <i class="fas fa-bolt mr-3"></i>
                    Aksi Cepat
                </h3>
                <div class="grid md:grid-cols-1 gap-6">
                    <a href="nilai_walikelas.php" class="bg-primary-blue text-white p-6 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover block">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-bold text-lg">Lihat Nilai Siswa</h4>
                                <p class="text-blue-100 text-sm">Tinjau nilai akhir siswa di semua mata pelajaran</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>