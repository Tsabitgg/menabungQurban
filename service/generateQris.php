<?php

require 'jwt.php';

// Koneksi database
$host = 'localhost:3306';
$db = 'menabung_qurban';
$user = 'root';
$pass = 'Smartpay1ct';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $mysqli->connect_error]));
}

// Pengaturan header CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Ambil parameter createdTime dari request
$createdTime = isset($_GET['createdTime']) ? intval($_GET['createdTime']) : 0;

if ($createdTime <= 0) {
    die(json_encode(['success' => false, 'message' => 'createdTime tidak valid']));
}

// Menggunakan prepared statement untuk menghindari SQL Injection
$query = "SELECT * FROM tagihan WHERE created_time = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $createdTime);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Data yang akan dikodekan ke JWT
    $data = [
        "accountNo" => "1030005418",
        "amount" => $row['jumlah_setoran'],
        "mitraCustomerId" => "DT Peduli508362",
        "transactionId" => $row['created_time'],
        "tipeTransaksi" => "MTR-GENERATE-QRIS-DYNAMIC",
        "vano" => $row['va_number']
    ];

    // Encode data menjadi token JWT
    $secretKey = 'TokenJWT_BMI_ICT';
    $jwtToken = JWT::encode($data, $secretKey);

    // URL API
    $url = 'http://10.99.23.23:8080/api/qris';

    // Mengirimkan token melalui URL
    $urlWithToken = $url . '?token=' . urlencode($jwtToken);

    // Inisialisasi CURL
    $ch = curl_init($urlWithToken);

    // Set CURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Eksekusi CURL dan ambil response
    $response = curl_exec($ch);

    // Cek apakah eksekusi CURL berhasil
    if ($response === false) {
        die(json_encode(['success' => false, 'message' => 'CURL Error: ' . curl_error($ch)]));
    } else {
        // Decode response untuk mengambil transactionQrId
        $responseData = json_decode($response, true);

        // Periksa apakah 'transactionDetail' dan 'transactionQrId' ada dalam response
        if (isset($responseData['transactionDetail']['transactionQrId'])) {
            $transactionQrId = $responseData['transactionDetail']['transactionQrId'];

            // Update database dengan transactionQrId
            $updateQuery = "UPDATE tagihan SET transaksi_qr_id = ? WHERE created_time = ?";
            $stmt = $mysqli->prepare($updateQuery);
            $stmt->bind_param('ss', $transactionQrId, $createdTime);

            // Execute statement untuk menyimpan perubahan
            if ($stmt->execute()) {
                // Tambahkan transactionQrId ke dalam respons untuk ditampilkan ke klien
                $responseData['transactionQrId'] = $transactionQrId;
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan Transaction QR ID: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            error_log("Response: " . json_encode($responseData));
            echo json_encode(['success' => false, 'message' => 'Transaction QR ID tidak ditemukan dalam response']);
        }

        // Kirim respons dari server QRIS (termasuk transactionQrId yang diperoleh)
        echo json_encode($responseData);
    }

    // Tutup CURL
    curl_close($ch);

} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}

// Tutup koneksi database
$mysqli->close();

?>
