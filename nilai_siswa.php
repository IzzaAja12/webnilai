<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['siswa_nis'])) {
    header("Location: login_siswa.php");
    exit;
}

$nis = $_SESSION['siswa_nis'];
$stmt = $pdo->prepare("SELECT n.*, m.nama_mapel, s.nama FROM nilai n JOIN mata_pelajaran m ON n.id_mapel = m.id JOIN siswa s ON n.nis = s.nis WHERE n.nis = ?");
$stmt->execute([$nis]);
$nilai = $stmt->fetchAll();

$siswa_info = $pdo->prepare("SELECT * FROM siswa WHERE nis = ?");
$siswa_info->execute([$nis]);
$siswa = $siswa_info->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Siswa - SMK Negeri 2 Magelang</title>
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
        .grade-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .grade-A { background-color: #DCFCE7; color: #166534; }
        .grade-B { background-color: #DBEAFE; color: #1E40AF; }
        .grade-C { background-color: #FEF3C7; color: #B45309; }
        .grade-D { background-color: #FED7AA; color: #C2410C; }
        .grade-E { background-color: #FEE2E2; color: #DC2626; }
        .table-hover:hover {
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="bg-light-blue min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user-graduate text-2xl text-primary-blue"></i>
                    <div>
                        <h1 class="text-xl font-bold text-primary-blue">Portal Siswa</h1>
                        <p class="text-gray-600 text-sm">Daftar Nilai Akademik</p>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center text-red-600 hover:text-red-700 transition-all duration-300">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Student Info Card -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100 mb-8">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-primary-blue"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-primary-blue"><?php echo $siswa['nama']; ?></h2>
                        <p class="text-gray-600">NIS: <?php echo $siswa['nis']; ?></p>
                        <p class="text-gray-600">Kelas: <?php echo $siswa['kelas']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <?php 
                $total_mapel = count($nilai);
                $total_rata = $total_mapel > 0 ? array_sum(array_column($nilai, 'na')) / $total_mapel : 0;
                $grade_counts = array_count_values(array_column($nilai, 'grade'));
                $grade_A_count = $grade_counts['A'] ?? 0;
                ?>
                
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-book text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Mata Pelajaran</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo $total_mapel; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata Nilai</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo number_format($total_rata, 1); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Grade A</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo $grade_A_count; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-trophy text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="text-lg font-bold text-primary-blue">
                                <?php echo $total_rata >= 75 ? 'Lulus' : 'Belum Lulus'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades Table -->
            <div class="bg-white rounded-2xl card-shadow border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-primary-blue flex items-center">
                        <i class="fas fa-table mr-3"></i>
                        Daftar Nilai
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <?php if (count($nilai) > 0) : ?>
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-book mr-2"></i>Mata Pelajaran
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-clipboard-check mr-2"></i>UTS
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-clipboard-check mr-2"></i>UAS
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-tasks mr-2"></i>Tugas
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-calculator mr-2"></i>Nilai Akhir
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-award mr-2"></i>Grade
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($nilai as $n) : ?>
                                    <tr class="table-hover transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="bg-blue-100 w-8 h-8 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-book text-primary-blue text-sm"></i>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900"><?php echo $n['nama_mapel']; ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                                            <?php echo $n['uts']; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                                            <?php echo $n['uas']; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                                            <?php echo $n['tugas']; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-900 font-bold">
                                            <?php echo number_format($n['na'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="grade-badge grade-<?php echo $n['grade']; ?>">
                                                <?php echo $n['grade']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <div class="text-center py-16">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Nilai</h3>
                            <p class="text-gray-600">Nilai Anda belum diinput oleh guru.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Grade Info -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100 mt-8">
                <h3 class="text-xl font-bold text-primary-blue mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    Keterangan Grade
                </h3>
                <div class="grid md:grid-cols-5 gap-4">
                    <div class="text-center">
                        <span class="grade-badge grade-A block mb-2">A</span>
                        <p class="text-sm text-gray-600">90 - 100</p>
                        <p class="text-xs text-gray-500">Sangat Baik</p>
                    </div>
                    <div class="text-center">
                        <span class="grade-badge grade-B block mb-2">B</span>
                        <p class="text-sm text-gray-600">80 - 89</p>
                        <p class="text-xs text-gray-500">Baik</p>
                    </div>
                    <div class="text-center">
                        <span class="grade-badge grade-C block mb-2">C</span>
                        <p class="text-sm text-gray-600">70 - 79</p>
                        <p class="text-xs text-gray-500">Cukup</p>
                    </div>
                    <div class="text-center">
                        <span class="grade-badge grade-D block mb-2">D</span>
                        <p class="text-sm text-gray-600">50 - 69</p>
                        <p class="text-xs text-gray-500">Kurang</p>
                    </div>
                    <div class="text-center">
                        <span class="grade-badge grade-E block mb-2">E</span>
                        <p class="text-sm text-gray-600">0 - 49</p>
                        <p class="text-xs text-gray-500">Sangat Kurang</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary-blue text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">
                &copy; 2025 SMK Negeri 2 Magelang. All Rights Reserved.
            </p>
        </div>
    </footer>

</body>
</html>