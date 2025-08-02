<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM guru WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $guru = $stmt->fetch();

    if ($guru) {
        $_SESSION['guru_id'] = $guru['id'];
        $_SESSION['id_mapel'] = $guru['id_mapel'];
        header("Location: home_guru.php");
        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru - SMK Negeri 2 Magelang</title>
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
        .hero-gradient {
            background: linear-gradient(135deg, #DEEBF7 0%, #BFDBFE 50%, #93C5FD 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.1);
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
        }
        .input-focus:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="hero-gradient min-h-screen flex items-center justify-center p-4">
    
    <!-- Back to Home Button -->
    <div class="absolute top-4 left-4">
        <a href="index.php" class="flex items-center text-primary-blue hover:text-blue-700 transition-all duration-300">
            <i class="fas fa-arrow-left mr-2"></i>
            <span class="hidden sm:inline">Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl card-shadow p-8 border border-gray-100">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chalkboard-teacher text-2xl text-primary-blue"></i>
                </div>
                <h2 class="text-2xl font-bold text-primary-blue mb-2">Login Guru</h2>
                <p class="text-gray-600">Masuk ke dashboard guru</p>
            </div>

            <!-- Error Message -->
            <?php if (isset($error)) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-primary-blue"></i>
                        Username
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-primary-blue"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition-all duration-300" 
                            placeholder="Masukkan password"
                            required
                        >
                        <i class="fas fa-eye-slash absolute right-4 top-4 text-gray-400 cursor-pointer" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-primary-blue text-white py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 btn-hover font-medium flex items-center justify-center"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Alternative Login -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm mb-4">atau</p>
                <a href="login_siswa.php" class="text-primary-blue hover:text-blue-700 transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Login sebagai Siswa
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-600 text-sm">
                &copy; 2025 SMK Negeri 2 Magelang
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="password"]');
            const eyeIcon = document.querySelector('.fa-eye-slash');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>