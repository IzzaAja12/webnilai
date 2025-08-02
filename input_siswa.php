<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login_guru.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas = $_POST['kelas'];
    $tahun_ajaran = $_POST['tahun_ajaran'];

    try {
        // Check if NIS already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nis = ?");
        $stmt->execute([$nis]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = "NIS sudah terdaftar!";
        } else {
            // Insert new student
            $stmt = $pdo->prepare("INSERT INTO siswa (nis, nama, jenis_kelamin, kelas, tahun_ajaran) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nis, $nama, $jenis_kelamin, $kelas, $tahun_ajaran]);
            $_SESSION['success'] = "Siswa berhasil ditambahkan!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: nilai_siswaa.php");
    exit;
}
?>