<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['walikelas_id'])) {
    header("Location: login_walikelas.php");
    exit;
}

// Parameter pencarian dan paginasi
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10; // Jumlah rekord per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk menghitung total rekord (untuk paginasi)
$count_query = "SELECT COUNT(*) FROM siswa s";
if ($search) {
    $count_query .= " WHERE s.nama LIKE :search OR s.nis LIKE :search";
}
$stmt = $pdo->prepare($count_query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->execute();
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Query untuk mengambil data siswa dengan nilai pivot
$query = "SELECT s.nis, s.nama, s.kelas,
                 MAX(CASE WHEN n.id_mapel = 2 THEN n.na END) as indo_na,
                 MAX(CASE WHEN n.id_mapel = 3 THEN n.na END) as math_na,
                 MAX(CASE WHEN n.id_mapel = 1 THEN n.na END) as inggris_na
          FROM siswa s 
          LEFT JOIN nilai n ON s.nis = n.nis
          WHERE s.kelas = 'XII PPLG 1'";
if ($search) {
    $query .= " AND (s.nama LIKE :search OR s.nis LIKE :search)";
}
$query .= " GROUP BY s.nis, s.nama, s.kelas ORDER BY s.nama LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$siswa_nilai = $stmt->fetchAll();

// Mengambil rata-rata nilai per mata pelajaran
$avg_query = "SELECT m.nama_mapel, AVG(n.na) as rata_rata
              FROM mata_pelajaran m
              LEFT JOIN nilai n ON m.id = n.id_mapel
              WHERE m.id IN (1, 2, 3)
              GROUP BY m.id";
$stmt = $pdo->prepare($avg_query);
$stmt->execute();
$averages = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nilai Siswa - SMK Negeri 2 Magelang</title>
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
        .table-hover:hover {
            background-color: #f8fafc;
        }
        .input-focus:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .pagination-link {
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 0.375rem;
            background-color: #f3f4f6;
            color: #1E40AF;
            transition: all 0.3s;
        }
        .pagination-link:hover {
            background-color: #1E40AF;
            color: white;
        }
        .pagination-link.active {
            background-color: #1E40AF;
            color: white;
        }
        .pagination-link.disabled {
            color: #6b7280;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light-blue min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clipboard-list text-2xl text-primary-blue"></i>
                    <div>
                        <h1 class="text-xl font-bold text-primary-blue">Daftar Nilai Siswa</h1>
                        <p class="text-gray-600 text-sm">Wali Kelas: Kelas XII PPLG 1</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
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
        <div class="max-w-7xl mx-auto">
            
            <!-- Header Halaman -->
            <div class="bg-white rounded-2xl card-shadow p-6 border border-gray-100 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-primary-blue mb-2">
                            <i class="fas fa-list mr-3"></i>
                            Daftar Nilai Siswa
                        </h2>
                        <p class="text-gray-600">
                            Lihat nilai akhir siswa untuk semua mata pelajaran
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <!-- Formulir Pencarian -->
                        <form method="GET" class="flex items-center">
                            <input 
                                type="text" 
                                name="search" 
                                value="<?php echo htmlspecialchars($search); ?>" 
                                placeholder="Cari nama atau NIS..." 
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300"
                            >
                            <button 
                                type="submit" 
                                class="ml-2 bg-primary-blue text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover"
                            >
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kartu Tabel -->
            <div class="bg-white rounded-2xl card-shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B. Indonesia</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Matematika</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B. Inggris</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($siswa_nilai as $index => $siswa) : ?>
                                <tr class="table-hover transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo $offset + $index + 1; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $siswa['nis']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-primary-blue rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo $siswa['nama']; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $siswa['kelas']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['indo_na'] ? number_format($siswa['indo_na'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['math_na'] ? number_format($siswa['math_na'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['inggris_na'] ? number_format($siswa['inggris_na'], 1) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Paginasi -->
                <div class="p-4 flex justify-center items-center space-x-2">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link">Sebelumnya</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">Sebelumnya</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages) : ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link">Selanjutnya</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">Selanjutnya</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistik Footer -->
            <div class="grid md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-book text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata B. Indonesia</p>
                            <p class="text-2xl font-bold text-primary-blue">
                                <?php echo isset($averages['Bahasa Indonesia']) ? number_format($averages['Bahasa Indonesia'], 1) : '0.0'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-calculator text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata Matematika</p>
                            <p class="text-2xl font-bold text-primary-blue">
                                <?php echo isset($averages['Matematika']) ? number_format($averages['Matematika'], 1) : '0.0'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-language text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata B. Inggris</p>
                            <p class="text-2xl font-bold text-primary-blue">
                                <?php echo isset($averages['Bahasa Inggris']) ? number_format($averages['Bahasa Inggris'], 1) : '0.0'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>