<?php
require '../config/data.php';
$qurbanData = getAllQurban($conn);
?>

<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script>
    function toggleSubmitButton(checkbox) {
      document.getElementById("submitBtn").disabled = !checkbox.checked;
    }

    function showToast(type, message) {
      Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top", 
        position: "center", 
        backgroundColor: type === 'success' ? "#4CAF50" : "#F44336",
      }).showToast();
    }
  </script>
</head>

 <body class="bg-gray-100 p-8">

 <?php
session_start();
if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
    $status = $_SESSION['status'];
    $message = $_SESSION['message'];
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('$status', '$message');
        });
    </script>";
    unset($_SESSION['status']);
    unset($_SESSION['message']);
}
?>

  <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
   <div class="flex">
    <div class="w-1/2 pr-4">
     <img alt="Tabungan Qurban banner" class="w-full mb-4" height="200" src="../assets/img/illustrations/banner-main.jpg" width="600"/>
     <p class="text-gray-700 mb-4" style="text-align: justify;">
      DT Peduli memfasilitasi masyarakat menyalurkan hewan kurban impian dari sekarang untuk berkurban di masa mendatang. Melalui tabungan kurban, masyarakat dengan mudah mendapatkan hewan kurban yang diinginkan.
     </p>
     <h2 class="text-lg font-semibold mb-2">
      Dokumentasi Progress Hewan Qurban
     </h2>
     <div class="mb-4">
      <iframe allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" frameborder="0" height="315" src="https://www.youtube.com/embed/f72rzhJAHA0" width="100%"></iframe>
     </div>
     <h2 class="text-lg font-semibold mb-2">
      Syarat dan Ketentuan *
     </h2>
     <div class="bg-gray-200 p-6 rounded-lg shadow-md">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Komitmen Tabungan Qurban 1446 H</h2>
      <ol class="list-decimal list-inside pl-6 text-gray-700 text-sm mb-4">
        <li>Tabungan qurban menggunakan akad wadi’ah yad amanah (titipan murni), yang akan dilanjutkan menjadi akad wakalah 
        dengan mempersilahkan DT Peduli untuk menyalurkan hewan qurban kepada yang lebih berhak menerimanya dalam program 
        Unggulkan Qurban DT Peduli 1446 H.</li>
        <li>Tabungan qurban bekerjasama dengan perbankan syari’ah dalam penghimpunannya.</li>
        <li>DT Peduli tidak menggunakan dana titipan kecuali menjelang pelaksanaan qurban.</li>
        <li>Setiap Pendaftar akan mendapatkan satu rekening Virtual Account (VA) khusus untuk tabungan qurban.</li>
        <li>Pendaftar dapat melakukan pembayaran tabungan qurban sesuai dengan waktu dan frekuensi yang disepakati bersama.</li>
        <li>Pelunasan tabungan qurban maksimal H-5 hari raya Idul Adha.</li>
        <li>DT Peduli akan membantu mengingatkan waktu pembayaran serta menginformasikan progres program secara berkala.</li>
        <li>Harga qurban sudah termasuk biaya operasional dan pendistribusian.</li>
        <li>Tabungan qurban tidak dapat ditarik kembali kecuali dalam hal meninggal dunia.</li>
        <li>Tabungan yang tidak lunas akan dikonfirmasi, bisa menjadi sedekah atau dilanjutkan untuk tahun berikutnya.</li>
        <li>Penabung dihimbau untuk melakukan transaksi maksimal 2 kali dalam satu bulan.</li>
      </ol>
      <div class="bg-white p-4 rounded-lg shadow-inner">
        <h3 class="text-md font-medium text-gray-800 mb-2">Pelaksanaan Qurban 1446 H / 2025 M</h3>
        <p class="text-gray-700 text-sm">
          Iedul Qurban: 10 Dzulhijjah 1446H | 07 Juni 2025M<br>
          Hari Tasyrik: 11 - 13 Dzulhijjah 1446H | 08 Juni - 10 Juni 2025M
        </p>
      </div>
     </div>

    </div>
    <div class="w-1/2 pl-4">
     <form method="POST" action="../config/prosesRegister.php">
     <div class="mb-4">
  <label class="block text-gray-700 font-semibold mb-2">Nama Pekurban <span class="text-red-500">*</span></label>
  <div class="flex space-x-4">
    <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Cerah Rupian" type="text" name="nama" required/>
    <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Bin Andang" type="text" name="nama_orang_tua" required/>
  </div>
  <div class="flex space-x-4 mt-2">
    <span class="w-1/2 text-gray-500 text-sm">Nama Lengkap</span>
    <span class="w-1/2 text-gray-500 text-sm">Bin/Binti Nama Orang Tua</span>
  </div>
</div>

<div class="mb-4">
  <input class="w-full p-2 border border-gray-300 rounded" placeholder="Alamat" type="text" name="alamat" required/>
  <div class="mt-2">
    <span class="text-gray-500 text-sm">Alamat</span>
  </div>
</div>

      <div class="mb-4">
       <label class="block text-gray-700 font-semibold mb-2">Wilayah Tebar Indonesia</label>
       <div class="space-y-2">
       <?php
                        if ($qurbanData->num_rows > 0) {
                            while ($row = $qurbanData->fetch_assoc()) {
                                ?>
                                <div>
                                    <input class="mr-2" id="qurban-<?php echo $row['qurban_id']; ?>" type="radio" name="qurban_id" value="<?php echo $row['qurban_id']; ?>" required/>
                                    <label class="text-gray-700" for="qurban-<?php echo $row['qurban_id']; ?>">
                                        <?php echo $row['tipe_qurban']; ?> Rp. <?php echo number_format($row['biaya'], 0, ',', '.'); ?> <b>(<?php echo $row['jenis']; ?>)</b>
                                    </label>
                                </div>
                                <?php
                            }
                        } else {
                            echo "Tidak ada data qurban.";
                        }
                        ?>
       </div>
      </div>
      <div class="mb-4">
  <label class="block text-gray-700 font-semibold mb-2">
    Nomor HP (WA) <span class="text-red-500">*</span>
  </label>
  <input 
    class="w-full p-2 border border-gray-300 rounded" 
    placeholder="628xxx" 
    type="text" 
    name="nomor_hp" 
    required 
    pattern="^(\\+62|62|08)[0-9]{8,}$"
    title="Format Nomor Handphone Tidak Valid!"
  />
</div>

      <!-- Checkbox untuk syarat dan ketentuan -->
      <div class="mb-4">
        <input type="checkbox" id="termsCheckbox" onchange="toggleSubmitButton(this)"/>
        <label for="termsCheckbox" class="text-gray-700">Saya menyetujui syarat dan ketentuan yang berlaku.</label>
      </div>

      <div class="text-center">
       <button id="submitBtn" class="bg-orange-500 text-white px-6 py-2 rounded" type="submit" disabled>Mendaftar</button>
      </div>
      <div class="text-center">
  <div class="flex justify-center items-center space-x-2">
    <p class="text-gray-700 text-sm">Sudah Memiliki Akun?</p>
    <a href="login.php" class="text-blue-500 text-sm hover:underline">Masuk Sekarang</a>
  </div>
</div>

     </form>
    </div>
   </div>
  </div>
 </body>
</html>
