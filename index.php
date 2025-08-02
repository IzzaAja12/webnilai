<?php
session_start();
require 'koneksi.php'; // Koneksi ke database

// Ambil data galeri dari database
$stmt = $pdo->query("SELECT nama_file, keterangan, deskripsi FROM galeri ORDER BY tanggal_upload DESC LIMIT 6");
$galeri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Negeri 2 Magelang - Sistem Nilai</title>
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
        .bg-secondary-blue {
            background-color: #3B82F6;
        }
        .school-logo {
            width: 140px;
            height: 80px;
            object-fit: contain;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #DEEBF7 0%, #BFDBFE 50%, #93C5FD 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.1);
        }
        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        input:checked ~ .dropdown-menu {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.15);
        }
        .gallery-img {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        }
        .modal {
            display: none;
        }
        .modal-open {
            display: flex;
        }
    </style>
</head>
<body class="bg-light-blue font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-graduation-cap text-2xl text-primary-blue"></i>
                    <div class="text-xl font-bold text-primary-blue">SMK Negeri 2 Magelang</div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="bg-gray-200 text-gray-500 px-6 py-2 rounded-full cursor-not-allowed transition-all duration-300" disabled>
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </button>
                    <button id="openModal" class="bg-secondary-blue text-white px-6 py-2 rounded-full hover:bg-blue-500 transition-all duration-300 btn-hover inline-flex items-center">
                        <i class="fas fa-image mr-2"></i>Input Galeri
                    </button>
                    <!-- Dropdown Login -->
                    <div class="relative">
                        <input type="checkbox" id="toggleLogin" class="hidden peer">
                        <label for="toggleLogin" class="bg-primary-blue text-white px-6 py-2 rounded-full cursor-pointer hover:bg-blue-700 transition-all duration-300 btn-hover inline-flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </label>
                        <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl z-20 peer-checked:block border border-gray-100">
                            <div class="py-2">
                                <a href="login_guru.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-primary-blue transition-all duration-200">
                                    <i class="fas fa-chalkboard-teacher mr-3 text-blue-500"></i>Login Guru
                                </a>
                                <a href="login_walikelas.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-primary-blue transition-all duration-200">
                                    <i class="fas fa-user-tie mr-3 text-blue-500"></i>Login Walikelas
                                </a>
                                <a href="login_siswa.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-primary-blue transition-all duration-200">
                                    <i class="fas fa-user-graduate mr-3 text-blue-500"></i>Login Siswa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal for Input Galeri -->
    <div id="modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-primary-blue">Input Galeri</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="proses_input_galeri.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="file_gambar" class="block text-gray-700 mb-2">Pilih Gambar</label>
                    <input type="file" id="file_gambar" name="file_gambar" accept="image/*" class="w-full p-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="block text-gray-700 mb-2">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Masukkan keterangan" required>
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="block text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="w-full p-2 border border-gray-300 rounded-lg" rows="4" placeholder="Masukkan deskripsi"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2 hover:bg-gray-400">Batal</button>
                    <button type="submit" class="bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-gradient py-20 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-white opacity-10"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-full p-6 w-32 h-32 mx-auto mb-8 shadow-lg">
                    <img src="logo.png" alt="Logo SMK Negeri 2 Magelang" class="school-logo mx-auto">
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-primary-blue mb-4">
                    SMK Negeri 2 Magelang
                </h1>
                <p class="text-xl md:text-2xl text-gray-700 mb-8">
                    Sistem Informasi Nilai Siswa
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="login_guru.php" class="bg-primary-blue text-white px-8 py-4 rounded-full hover:bg-blue-700 transition-all duration-300 btn-hover inline-flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        Portal Guru
                    </a>
                    <a href="login_walikelas.php" class="bg-white text-primary-blue px-8 py-4 rounded-full hover:bg-gray-50 transition-all duration-300 btn-hover inline-flex items-center justify-center border-2 border-primary-blue">
                        <i class="fas fa-user-tie mr-2"></i>
                        Portal Walikelas
                    </a>
                    <a href="login_siswa.php" class="bg-white text-primary-blue px-8 py-4 rounded-full hover:bg-gray-50 transition-all duration-300 btn-hover inline-flex items-center justify-center border-2 border-primary-blue">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Portal Siswa
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary-blue mb-4">Fitur Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Sistem informasi nilai yang mudah digunakan dan terpercaya untuk mendukung proses pembelajaran
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-white p-8 rounded-2xl card-shadow border border-gray-100 transition-all duration-300 text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-primary-blue"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-blue mb-3">Kelola Nilai</h3>
                    <p class="text-gray-600">Guru dapat mengelola dan memasukkan nilai siswa dengan mudah dan cepat</p>
                </div>
                <div class="feature-card bg-white p-8 rounded-2xl card-shadow border border-gray-100 transition-all duration-300 text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-eye text-2xl text-primary-blue"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-blue mb-3">Lihat Nilai</h3>
                    <p class="text-gray-600">Siswa dapat melihat nilai mereka secara real-time dan transparan</p>
                </div>
                <div class="feature-card bg-white p-8 rounded-2xl card-shadow border border-gray-100 transition-all duration-300 text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-primary-blue"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-blue mb-3">Responsif</h3>
                    <p class="text-gray-600">Dapat diakses dari berbagai perangkat, kapan saja dan dimana saja</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16 bg-light-blue">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary-blue mb-4">Galeri Kegiatan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Lihat momen-momen berharga dari kegiatan belajar dan acara di SMK Negeri 2 Magelang
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($galeri as $foto): ?>
                    <div class="relative overflow-hidden rounded-xl card-shadow">
                        <img src="images/galeri/<?php echo htmlspecialchars($foto['nama_file']); ?>" alt="<?php echo htmlspecialchars($foto['keterangan']); ?>" class="w-full h-64 object-cover gallery-img">
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-4">
                            <p class="font-semibold"><?php echo htmlspecialchars($foto['keterangan']); ?></p>
                            <p class="text-sm"><?php echo htmlspecialchars($foto['deskripsi']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-6">
                <p class="text-gray-600 text-sm">
                    Sumber foto: <a href="https://www.instagram.com/p/DJ1a-umvkiF/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==" target="_blank" class="text-primary-blue hover:underline">SMK Negeri 2 Magelang (Instagram)</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary-blue text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <div class="flex justify-center items-center space-x-3 mb-4">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                    <span class="text-xl font-semibold">SMK Negeri 2 Magelang</span>
                </div>
                <p class="text-blue-200 mb-4">Sistem Informasi Nilai Siswa</p>
                <div class="border-t border-blue-700 pt-4">
                    <p class="text-sm text-blue-200">
                        &copy; 2025 SMK Negeri 2 Magelang. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Modal JavaScript
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelModalBtn = document.getElementById('cancelModal');
        const modal = document.getElementById('modal');

        openModalBtn.addEventListener('click', () => {
            modal.classList.add('modal-open');
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.remove('modal-open');
        });

        cancelModalBtn.addEventListener('click', () => {
            modal.classList.remove('modal-open');
        });
    </script>
</body>
</html>