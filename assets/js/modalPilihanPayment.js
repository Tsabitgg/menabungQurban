function openPaymentModal(kartuQurbanId) {
  var jumlahSetoran = document.getElementById(
    "jumlahSetoran" + kartuQurbanId
  ).value;

  // Validasi jika input jumlah setoran kosong
  if (jumlahSetoran === "") {
    alert("Harap masukkan jumlah setoran.");
    return;
  }

  // Set nilai di modal pembayaran
  document.getElementById("modalQurbanId").value = kartuQurbanId;
  document.getElementById("modalJumlahSetoran").value = jumlahSetoran;

  // Tutup modal setoran dan buka modal pembayaran
  $("#modalSetor" + kartuQurbanId).modal("hide");
  $("#modalPembayaran").modal("show");
}

function selectPaymentMethod(method) {
  // Menyimpan metode pembayaran yang dipilih di input hidden dengan id 'metodePembayaran'
  document.getElementById('metodePembayaran').value = method;
  
  // Opsional: Update button styles untuk menunjukkan metode yang dipilih
  document.getElementById('qrisButton').classList.remove('active');
  document.getElementById('vaButton').classList.remove('active');
  
  if (method === 'qris') {
      document.getElementById('qrisButton').classList.add('active');
  } else if (method === 'va') {
      document.getElementById('vaButton').classList.add('active');
  }
}


document
  .getElementById("btnBayarSekarang")
  .addEventListener("click", function () {
    var metodePembayaran = document.getElementById("metodePembayaran").value;
    var kartuQurbanId = document.getElementById("modalQurbanId").value;
    var jumlahSetoran = document.getElementById("modalJumlahSetoran").value;

    if (metodePembayaran === "qris") {
      // Kirim request ke prosesTagihan.php dan tunggu respons dengan createdTime
      fetch("../config/prosesTagihan.php", {
        method: "POST",
        body: new FormData(document.getElementById("paymentMethodForm")),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          if (data.success) {
            var createdTime = data.created_time; // Ambil createdTime dari respons
            console.log("Created Time: ", createdTime);

            // Setelah mendapatkan createdTime, hit generateQris.php dengan metode POST dan query string
            fetch(`../config/generateQris.php?createdTime=${createdTime}`, {
                method: "POST", 
                headers: {
                  Accept: "application/json",
                },
              })
                .then((response) => response.text())  // Mengambil respons sebagai teks mentah
                .then((responseText) => {
                  console.log("Response Text: ", responseText); // Cek respons mentah dari server
                  
                  // Lalu coba parse ke JSON
                  try {
                    const responseData = JSON.parse(responseText);
                    console.log("Parsed JSON: ", responseData);
              
                    if (responseData.transactionDetail && responseData.transactionDetail.transactionQrId) {
                      var rawQrData = responseData.transactionDetail.rawQr;
                      $("#qrisModalBody").html(
                        '<img src="data:image/png;base64,' + rawQrData + '" alt="QRIS Code">'
                      );
                      $("#qrisModal").modal("show");
                    } else {
                      alert("Transaction QR ID tidak ditemukan dalam response.");
                    }
                  } catch (e) {
                    alert("Error parsing JSON: " + e.message);
                  }
                })
                .catch((error) => console.error("Error fetching QRIS data:", error));
              
              
          } else {
            alert("Terjadi kesalahan dalam membuat tagihan.");
          }
        })
        .catch((error) => console.error("Error:", error));
    } else if (metodePembayaran === "va") {
      fetch("../config/prosesTagihan.php", {
        method: "POST",
        body: new FormData(document.getElementById("paymentMethodForm")),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("detailTipeQurban").textContent =
              data.tipe_qurban;
            document.getElementById("detailJumlahSetoran").textContent =
              new Intl.NumberFormat("id-ID").format(data.jumlah_setoran);
            document.getElementById("detailVaNumber").textContent =
              data.va_number;

            var modalDetailTagihan = new bootstrap.Modal(
              document.getElementById("modalDetailTagihan")
            );
            modalDetailTagihan.show();
          } else {
            alert("Terjadi kesalahan dalam memproses pembayaran.");
          }
        })
        .catch((error) => console.error("Error:", error));
    } else {
      alert("Metode pembayaran tidak valid.");
    }
  });


// Function to copy VA number to clipboard
function copyVaNumber() {
  const vaNumber = document.getElementById("detailVaNumber").textContent;
  navigator.clipboard.writeText(vaNumber).then(
    function () {
      alert("Nomor Virtual Account disalin ke clipboard!");
    },
    function (err) {
      console.error("Gagal menyalin teks: ", err);
    }
  );
}
