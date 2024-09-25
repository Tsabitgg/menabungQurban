<?php

require 'jwt.php';

$host = 'localhost:3306';
$db = 'menabung_qurban';
$user = 'root';
$pass = 'Smartpay1ct';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    // Mengembalikan error dalam format JSON
    echo json_encode(['status' => false, 'message' => 'Koneksi gagal: ' . $mysqli->connect_error]);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json"); // Set header response ke JSON

// Ambil billing_id dari request parameter
$createdTime = isset($_GET['createdTime']) ? intval($_GET['createdTime']) : 0;

if ($createdTime <= 0) {
    echo json_encode(['status' => false, 'message' => 'createdTime tidak valid']);
    exit();
}

$query = "SELECT * FROM tagihan WHERE created_time = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $createdTime);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $data = [
        "accountNo" => "5010118824",
        "amount" => $row['jumlah_setoran'],
        "mitraCustomerId" => "LAZIZMU KOTA SEMARANG INFAQ511164",
        "transactionId" => $row['created_time'],
        "tipeTransaksi" => "MTR-GENERATE-QRIS-DYNAMIC",
        "vano" => $row['va_number']
    ];

    // Encode data menjadi token JWT
    $secretKey = 'TokenJWT_BMI_ICT';
    $jwtToken = JWT::encode($data, $secretKey);

    $url = 'http://10.99.23.23:8080/api/qris';

    // Inisialisasi CURL
    $ch = curl_init($url);

    // Data yang akan dikirimkan dengan request POST
    $postData = json_encode(['token' => $jwtToken]);

    // Set CURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Eksekusi CURL dan ambil response
    $response = curl_exec($ch);

    // Check for CURL errors
    if ($response === false) {
        echo json_encode(['status' => false, 'message' => 'CURL Error: ' . curl_error($ch)]);
        exit();
    }

    // Decode response untuk mengambil transactionQrId
    $responseData = json_decode($response, true);

    // Periksa apakah 'transactionDetail' dan 'transactionQrId' ada dalam response
    if (isset($responseData['transactionDetail']['transactionQrId'])) {
        $transactionQrId = $responseData['transactionDetail']['transactionQrId'];

        // Update database dengan transactionQrId
        $updateQuery = "UPDATE tagihan SET transaksi_qr_id = ? WHERE created_time = ?";
        $stmt = $mysqli->prepare($updateQuery);
        $stmt->bind_param('si', $transactionQrId, $createdTime);

        // Execute statement untuk menyimpan perubahan
        if ($stmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Transaction QR ID berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan Transaction QR ID: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => false, 'message' => 'Transaction QR ID tidak ditemukan dalam response.']);
    }

    // Tutup CURL
    curl_close($ch);

} else {
    echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
}

// Tutup koneksi
$mysqli->close();

?>
