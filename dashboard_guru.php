<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login_guru.php");
    exit;
}

$id_mapel = $_SESSION['id_mapel'];
$mapel = $pdo->query("SELECT * FROM mata_pelajaran WHERE id = $id_mapel")->fetch();
$siswa = $pdo->query("SELECT * FROM siswa ORDER BY nama")->fetchAll();
$success = null;

// Check if this is an edit or input request
$selected_nis = isset($_GET['input']) ? $_GET['input'] : (isset($_GET['edit']) ? $_GET['edit'] : '');
$edit_mode = isset($_GET['edit']) && isset($_GET['nilai_id']);
$nilai_id = $edit_mode ? $_GET['nilai_id'] : null;
$existing_grades = null;

// Fetch existing grades if in edit mode
if ($edit_mode) {
    $stmt = $pdo->prepare("SELECT * FROM nilai WHERE id = ? AND id_mapel = ?");
    $stmt->execute([$nilai_id, $id_mapel]);
    $existing_grades = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];
    $uts = $_POST['uts'];
    $uas = $_POST['uas'];
    $tugas = $_POST['tugas'];
    $na = ($uts + $uas + $tugas) / 3;
    $grade = $na >= 90 ? 'A' : ($na >= 80 ? 'B' : ($na >= 70 ? 'C' : ($na >= 50 ? 'D' : 'E')));

    if (isset($_POST['nilai_id']) && !empty($_POST['nilai_id'])) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE nilai SET uts = ?, uas = ?, tugas = ?, na = ?, grade = ? WHERE id = ? AND id_mapel = ?");
        $stmt->execute([$uts, $uas, $tugas, $na, $grade, $_POST['nilai_id'], $id_mapel]);
        $success = "Nilai berhasil diperbarui!";
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO nilai (nis, id_mapel, uts, uas, tugas, na, grade) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nis, $id_mapel, $uts, $uas, $tugas, $na, $grade]);
        $success = "Nilai berhasil disimpan!";
    }
    // Redirect to nilai_siswaa.php to prevent form resubmission
    header("Location: nilai_siswaa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SMK Negeri 2 Magelang</title>
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
        .input-focus:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .grade-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
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
                        <p class="text-gray-600 text-sm">Mata Pelajaran: <?php echo $mapel['nama_mapel']; ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="nilai_siswaa.php" class="flex items-center text-primary-blue hover:text-blue-700 transition-all duration-300 bg-blue-50 px-4 py-2 rounded-lg">
                        <i class="fas fa-list mr-2"></i>
                        Daftar Nilai
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
        <div class="max-w-4xl mx-auto">
            
            <!-- Success Message -->
            <?php if (isset($success)) : ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <!-- Input Form Card -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-primary-blue mb-2 flex items-center">
                        <i class="fas fa-plus-circle mr-3"></i>
                        <?php echo $edit_mode ? 'Edit Nilai Siswa' : 'Input Nilai Siswa'; ?>
                    </h2>
                    <p class="text-gray-600">Masukkan nilai untuk mata pelajaran <?php echo $mapel['nama_mapel']; ?></p>
                </div>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="nilai_id" value="<?php echo $edit_mode ? $nilai_id : ''; ?>">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-graduate mr-2 text-primary-blue"></i>
                                Pilih Siswa
                            </label>
                            <select name="nis" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" required <?php echo $selected_nis ? 'disabled' : ''; ?>>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($siswa as $s) : ?>
                                    <option value="<?php echo $s['nis']; ?>" <?php echo $s['nis'] == $selected_nis ? 'selected' : ''; ?>>
                                        <?php echo $s['nama']; ?> (<?php echo $s['nis']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($selected_nis) : ?>
                                <input type="hidden" name="nis" value="<?php echo $selected_nis; ?>">
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-check mr-2 text-primary-blue"></i>
                                Nilai UTS
                            </label>
                            <input 
                                type="number" 
                                name="uts" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                placeholder="0 - 100"
                                value="<?php echo $edit_mode && $existing_grades ? $existing_grades['uts'] : ''; ?>"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-check mr-2 text-primary-blue"></i>
                                Nilai UAS
                            </label>
                            <input 
                                type="number" 
                                name="uas" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                placeholder="0 - 100"
                                value="<?php echo $edit_mode && $existing_grades ? $existing_grades['uas'] : ''; ?>"
                                required
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tasks mr-2 text-primary-blue"></i>
                                Nilai Tugas
                            </label>
                            <input 
                                type="number" 
                                name="tugas" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                placeholder="0 - 100"
                                value="<?php echo $edit_mode && $existing_grades ? $existing_grades['tugas'] : ''; ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-primary-blue text-white py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover font-medium flex items-center justify-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            <?php echo $edit_mode ? 'Update Nilai' : 'Simpan Nilai'; ?>
                        </button>
                        <button 
                            type="reset" 
                            class="flex-1 bg-gray-500 text-white py-3 rounded-xl hover:bg-gray-600 transition-all duration-300 btn-hover font-medium flex items-center justify-center"
                        >
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Cards -->
            <div class="grid md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-users text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Siswa</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo count($siswa); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-book text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Mata Pelajaran</p>
                            <p class="text-lg font-bold text-primary-blue"><?php echo $mapel['nama_mapel']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="text-lg font-bold text-primary-blue">Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grading System Info -->
            <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100 mt-8">
                <h3 class="text-xl font-bold text-primary-blue mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    Sistem Penilaian
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Rentang Nilai:</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">90 - 100</span>
                                <span class="grade-badge bg-green-100 text-green-800">A</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">80 - 89</span>
                                <span class="grade-badge bg-blue-100 text-blue-800">B</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">70 - 79</span>
                                <span class="grade-badge bg-yellow-100 text-yellow-800">C</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">50 - 69</span>
                                <span class="grade-badge bg-orange-100 text-orange-800">D</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">0 - 49</span>
                                <span class="grade-badge bg-red-100 text-red-800">E</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Perhitungan:</h4>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-600">
                                <strong>Nilai Akhir = (UTS + UAS + Tugas) ÷ 3</strong>
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Nilai akan otomatis dihitung dan dikategorikan sesuai grade.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>