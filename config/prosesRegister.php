<?php
require 'data.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nama_orang_tua = $_POST['nama_orang_tua'];
    $qurban_id = $_POST['qurban_id'];
    $nomor_hp = $_POST['nomor_hp'];

    // Cek apakah nomor HP sudah terdaftar
    $sqlCheckHP = "SELECT * FROM users WHERE nomor_hp = ?";
    $stmtCheckHP = $conn->prepare($sqlCheckHP);
    $stmtCheckHP->bind_param("s", $nomor_hp);
    $stmtCheckHP->execute();
    $resultCheckHP = $stmtCheckHP->get_result();

    if ($resultCheckHP->num_rows > 0) {
        // Jika nomor HP sudah ada, set pesan error dan hentikan proses
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Nomor HP sudah terdaftar. Gunakan nomor lain atau login.';
        header("Location: ../page/register.php");
        exit();
    }

    // Jika nomor HP belum terdaftar, lanjutkan proses pendaftaran
    $password = substr($nomor_hp, -6); // Ambil 6 digit terakhir dari nomor HP
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash password

    // Simpan ke tabel users
    $sqlUser = "INSERT INTO users (nama, nama_orang_tua, nomor_hp, password) VALUES (?, ?, ?, ?)";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("ssss", $nama, $nama_orang_tua, $nomor_hp, $hashed_password);

    if ($stmtUser->execute()) {
        $user_id = $stmtUser->insert_id; // Dapatkan ID user yang baru ditambahkan
        $stmtUser->close();

        // Ambil detail qurban berdasarkan qurban_id
        $sqlQurban = "SELECT * FROM qurban WHERE qurban_id = ?";
        $stmtQurban = $conn->prepare($sqlQurban);
        $stmtQurban->bind_param("i", $qurban_id);
        $stmtQurban->execute();
        $resultQurban = $stmtQurban->get_result();

        if ($resultQurban->num_rows > 0) {
            $row = $resultQurban->fetch_assoc();
            $biaya = $row['biaya'];
            $jumlah_terkumpul = 0;

            // Generate nomor VA
            $timestamp = substr(time(), -8);
            $random_digits = random_int(10, 99);
            $va_number = $timestamp . $random_digits;

            // Simpan ke tabel kartu_qurban
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

        // Set pesan sukses untuk user
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Berhasil mendaftar, password anda adalah 6 digit belakang nomor handphone Anda: ' . $password;
        header("Location: ../page/login.php");
        exit();
    } else {
        // Set session untuk pesan error jika pendaftaran gagal
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Gagal mendaftar. Silakan coba lagi.';
        header("Location: ../page/register.php");
        exit();
    }
}
?>
