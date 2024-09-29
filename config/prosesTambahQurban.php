<?php
session_start();
require 'data.php';

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

        //generate va
        $timestamp = substr(time(), -8);
        $random_digits = random_int(10, 99);

        $va_number = $timestamp . $random_digits;

        $sql = "INSERT INTO kartu_qurban (user_id, qurban_id, nama_pengqurban, biaya, jumlah_terkumpul, va_number, status) VALUES (?, ?, ?, ?, ?, ?, 'Aktif')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $user_id, $qurban_id, $nama_pengqurban, $biaya, $jumlah_terkumpul, $va_number);

        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Kartu Qurban berhasil ditambahkan.';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal menambahkan Kartu Qurban. Coba lagi.';
        }
        header("Location: ../page/usersCard.php");
        exit();
    }
}
?>
