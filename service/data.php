<?php
$host = "localhost";
$user = "root";
$pass = "Smartpay1ct";
$database = "menabung_qurban";

$conn = new mysqli($host, $user, $pass, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function getLoggedInUser($conn) {
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['user_id'];

        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function updateUserProfile($conn, $user_id, $data) {
    $sql = "UPDATE users SET nama = ?, nama_orang_tua = ?, email = ?, alamat = ?, kota = ?, nomor_hp = ?, jenis_kelamin = ?, tanggal_lahir = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssssssi", 
        $data['nama'], 
        $data['nama_orang_tua'], 
        $data['email'], 
        $data['alamat'],
        $data['kota'], 
        $data['nomor_hp'], 
        $data['jenis_kelamin'], 
        $data['tanggal_lahir'], 
        $user_id
    );

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}


function getAllQurban($conn) {
    $sql = "SELECT * FROM qurban";
    $result = $conn->query($sql);

    return $result;
}

function getTabunganAndTarget($conn) {
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['user_id'];

        $sql = "SELECT 
                    SUM(jumlah_terkumpul) AS total_tabungan, 
                    SUM(biaya) AS target_tabungan 
                FROM kartu_qurban 
                WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return [
                'total_tabungan' => 0, 
                'target_tabungan' => 0
            ];
        }
    } else {
        return null;
    }
}


function getKartuQurban($conn) {
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['user_id'];

        $sql = "SELECT * FROM kartu_qurban JOIN qurban on kartu_qurban.qurban_id = qurban.qurban_id WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $kartuQurban = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kartuQurban[] = $row;
            }
        }
        return $kartuQurban;
    } else {
        return null;
    }
}

function getQurbanProgress($conn) {
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['user_id'];

        $sql = "SELECT kartu_qurban.*, (kartu_qurban.jumlah_terkumpul / kartu_qurban.biaya) * 100 AS progress, 
        qurban.* FROM kartu_qurban JOIN qurban ON kartu_qurban.qurban_id = qurban.qurban_id
        WHERE kartu_qurban.user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $kartuQurban = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kartuQurban[] = $row;
            }
        }
        return $kartuQurban;
    } else {
        return null;
    }
}

// function getTotalSaldo($conn) {
//     if (isset($_SESSION['user'])) {
//         // Ambil user_id dari session
//         $user_id = $_SESSION['user']['user_id'];

//         // Query untuk menghitung total jumlah terkumpul dan total biaya
//         $sql = "SELECT SUM(jumlah_terkumpul) AS total_terkumpul, SUM(biaya) AS total_biaya 
//                 FROM kartu_qurban 
//                 WHERE user_id = ?";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("i", $user_id);
//         $stmt->execute();
//         $result = $stmt->get_result();
        
//         if ($row = $result->fetch_assoc()) {
//             $total_terkumpul = $row['total_terkumpul'] ? $row['total_terkumpul'] : 0;
//             $total_biaya = $row['total_biaya'] ? $row['total_biaya'] : 0;
//             $persentase = $total_biaya > 0 ? ($total_terkumpul / $total_biaya) * 100 : 0;
//             return [
//                 'total_terkumpul' => $total_terkumpul,
//                 'persentase' => $persentase
//             ];
//         }
//     }
//     return null;
// }


function getTransaksi($conn) {
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['user_id'];
        $sql = "SELECT * FROM transaksi WHERE user_id = ? ORDER BY tanggal_transaksi, transaksi_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $transaksi = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transaksi[] = $row;
            }
        }
        return $transaksi;
    } else {
        return null;
    }
}

function getLastTransaksiDaging($conn) {
    $user_id = $_SESSION['user']['user_id'];
    $sql = "SELECT jumlah_sedekah, tanggal_sedekah FROM sedekah_daging WHERE user_id = ? 
            ORDER BY tanggal_sedekah DESC, sedekah_daging_id DESC 
    LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function getSedekahDaging($conn) {
    $user_id = $_SESSION['user']['user_id'];
    $sql = "SELECT SUM(jumlah_sedekah) AS total_sedekah FROM sedekah_daging WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_sedekah'] ? $row['total_sedekah'] : 0;
    }
    
    return 0;
}


?>
