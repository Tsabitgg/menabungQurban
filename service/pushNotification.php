<?php

require 'jwt.php';

$host = 'localhost';
$db = 'menabung_qurban';
$user = 'root';
$pass = 'Smartpay1ct';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Koneksi gagal: ' . $mysqli->connect_error);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header('Content-Type: application/json');
    echo json_encode([
        'responseCode' => '01',
        'responseMessage' => 'Token tidak ditemukan',
        'responseTimestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

$secretKey = 'TokenJWT_BMI_ICT';

try {
    $decoded = JWT::decode($token, $secretKey, ['HS256']);

    $responseCode = $decoded->responseCode;
    $responseMessage = $decoded->responseMessage;
    $responseTimestamp = $decoded->responseTimestamp;
    $transactionId = $decoded->transactionId;
    $data = $decoded->data;

    if ($responseCode === '00') {
        // Proses data notifikasi
        $vano = $data->vano;
        $amount = $data->amount;
        $accountNo = $data->accountNo;
        $transactionQrId = $data->transactionQrId;
        $description = $data->description;

        // Update tabel billing
        $updateQuery = "UPDATE tagihan SET success = 1 WHERE transaksi_qr_id = '$transactionQrId'";
        $mysqli->query($updateQuery);

        // Ambil data billing dan user
        $selectQuery = "
            SELECT * FROM tagihan
            WHERE transaksi_qr_id = '$transactionQrId'
        ";

        $billingResult = $mysqli->query($selectQuery);

        if ($billingResult->num_rows > 0) {
            $transactionData = $billingResult->fetch_assoc();

            $jumlah_setoran = $transactionData['jumlah_setoran'];
            $transaction_amount = $transactionData['total_tagihan'];


            $success = 1;
            $transaction_qr_id = $transactionData['transaksi_qr_id'];
            $transaction_date = date('Y-m-d H:i:s');

            // Menangani user_id yang mungkin NULL
            $user_id = !empty($transactionData['user_id']) ? "'{$transactionData['user_id']}'" : 'NULL';

            // Query untuk memasukkan transaksi
            $insertQuery = "
            INSERT INTO transaksi
                (user_id, kartu_qurban_id, tanggal_transaksi, jumlah_setoran, biaya_admin, total_tagihan, metode_pembayaran, va_number, transaksi_qr_id, created_time, success, tagihan_id)
            VALUES 
                ($user_id, '{$transactionData['kartu_qurban_id']}', '{$transactionData['tanggal_tagihan']}', 
                '{$transactionData['jumlah_setoran']}','{$transactionData['biaya_admin']}','$transaction_amount',
                '{$transactionData['metode_pembayaran']}', '{$transactionData['va_number']}', 
                '{$transactionData['transaksi_qr_id']}', '{$transactionData['created_time']}', '$success', '{$transactionData['tagihan_id']}')";

            // Eksekusi query untuk insert ke tabel transaksi
            if ($mysqli->query($insertQuery)) {
                // Update kolom jumlah_terkumpul di tabel kartu_qurban
                $kartuQurbanId = $transactionData['kartu_qurban_id'];
                $updateKartuQuery = "
                    UPDATE kartu_qurban 
                    SET jumlah_terkumpul = jumlah_terkumpul + $jumlah_setoran 
                    WHERE kartu_qurban_id = '$kartuQurbanId'
                ";
                $mysqli->query($updateKartuQuery); // Eksekusi query untuk update

                $PAYMENT = $jumlah_setoran; // menggunakan jumlah yang sudah dikurangi

                // Keluarkan respon sukses tanpa mengirim pesan WhatsApp
                header('Content-Type: application/json');
                echo json_encode([
                    'responseCode' => '00',
                    'responseMessage' => 'TRANSACTION SUCCESS',
                    'responseTimestamp' => date('Y-m-d H:i:s'),
                    'transactionId' => $transactionId
                ]);
            } else {
                echo "Gagal memasukkan data ke tabel transaksi: " . $mysqli->error;
            }
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'responseCode' => '01',
            'responseMessage' => $responseMessage,
            'responseTimestamp' => date('Y-m-d H:i:s')
        ]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'responseCode' => '01',
        'responseMessage' => 'Invalid token or data',
        'responseTimestamp' => date('Y-m-d H:i:s')
    ]);
}

$mysqli->close();

?>
