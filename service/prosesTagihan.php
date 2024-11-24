<?php
include 'data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kartu_qurban_id = $_POST['kartu_qurban_id'];

    $jumlah_setoran = str_replace('.', '', $_POST['jumlah_setoran']); // Hapus format titik
    $jumlah_setoran = intval($jumlah_setoran); // Ubah ke integer
    $metode_pembayaran = $_POST['metode_pembayaran'];

        // Hitung biaya admin berdasarkan jumlah setoran
    if ($jumlah_setoran > 100000) {
        $biaya_admin = 3000;
    } elseif ($jumlah_setoran < 100000) {
        $biaya_admin = $jumlah_setoran * 0.025; // 2,5%
    } else {
        $biaya_admin = 3000;
    }

    $total_tagihan = $jumlah_setoran + $biaya_admin;

    // Generate 8 digit unik dari milisecond datetime
    $created_time = substr(round(microtime(true) * 1000), -8);

    if ($metode_pembayaran === 'va') {
        $sql = "SELECT kartu_qurban.user_id as user_id, kartu_qurban.va_number as va_number, qurban.tipe_qurban as tipe_qurban 
                FROM kartu_qurban 
                JOIN qurban ON kartu_qurban.qurban_id = qurban.qurban_id 
                WHERE kartu_qurban_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $kartu_qurban_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $va_number = '797754' . $row['va_number'];
            $tipe_qurban = $row['tipe_qurban'];

            $sqlInsert = "INSERT INTO tagihan (kartu_qurban_id, user_id, tanggal_tagihan, jumlah_setoran, biaya_admin, total_tagihan, metode_pembayaran, va_number, created_time, success) 
                          VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, 0)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("iidddsss", $kartu_qurban_id, $user_id, $jumlah_setoran, $biaya_admin, $total_tagihan, $metode_pembayaran, $va_number, $created_time);

            if ($stmtInsert->execute()) {
                echo json_encode([
                    'success' => true,
                    'tipe_qurban' => $tipe_qurban,
                    'jumlah_setoran' => $jumlah_setoran,
                    'tanggal_tagihan' => date('Y-m-d'),
                    'va_number' => $va_number,
                    'created_time' => $created_time
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => $stmtInsert->error]);
            }
        }
    } elseif ($metode_pembayaran === 'qris') {
        $sql = "SELECT kartu_qurban.user_id as user_id, kartu_qurban.va_number as va_number, qurban.tipe_qurban as tipe_qurban 
                FROM kartu_qurban 
                JOIN qurban ON kartu_qurban.qurban_id = qurban.qurban_id 
                WHERE kartu_qurban_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $kartu_qurban_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $va_number = '797754' . $row['va_number'];
            $tipe_qurban = $row['tipe_qurban'];
        
        $sqlInsert = "INSERT INTO tagihan (kartu_qurban_id, user_id, tanggal_tagihan, jumlah_setoran, biaya_admin, total_tagihan, metode_pembayaran, va_number, created_time, success) 
                      VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, 0)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iidddsss", $kartu_qurban_id, $user_id, $jumlah_setoran,$biaya_admin, $total_tagihan, $metode_pembayaran, $va_number, $created_time);

        if ($stmtInsert->execute()) {
            echo json_encode([
                'success' => true,
                'jumlah_setoran' => $jumlah_setoran,
                'tanggal_tagihan' => date('Y-m-d'),
                'created_time' => $created_time
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmtInsert->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Metode pembayaran tidak valid']);
    }
}
}
?>
