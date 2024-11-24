<?php
session_start();
require 'data.php';

if (isset($_POST['alokasi']) && isset($_POST['nominal']) && isset($_SESSION['user'])) {
    $alokasi = $_POST['alokasi'];

    $nominal = str_replace('.', '', $_POST['nominal']); // Hapus format titik
    $nominal = intval($nominal);
    $user_id = $_SESSION['user']['user_id'];

    // Ambil saldo pengguna
    $stmt = $conn->prepare("SELECT saldo FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $nominal > 0 && $nominal <= $user['saldo']) {
        // Kurangi saldo
        $stmt = $conn->prepare("UPDATE users SET saldo = saldo - ? WHERE user_id = ?");
        $stmt->bind_param("ii", $nominal, $user_id);
        $stmt->execute();

        // Insert ke tujuan
        if (strpos($alokasi, 'qurban_') === 0) {
            $kartu_qurban_id = intval(str_replace('qurban_', '', $alokasi));
            $stmt = $conn->prepare("UPDATE kartu_qurban SET jumlah_terkumpul = jumlah_terkumpul + ? WHERE kartu_qurban_id = ?");
            $stmt->bind_param("ii", $nominal, $kartu_qurban_id);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO transaksi (kartu_qurban_id, tanggal_transaksi, jumlah_setoran, metode_pembayaran, user_id, success) VALUES (?, NOW(), ?, 'Saldo', ?, 1)");
            $stmt->bind_param("idi", $kartu_qurban_id, $nominal, $user_id);
            $stmt->execute();
        } elseif ($alokasi == 'sedekah_daging') {
            $stmt = $conn->prepare("INSERT INTO sedekah_daging (user_id, jumlah_sedekah, tanggal_sedekah) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $user_id, $nominal);
            $stmt->execute();
        }

        $_SESSION['message'] = "Setoran berhasil dialokasikan.";
    } else {
        $_SESSION['error'] = "Nominal tidak valid atau saldo tidak mencukupi.";
    }

    header("Location: ../view/usersCard.php"); // Ganti dengan halaman utama
    exit;
}
