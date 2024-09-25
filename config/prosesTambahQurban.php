<?php
session_start();
require 'data.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $qurban_id = $_POST['qurban_id'];
    $nama_pengqurban = $_POST['nama_pengqurban'];

    $sql = "SELECT * FROM qurban WHERE qurban_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $qurban_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result-> num_rows > 0){
        $row = $result->fetch_assoc();

        $biaya = $row['biaya'];
        $jumlah_terkumpul = 0;
        // Ambil timestamp saat ini dan ambil 8 digit terakhir
        $timestamp = substr(time(), -8);

        // Generate 2 digit acak menggunakan random_int()
        $random_digits = random_int(10, 99);

        // Gabungkan timestamp dan digit acak
        $va_number = $timestamp . $random_digits;

        $sql = "INSERT INTO kartu_qurban (user_id, qurban_id, nama_pengqurban, biaya, jumlah_terkumpul, va_number, status) VALUES (?, ?, ?, ?, ?, ?, 'Aktif')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $user_id, $qurban_id, $nama_pengqurban, $biaya, $jumlah_terkumpul, $va_number);

        if ($stmt->execute()) {
            // Set session flash message untuk notifikasi sukses
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Kartu Qurban berhasil ditambahkan.';
        } else {
            // Set session flash message untuk notifikasi error
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal menambahkan Kartu Qurban. Coba lagi.';
        }

        // Redirect ke usersCard.php
        header("Location: ../page/usersCard.php");
        exit();
    }
}
?>
