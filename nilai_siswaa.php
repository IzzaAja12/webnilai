<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login_guru.php");
    exit;
}

$id_mapel = $_SESSION['id_mapel'];
$mapel = $pdo->query("SELECT * FROM mata_pelajaran WHERE id = $id_mapel")->fetch();

// Search and pagination parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the query for counting total records (for pagination)
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

// Build the query for fetching student data
$query = "SELECT s.*, n.uts, n.uas, n.tugas, n.na, n.grade, n.id as nilai_id
          FROM siswa s 
          LEFT JOIN nilai n ON s.nis = n.nis AND n.id_mapel = :id_mapel";
if ($search) {
    $query .= " WHERE s.nama LIKE :search OR s.nis LIKE :search";
}
$query .= " ORDER BY s.nama LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':id_mapel', $id_mapel, PDO::PARAM_INT);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$siswa_nilai = $stmt->fetchAll();
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
        .grade-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .table-hover:hover {
            background-color: #f8fafc;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .modal-open {
            display: flex;
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
                        <p class="text-gray-600 text-sm">Mata Pelajaran: <?php echo $mapel['nama_mapel']; ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="home_guru.php" class="flex items-center text-primary-blue hover:text-blue-700 transition-all duration-300 bg-blue-50 px-4 py-2 rounded-lg">
                        <i class="fas fa-home mr-2"></i>
                        Home
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
        <div class="max-w-7xl mx-auto">
            
            <!-- Page Header -->
            <div class="bg-white rounded-2xl card-shadow p-6 border border-gray-100 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-primary-blue mb-2">
                            <i class="fas fa-list mr-3"></i>
                            Daftar Nilai Siswa
                        </h2>
                        <p class="text-gray-600">
                            Kelola nilai siswa untuk mata pelajaran <?php echo $mapel['nama_mapel']; ?>
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-4">
                        <!-- Search Form -->
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
                        <button id="openModalBtn" class="bg-primary-blue text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Input Siswa Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Adding New Student -->
            <div id="studentModal" class="modal">
                <div class="modal-content">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-primary-blue">
                            <i class="fas fa-user-plus mr-2"></i>
                            Tambah Siswa Baru
                        </h2>
                        <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form method="POST" action="input_siswa.php" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-id-card mr-2 text-primary-blue"></i>
                                NIS
                            </label>
                            <input 
                                type="text" 
                                name="nis" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                maxlength="10" 
                                placeholder="Masukkan NIS (maks. 10 karakter)"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-primary-blue"></i>
                                Nama Siswa
                            </label>
                            <input 
                                type="text" 
                                name="nama" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                placeholder="Masukkan nama lengkap"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-venus-mars mr-2 text-primary-blue"></i>
                                Jenis Kelamin
                            </label>
                            <select 
                                name="jenis_kelamin" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                required
                            >
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-school mr-2 text-primary-blue"></i>
                                Kelas
                            </label>
                            <input 
                                type="text" 
                                name="kelas" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                value="XII PPLG 1" 
                                readonly
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2 text-primary-blue"></i>
                                Tahun Ajaran
                            </label>
                            <input 
                                type="text" 
                                name="tahun_ajaran" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                                value="2054/2026" 
                                readonly
                            >
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button 
                                type="submit" 
                                class="flex-1 bg-primary-blue text-white py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover font-medium flex items-center justify-center"
                            >
                                <i class="fas fa-save mr-2"></i>
                                Simpan
                            </button>
                            <button 
                                type="button" 
                                id="cancelModalBtn" 
                                class="flex-1 bg-gray-500 text-white py-3 rounded-xl hover:bg-gray-600 transition-all duration-300 btn-hover font-medium flex items-center justify-center"
                            >
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl card-shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UTS</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UAS</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo $siswa['nama']; ?></div>
                                                <div class="text-sm text-gray-500"><?php echo $siswa['jenis_kelamin']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $siswa['kelas']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['uts'] ? number_format($siswa['uts'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['uas'] ? number_format($siswa['uas'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php echo $siswa['tugas'] ? number_format($siswa['tugas'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                        <?php echo $siswa['na'] ? number_format($siswa['na'], 1) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($siswa['grade']) : ?>
                                            <span class="grade-badge 
                                                <?php 
                                                    echo $siswa['grade'] == 'A' ? 'bg-green-100 text-green-800' :
                                                        ($siswa['grade'] == 'B' ? 'bg-blue-100 text-blue-800' :
                                                        ($siswa['grade'] == 'C' ? 'bg-yellow-100 text-yellow-800' :
                                                        ($siswa['grade'] == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')));
                                                ?>">
                                                <?php echo $siswa['grade']; ?>
                                            </span>
                                        <?php else : ?>
                                            <span class="grade-badge bg-gray-100 text-gray-800">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <?php if ($siswa['nilai_id']) : ?>
                                                <!-- Button Edit -->
                                                <a href="dashboard_guru.php?edit=<?php echo $siswa['nis']; ?>&nilai_id=<?php echo $siswa['nilai_id']; ?>" 
                                                   class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition-all duration-200 text-xs">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>
                                            <?php else : ?>
                                                <!-- Button Input -->
                                                <a href="dashboard_guru.php?input=<?php echo $siswa['nis']; ?>" 
                                                   class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition-all duration-200 text-xs">
                                                    <i class="fas fa-plus mr-1"></i>
                                                    Input
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-4 flex justify-center items-center space-x-2">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link">Previous</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">Previous</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages) : ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="pagination-link">Next</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">Next</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics Footer -->
            <div class="grid md:grid-cols-4 gap-6 mt-8">
                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-users text-primary-blue"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Siswa</p>
                            <p class="text-2xl font-bold text-primary-blue"><?php echo $total_records; ?></p>
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
                            <p class="text-2xl font-bold text-green-600">
                                <?php 
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa s JOIN nilai n ON s.nis = n.nis WHERE n.id_mapel = ?");
                                    $stmt->execute([$id_mapel]);
                                    echo $stmt->fetchColumn();
                                ?>
                            </p>
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
                            <p class="text-2xl font-bold text-orange-600">
                                <?php 
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa s LEFT JOIN nilai n ON s.nis = n.nis AND n.id_mapel = ? WHERE n.id IS NULL");
                                    $stmt->execute([$id_mapel]);
                                    echo $stmt->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl card-shadow p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rata-rata</p>
                            <p class="text-2xl font-bold text-purple-600">
                                <?php 
                                    $stmt = $pdo->prepare("SELECT AVG(na) FROM nilai WHERE id_mapel = ?");
                                    $stmt->execute([$id_mapel]);
                                    $rata_rata = $stmt->fetchColumn();
                                    echo $rata_rata ? number_format($rata_rata, 1) : '0.0';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const modal = document.getElementById('studentModal');
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementByid('cancelModalBtn');

        openModalBtn.addEventListener('click', () => {
            modal.classList.add('modal-open');
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.remove('modal-open');
        });

        cancelModalBtn.addEventListener('click', () => {
            modal.classList.remove('modal-open');
        });

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('modal-open');
            }
        });
    </script>

</body>
</html>