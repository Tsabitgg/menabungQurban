<?php

require 'jwt.php';

$host = 'localhost:3306';
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

// Ambil billing_id dari request parameter
$createdTime = isset($_GET['createdTime']) ? intval($_GET['createdTime']) : 0;

if ($createdTime <= 0) {
    die('createdTime tidak valid');
}

$query = "SELECT * FROM tagihan WHERE created_time = $createdTime";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Pastikan data yang seharusnya berupa bigint dikonversi menjadi integer
    $accountNo = intval("5010118824");  // Pastikan ini angka
    $vano = intval($row['va_number']);  // Konversi ke integer (bigint)
    $transactionId = intval($row['created_time']); // Konversi created_time ke integer

    $data = [
        "accountNo" => $accountNo,
        "amount" => $row['jumlah_setoran'], // Ini kemungkinan tipe float atau double
        "mitraCustomerId" => "LAZIZMU KOTA SEMARANG INFAQ511164", // Tetap string
        "transactionId" => $transactionId,
        "tipeTransaksi" => "MTR-GENERATE-QRIS-DYNAMIC",
        "vano" => $vano // Pastikan va_number dikonversi ke bigint
    ];

    // Encode data menjadi token JWT
    $secretKey = 'TokenJWT_BMI_ICT';
    $jwtToken = JWT::encode($data, $secretKey);

    $url = 'http://10.99.23.23:8080/api/qris';

    // Inisialisasi CURL
    $ch = curl_init($url);

    // Data yang akan dikirimkan dengan request POST
    $postData = json_encode([
        'token' => $jwtToken
    ]);

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

    // Tampilkan response mentah untuk debugging
    echo "Response raw: " . $response;

    // Check for CURL errors
    if ($response === false) {
        echo 'CURL Error: ' . curl_error($ch);
    } else {
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
                echo '';
            } else {
                echo 'Gagal menyimpan Transaction QR ID: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            echo 'Transaction QR ID tidak ditemukan dalam response.';
        }
    }

    // Tutup CURL
    curl_close($ch);

} else {
    echo "Data tidak ditemukan";
}

// Tutup koneksi
$mysqli->close();

?>
