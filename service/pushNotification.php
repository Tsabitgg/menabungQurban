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

            // Kurangi billing_amount sebesar 3000
            $transaction_amount = $transactionData['jumlah_setoran'];

            $success = 1;
            $transaction_qr_id = $transactionData['transaksi_qr_id'];
            $transaction_date = date('Y-m-d H:i:s');

            // Query untuk memasukkan transaksi
            $insertQuery = "
            INSERT INTO transaksi
                (kartu_qurban_id, tanggal_transaksi, jumlah_setoran, metode_pembayaran, va_number, transaksi_qr_id, created_time, success, tagihan_id)
            VALUES 
                ('{$transactionData['kartu_qurban_id']}', '{$transactionData['tanggal_tagihan']}', 
                '$transaction_amount', '{$transactionData['metode_pembayaran']}', '{$transactionData['va_number']}', 
                '{$transactionData['transaksi_qr_id']}', '{$transactionData['created_time']}', '$success', '{$transactionData['tagihan_id']}')";

            // Eksekusi query untuk insert ke tabel transaction
            if ($mysqli->query($insertQuery)) {
                $PAYMENT = $transaction_amount; // menggunakan jumlah yang sudah dikurangi

                // Keluarkan respon sukses tanpa mengirim pesan WhatsApp
                header('Content-Type: application/json');
                echo json_encode([
                    'responseCode' => '00',
                    'responseMessage' => 'TRANSACTION SUCCESS',
                    'responseTimestamp' => date('Y-m-d H:i:s'),
                    'transactionId' => $transactionId
                ]);
            } else {
                echo "Gagal memasukkan data ke tabel transaction: " . $mysqli->error;
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
