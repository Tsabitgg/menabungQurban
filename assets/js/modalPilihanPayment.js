function openPaymentModal(kartuQurbanId) {
  const jumlahSetoran = document.getElementById(
    `jumlahSetoran${kartuQurbanId}`
  ).value;

  // Set nilai input hidden ke dalam modal
  document.getElementById("modalQurbanId").value = kartuQurbanId;
  document.getElementById("modalJumlahSetoran").value = jumlahSetoran;

  // Tutup modal pembayaran sebelum memproses
  const modalSetorId = `modalSetor${kartuQurbanId}`; // Pastikan ID modal benar
  const setorModalElement = document.getElementById(modalSetorId);

  if (setorModalElement) {
    const setorModal = bootstrap.Modal.getInstance(setorModalElement);
    if (setorModal) {
      setorModal.hide(); // Tutup modal pembayaran jika instance ditemukan
    }
  } else {
    console.error(`Modal dengan ID ${modalSetorId} tidak ditemukan.`);
  }

  // Tampilkan modal pilihan metode pembayaran
  const paymentModal = new bootstrap.Modal(
    document.getElementById("modalPembayaran")
  );
  paymentModal.show();
}

function selectPaymentMethod(method) {
  // Set metode pembayaran
  document.getElementById("metodePembayaran").value = method;

  // Handle tombol Bayar Sekarang
  const btnBayarSekarang = document.getElementById("btnBayarSekarang");
  btnBayarSekarang.onclick = function () {
    const formData = new FormData(document.getElementById("paymentMethodForm"));

    // Tutup modal pembayaran sebelum memproses
    const paymentModal = bootstrap.Modal.getInstance(
      document.getElementById("modalPembayaran")
    );
    paymentModal.hide(); // Tutup modal pembayaran

    // Kirim data ke prosesTagihan.php
    fetch("../service/prosesTagihan.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (method === "qris") {
            // Jika metode pembayaran QRIS, ambil created_time
            const createdTime = data.created_time;
            generateQris(createdTime); // Panggil fungsi untuk generate QRIS
          } else if (method === "va") {
            // Jika metode pembayaran VA, tampilkan detail tagihan
            displayBillDetails(data);
          }
        } else {
          alert(data.message || "Terjadi kesalahan, coba lagi.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat memproses pembayaran.");
      });
  };
}

function generateQris(createdTime) {
  // Panggil generateQris.php dengan createdTime
  fetch(`../service/generateQris.php?createdTime=${createdTime}`)
    .then((response) => response.json()) // Asumsikan response dalam format JSON
    .then((data) => {
      const rawQrData = data.transactionDetail.rawQrData; // Ambil rawQrData dari response

      // Hapus QR code lama jika ada
      document.getElementById("qrisCode").innerHTML = "";

      // Generate QR code baru
      const qrcode = new QRCode(document.getElementById("qrisCode"), {
        text: rawQrData, // Gunakan rawQrData untuk QR code
        width: 256, // Ukuran QR code
        height: 256,
      });

      // Tampilkan modal QRIS
      const qrisModal = new bootstrap.Modal(
        document.getElementById("qrisModal")
      );
      qrisModal.show();

      // Tambahkan fungsi download QR code
      document
        .getElementById("downloadQrBtn")
        .addEventListener("click", function () {
          const qrCanvas = document.querySelector("#qrisCode canvas");

          // Membuat canvas baru untuk menambahkan judul dan deskripsi
          const customCanvas = document.createElement("canvas");
          const context = customCanvas.getContext("2d");

          // Ukuran canvas baru (menyesuaikan dengan ukuran QR code + tambahan teks)
          const qrWidth = qrCanvas.width * 2; // Menambah resolusi QR code 2x
          const qrHeight = qrCanvas.height * 2; // Menambah resolusi QR code 2x
          const padding = 20;
          const titleHeight = 40;
          const descriptionHeight = 60;
          customCanvas.width = qrWidth + padding * 2;
          customCanvas.height =
            qrHeight + titleHeight + descriptionHeight + padding * 2;

          // Latar belakang putih
          context.fillStyle = "#fff";
          context.fillRect(0, 0, customCanvas.width, customCanvas.height);

          // Tambahkan judul
          context.fillStyle = "#000";
          context.font = "bold 20px Arial";
          context.textAlign = "center";
          context.fillText(
            "QRIS Tabungan Qurban",
            customCanvas.width / 2,
            titleHeight
          );

          // Tambahkan deskripsi
          context.font = "16px Arial";
          context.fillText(
            "Gunakan e-Wallet atau M-Banking Anda",
            customCanvas.width / 2,
            customCanvas.height - padding - descriptionHeight / 2
          );

          // Gambar QR code di tengah dengan ukuran yang lebih besar
          context.drawImage(
            qrCanvas,
            padding,
            titleHeight + padding,
            qrWidth,
            qrHeight
          );

          // Konversi canvas baru ke gambar
          const qrImage = customCanvas.toDataURL("image/png");
          const link = document.createElement("a");
          link.href = qrImage;
          link.download = "qris_code.png";
          link.click();
        });

      // // Tampilkan waktu expiry
      // const expiryTime = data.transactionDetail.expiredTime;
      // const expiryMinutes = Math.floor((new Date(expiryTime) - new Date()) / (1000 * 60)); // Hitung waktu kadaluarsa
      // document.getElementById('expiryTime').textContent = expiryMinutes;
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Terjadi kesalahan saat menghasilkan QRIS.");
    });
}

function displayBillDetails(data) {
  // Tampilkan detail tagihan di modal
  document.getElementById("detailTipeQurban").textContent = data.tipe_qurban;
  document.getElementById("detailJumlahSetoran").textContent =
    data.jumlah_setoran;
  document.getElementById("detailTanggalTagihan").textContent =
    data.tanggal_tagihan;
  document.getElementById("detailVaNumber").textContent = data.va_number;

  // Tampilkan modal detail tagihan
  const detailModal = new bootstrap.Modal(
    document.getElementById("modalDetailTagihan")
  );
  detailModal.show();
}

function copyVaNumber() {
  const vaNumberElement = document.getElementById("detailVaNumber");
  const vaNumber = vaNumberElement.textContent;

  // Salin VA Number ke clipboard
  navigator.clipboard
    .writeText(vaNumber)
    .then(() => {
      alert("VA Number telah disalin ke clipboard.");
    })
    .catch((err) => {
      console.error("Gagal menyalin VA Number:", err);
    });
}
