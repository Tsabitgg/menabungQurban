<?php
session_start();
require 'data.php'; // Pastikan file koneksi ada

if (isset($_GET['kartu_qurban_id'])) {
    $kartu_qurban_id = intval($_GET['kartu_qurban_id']);

    // Hapus kartu qurban berdasarkan ID
    $sql = "DELETE FROM kartu_qurban WHERE kartu_qurban_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $kartu_qurban_id, $_SESSION['user']['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Kartu Qurban berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus Kartu Qurban.";
    }

    $stmt->close();
    header("Location: ../view/usersCard.php"); // Ganti dengan halaman utama qurban Anda
    exit;
}
