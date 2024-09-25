<?php
include 'data.php';

// Ambil data dari form
$nomor_hp = $_POST['nomor_hp'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE nomor_hp='$nomor_hp'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($password === $row['password']) {
        session_start();
        $_SESSION['user'] = [
            'user_id' => $row['user_id'],
            'nama' => $row['nama'],
            'nomor_hp' => $row['nomor_hp'],
            'alamat' => $row['alamat']
        ];
        header("Location: ../page/usersCard.php");
        exit();
    } else {
        echo "Password salah!";
    }
} else {
    echo "Nomor HP tidak ditemukan!";
}

$conn->close();
?>
