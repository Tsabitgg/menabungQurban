<?php
require 'data.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nama_orang_tua = $_POST['nama_orang_tua'];
    $alamat = $_POST['alamat'];
    $qurban_id = $_POST['qurban_id'];
    $nomor_hp = $_POST['nomor_hp'];

    //cek no hp
    $sqlCheckHP = "SELECT * FROM users WHERE nomor_hp = ?";
    $stmtCheckHP = $conn->prepare($sqlCheckHP);
    $stmtCheckHP->bind_param("s", $nomor_hp);
    $stmtCheckHP->execute();
    $resultCheckHP = $stmtCheckHP->get_result();

    if ($resultCheckHP->num_rows > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Nomor HP sudah terdaftar. Gunakan nomor lain atau login.';
        header("Location: ../view/register.php");
        exit();
    }

    $password = substr($nomor_hp, -6);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sqlUser = "INSERT INTO users (nama, nama_orang_tua, alamat, nomor_hp, password, saldo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtUser = $conn->prepare($sqlUser);
    
    $saldo = "0.00";
    
    $stmtUser->bind_param("ssssss", $nama, $nama_orang_tua, $alamat, $nomor_hp, $hashed_password, $saldo);

    if ($stmtUser->execute()) {
        $user_id = $stmtUser->insert_id;
        $stmtUser->close();

        $sqlQurban = "SELECT * FROM qurban WHERE qurban_id = ?";
        $stmtQurban = $conn->prepare($sqlQurban);
        $stmtQurban->bind_param("i", $qurban_id);
        $stmtQurban->execute();
        $resultQurban = $stmtQurban->get_result();

        if ($resultQurban->num_rows > 0) {
            $row = $resultQurban->fetch_assoc();
            $biaya = $row['biaya'];
            $jumlah_terkumpul = 0;

            //generate va
            $timestamp = substr(time(), -8);
            $random_digits = random_int(10, 99);
            $va_number = $timestamp . $random_digits;

            $sqlKartu = "INSERT INTO kartu_qurban (user_id, qurban_id, nama_pengqurban, biaya, jumlah_terkumpul, va_number, status) 
                         VALUES (?, ?, ?, ?, ?, ?, 'Aktif')";
            $stmtKartu = $conn->prepare($sqlKartu);
            $stmtKartu->bind_param("iissds", $user_id, $qurban_id, $nama, $biaya, $jumlah_terkumpul, $va_number);

            if ($stmtKartu->execute()) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Kartu Qurban berhasil ditambahkan.';
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Gagal menambahkan Kartu Qurban.';
            }
            $stmtKartu->close();
        }

        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Berhasil mendaftar, password anda adalah 6 digit belakang nomor handphone Anda: ' . $password;
        header("Location: ../view/login.php");
        exit();
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Gagal mendaftar. Silakan coba lagi.';
        header("Location: ../view/register.php");
        exit();
    }
}
?>
