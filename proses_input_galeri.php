<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keterangan = $_POST['keterangan'];
    $deskripsi = $_POST['deskripsi'];
    
    // Validasi file gambar
    if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file_gambar']['tmp_name'];
        $file_name = $_FILES['file_gambar']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Validasi ekstensi file
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = 'images/galeri/' . $new_file_name;
            
            // Buat folder jika belum ada
            if (!is_dir('images/galeri')) {
                mkdir('images/galeri', 0777, true);
            }
            
            // Pindahkan file ke folder
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Simpan data ke database
                $stmt = $pdo->prepare("INSERT INTO galeri (nama_file, keterangan, deskripsi) VALUES (?, ?, ?)");
                if ($stmt->execute([$new_file_name, $keterangan, $deskripsi])) {
                    // Redirect kembali ke index.php dengan pesan sukses
                    header('Location: index.php?status=success');
                    exit;
                } else {
                    echo "Gagal menyimpan data ke database.";
                }
            } else {
                echo "Gagal mengunggah file.";
            }
        } else {
            echo "Ekstensi file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    } else {
        echo "Gagal mengunggah file atau file tidak dipilih.";
    }
} else {
    echo "Metode request tidak valid.";
}
?>