<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'data.php';

    $kartu_qurban_id = $_POST['kartu_qurban_id'];
    $jumlah_setoran = $_POST['jumlah_setoran'];

    $conn->begin_transaction();

    try {
        $sql = "UPDATE kartu_qurban SET jumlah_terkumpul = jumlah_terkumpul + ? WHERE kartu_qurban_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $jumlah_setoran, $kartu_qurban_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menyimpan setoran.");
        }

        $sql = "SELECT user_id, biaya, jumlah_terkumpul FROM kartu_qurban WHERE kartu_qurban_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $kartu_qurban_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $kartu_qurban = $result->fetch_assoc();

        $user_id = $kartu_qurban['user_id'];
        $biaya = $kartu_qurban['biaya'];
        $jumlah_terkumpul = $kartu_qurban['jumlah_terkumpul'];

        $sql = "INSERT INTO transaksi (kartu_qurban_id, tanggal_transaksi, jumlah, metode_pembayaran, user_id, tagihan_id) 
                VALUES (?, NOW(), 'Sapi Jumbo', ?, 'VA', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idi", $kartu_qurban_id, $jumlah_setoran, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menyimpan transaksi.");
        }

        if ($jumlah_terkumpul > $biaya) {
            $sisa = $jumlah_terkumpul - $biaya;
            $sql = "INSERT INTO sedekah_daging (user_id, jumlah_sedekah, tanggal_sedekah) 
                    VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("id", $user_id, $sisa);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("Gagal menyimpan sedekah.");
            }
        }

        $conn->commit();
        header("Location: ../view/usersCard.php");
    } catch (Exception $e) {
        $conn->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>
