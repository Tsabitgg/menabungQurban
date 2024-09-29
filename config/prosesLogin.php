<?php
include 'data.php';

session_start(); // Pindahkan session_start ke sini

$nomor_hp = $_POST['nomor_hp'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE nomor_hp='$nomor_hp'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user'] = [
            'user_id' => $row['user_id'],
            'nama' => $row['nama'],
            'nomor_hp' => $row['nomor_hp'],
            'alamat' => $row['alamat']
        ];
        header("Location: ../page/usersCard.php");
        exit();
    } else {
        $_SESSION['error'] = "Password salah!";
        header("Location: ../page/login.php"); // Redirect kembali ke halaman login
        exit();
    }
} else {
    $_SESSION['error'] = "Nomor HP tidak ditemukan!";
    header("Location: ../page/login.php"); // Redirect kembali ke halaman login
    exit();
}

$conn->close();
?>
