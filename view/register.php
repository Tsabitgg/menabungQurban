<?php
require '../service/data.php';
$qurbanData = getAllQurban($conn);
$qurbanDataMob = getAllQurban($conn);
?>

<html>

<head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
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

<body class="md:bg-gray-100 md:p-8">

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

  <div class="hidden md:block max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <div class="flex">
      <div class="w-1/2 pr-4">
        <img alt="Tabungan Qurban banner" class="w-full mb-4" height="200" src="../assets/img/illustrations/banner-main2.jpg" width="600" />
        <img alt="Tabungan Qurban banner3" class="w-full mb-4" height="200" src="../assets/img/illustrations/banner-main3.jpg" width="400" />
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
            <li>DT Peduli tidak menggunakan dana titfipan kecuali menjelang pelaksanaan qurban.</li>
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
        <form method="POST" action="../service/prosesRegister.php">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Pekurban <span class="text-red-500">*</span></label>
            <div class="flex space-x-4">
              <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Cerah Rupian" type="text" name="nama" required />
              <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Bin Andang" type="text" name="nama_orang_tua" required />
            </div>
            <div class="flex space-x-4 mt-2">
              <span class="w-1/2 text-gray-500 text-sm">Nama Lengkap</span>
              <span class="w-1/2 text-gray-500 text-sm">Bin/Binti Nama Orang Tua</span>
            </div>
          </div>

          <div class="mb-4">
            <input class="w-full p-2 border border-gray-300 rounded" placeholder="Alamat" type="text" name="alamat" required />
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
                    <input class="mr-2" id="qurban-<?php echo $row['qurban_id']; ?>" type="radio" name="qurban_id" value="<?php echo $row['qurban_id']; ?>" required />
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
              title="Format Nomor Handphone Tidak Valid!" />
          </div>

          <!-- Checkbox untuk syarat dan ketentuan -->
          <div class="mb-4">
            <input type="checkbox" id="termsCheckbox" onchange="toggleSubmitButton(this)" />
            <label for="termsCheckbox" class="text-white-700">Saya menyetujui syarat dan ketentuan yang berlaku.</label>
          </div>

          <div class="text-center">
            <button id="submitBtn" class="bg-orange-500 text-white px-6 py-2 rounded" type="submit" disabled>Mendaftar</button>
          </div>
          <div class="text-center">
            <div class="flex justify-center items-center space-x-2">
              <p class="text-white-700 text-sm">Sudah Memiliki Akun?</p>
              <a href="login.php" class="text-blue-500 text-sm hover:underline">Masuk Sekarang</a>
            </div>
          </div>

        </form>
      </div>
    </div>

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
      <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
          ©
          <script>
            document.write(new Date().getFullYear());
          </script>
          , DT Peduli
        </div>
        <div>
          <span>Powered by: <strong>PT. Inovasi Cipta Teknologi</strong></span>
          <span class="mx-2">|</span>
          <span>Support by: <strong>Bank Muamalat Indonesia</strong></span>
        </div>
      </div>
    </footer>
    <!-- / Footer -->
  </div>

  

  <!-- mobile responsive -->
  <div class="block md:hidden w-full mx-auto bg-white">
    <div>
      <div class="w-full flex items-center justify-center">
        <img alt="Tabungan Qurban banner" class="w-11/12 mb-4" src="../assets/img/illustrations/banner-main2.jpg" />
      </div>
      <div class="w-full">
        <form method="POST" action="../service/prosesRegister.php" class="border border-gray-300 mt-4 mx-4" id="formPendaftaran">
          <h1 class="flex items-center justify-center font-bold my-2">Form Pendaftaran Tabungan Qurban</h1>
          <div class="mb-4 mx-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Pekurban <span class="text-red-500">*</span></label>
            <div class="flex space-x-4">
              <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Cerah Rupian" type="text" name="nama" required />
              <input class="w-1/2 p-2 border border-gray-300 rounded" placeholder="Contoh: Bin Andang" type="text" name="nama_orang_tua" required />
            </div>
            <div class="flex space-x-4 mt-2">
              <span class="w-1/2 text-gray-500 text-sm">Nama Lengkap</span>
              <span class="w-1/2 text-gray-500 text-sm">Bin/Binti Nama Orang Tua</span>
            </div>
          </div>

          <div class="mb-4 mx-4">
            <input class="w-full p-2 border border-gray-300 rounded" placeholder="Alamat" type="text" name="alamat" required />
            <div class="mt-2">
              <span class="text-gray-500 text-sm">Alamat</span>
            </div>
          </div>

          <div class="mb-4 mx-4">
            <label class="block text-gray-700 font-semibold mb-2">Wilayah Tebar Indonesia</label>
            <div>
              <?php
              if ($qurbanDataMob->num_rows > 0) {
                while ($row = $qurbanDataMob->fetch_assoc()) {
              ?>
                  <div>
                    <input class="mr-2" id="qurban-<?php echo $row['qurban_id']; ?>" type="radio" name="qurban_id" value="<?php echo $row['qurban_id']; ?>" required />
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
          <div class="mb-4 mx-4">
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
              title="Format Nomor Handphone Tidak Valid!" />
          </div>
        </form>
        <div class="w-11/12 mx-4">
          <img alt="Tabungan Qurban banner3" class="mb-3" src="../assets/img/illustrations/banner-main3.jpg" />
          <p class="text-gray-700 mb-4 text-center">
            DT Peduli memfasilitasi masyarakat menyalurkan hewan kurban impian dari sekarang untuk berkurban di masa mendatang. Melalui tabungan kurban, masyarakat dengan mudah mendapatkan hewan kurban yang diinginkan.
          </p>
        </div>
      </div>
    </div>
    <div class="w-full bg-blue-800 rounded-tr-[25px] h-4/5 rounded-tl-[25px]">
      <div class="w-full flex items-center justify-center">
        <div class="w-3/5 bg-white rounded-md shadow-md flex items-center justify-center mt-5">
          <p class="font-bold py-1">Video Laporan Qurban</p>
        </div>
      </div>
      <div class="my-4 w-full">
        <iframe allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" frameborder="0" class="w-11/12 h-72 mx-auto" src="https://www.youtube.com/embed/f72rzhJAHA0"></iframe>
      </div>
      <div class="w-full bg-white rounded-tr-[30px] h-96 rounded-tl-[30px] mt-16">
        <div class="w-ful flex items-center justify-center">
          <div class="w-6/12 bg-yellow-500 text-white rounded-md shadow-md flex items-center justify-center -mt-4">
            <p class="font-bold py-2 px-4 text-lg">Syarat dan Ketentuan *</p>
          </div>
        </div>
        <div class="mt-4 mx-5">
          <h2 class="text-lg font-semibold text-center text-gray-800 mb-4">Detail Komitmen Tabungan Qurban 1446 H</h2>
          <ol class="list-decimal list-outside text-gray-700 text-sm mb-4 mx-10 text-justify">
            <li class="pl-2">Tabungan qurban menggunakan akad wadi’ah yad amanah (titipan murni), yang akan dilanjutkan menjadi akad wakalah
              dengan mempersilahkan DT Peduli untuk menyalurkan hewan qurban kepada yang lebih berhak menerimanya dalam program
              Unggulkan Qurban DT Peduli 1446 H.</li>
            <li class="pl-2">Tabungan qurban bekerjasama dengan perbankan syari’ah dalam penghimpunannya.</li>
            <li class="pl-2">DT Peduli tidak menggunakan dana titipan kecuali menjelang pelaksanaan qurban.</li>
            <li class="pl-2">Setiap Pendaftar akan mendapatkan satu rekening Virtual Account (VA) khusus untuk tabungan qurban.</li>
            <li class="pl-2">Pendaftar dapat melakukan pembayaran tabungan qurban sesuai dengan waktu dan frekuensi yang disepakati bersama.</li>
            <li class="pl-2">Pelunasan tabungan qurban maksimal H-5 hari raya Idul Adha.</li>
            <li class="pl-2">DT Peduli akan membantu mengingatkan waktu pembayaran serta menginformasikan progres program secara berkala.</li>
            <li class="pl-2">Harga qurban sudah termasuk biaya operasional dan pendistribusian.</li>
            <li class="pl-2">Tabungan qurban tidak dapat ditarik kembali kecuali dalam hal meninggal dunia.</li>
            <li class="pl-2">Tabungan yang tidak lunas akan dikonfirmasi, bisa menjadi sedekah atau dilanjutkan untuk tahun berikutnya.</li>
            <li class="pl-2">Penabung dihimbau untuk melakukan transaksi maksimal 2 kali dalam satu bulan.</li>
          </ol>
          <div class="bg-gray-300 p-4 rounded-lg shadow-inner shadow-md">
            <h3 class="text-md font-semibold text-gray-800 mb-2">[ Pelaksanaan Qurban 1446 H / 2025 M ]</h3>
            <p class="text-gray-700 text-sm">
              Iedul Qurban: 10 Dzulhijjah 1446H | 07 Juni 2025M<br>
              Hari Tasyrik: 11 - 13 Dzulhijjah 1446H | 08 Juni - 10 Juni 2025M
            </p>
          </div>
        </div>
        <div class="text-center my-4">
  <label class="flex justify-center items-center space-x-2 cursor-pointer">
    <input type="checkbox" class="form-checkbox">
    <span class="text-white text-sm">Saya menyetujui syarat dan ketentuan</span>
  </label>
</div>


        <div class="text-center my-4">
          <button id="submitBtn" class="bg-orange-500 text-white px-6 py-2 rounded-md shadow-md cursor-pointer" type="submit" form="formPendaftaran" disabled>Mendaftar</button>
        </div>
        <div class="text-center my-4">
          <div class="flex justify-center items-center space-x-2">
            <p class="text-white text-sm">Sudah Memiliki Akun?</p>
            <a href="login.php" class="text-blue-500 text-sm hover:underline cursor-pointer">Masuk Sekarang</a>
          </div>
        </div>
        <footer class="bg-blue-800 text-white h-72 px-6 py-1">
          <div class="mx-2 mt-4">
            <div class="mb-2 font-bold text-2xl">
              <span class="text-yellow-500">dt</span>peduli
            </div>
            <p class="text-md font-bold">Kontak</p>
            <p class="text-md">Jl. Gegerkalong Girang No. 32 Bandung</p>
            <p class="text-md">081317121712 (Konfirmasi)</p>
            <p class="text-md">info@dtpeduli.org</p>
            <p class="text-xs mt-2">
              Copyright
              <script>
                document.write(new Date().getFullYear());
              </script>
              DT Peduli. All Rights Reserved
            </p>

          </div>
        </footer>
        
        <div class="fixed bottom-0 left-0 w-full text-gray-600">
          <!-- Wrapper -->
          <div class="relative grid h-24 w-full mx-auto grid-cols-2 gap-20 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            <!-- Card 1 -->
            <div class="relative z-10 flex flex-col items-center justify-center rounded-l-lg rounded-r-[2rem] bg-white">
              <a href="" class="transition hover:text-blue-900 active:-translate-y-1 active:underline underline-offset-4 flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="h-10 w-10 mb-2" fill="currentColor">
                  <path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM112 256l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />
                </svg>
                <span class="text-wrap">Cek Laporan Qurban</span>
              </a>
            </div>

            <!-- Card 2 -->
            <div class="relative z-10 flex flex-col items-center justify-center rounded-l-[2rem] rounded-r-lg bg-white">
              <a href="" class="transition hover:text-blue-900 active:-translate-y-1 active:underline underline-offset-4 flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="currentColor" viewBox="0 0 448 512">
                  <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                </svg>
                <span>Hubungi kami</span>
              </a>
            </div>

            <!-- Floating Button -->
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 h-20 w-20 rounded-full border-8 border-white flex items-center justify-center">
              <a href="" class="hover:text-blue-900 transition text-white duration-200 active:-translate-y-1 flex items-center justify-center rounded-full w-full h-full bg-gradient-to-br from-yellow-600 to-yellow-400 group">
                <div class="group-hover:-translate-y-1 transition duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="currentColor" viewBox="0 0 576 512">
                    <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                  </svg>
                </div>
              </a>
            </div>

            <!-- Background Shape -->
            <div class="absolute z-[-1] bottom-0 h-[80%] bg-white -translate-x-1/2 left-1/2 w-1/2"></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>

</html>