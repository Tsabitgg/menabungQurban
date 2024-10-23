function openPaymentModal(kartuQurbanId) {
    const jumlahSetoran = document.getElementById(`jumlahSetoran${kartuQurbanId}`).value;
    
    // Set nilai input hidden ke dalam modal
    document.getElementById('modalQurbanId').value = kartuQurbanId;
    document.getElementById('modalJumlahSetoran').value = jumlahSetoran;

    // Tampilkan modal pilihan metode pembayaran
    const paymentModal = new bootstrap.Modal(document.getElementById('modalPembayaran'));
    paymentModal.show();
}

function selectPaymentMethod(method) {
    // Set metode pembayaran
    document.getElementById('metodePembayaran').value = method;

    // Handle tombol Bayar Sekarang
    const btnBayarSekarang = document.getElementById('btnBayarSekarang');
    btnBayarSekarang.onclick = function () {
        const formData = new FormData(document.getElementById('paymentMethodForm'));
        
        // Kirim data ke prosesTagihan.php
        fetch('../service/prosesTagihan.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (method === 'qris') {
                    // Jika metode pembayaran QRIS, ambil created_time
                    const createdTime = data.created_time;
                    generateQris(createdTime); // Panggil fungsi untuk generate QRIS
                } else if (method === 'va') {
                    // Jika metode pembayaran VA, tampilkan detail tagihan
                    displayBillDetails(data);
                }
            } else {
                alert(data.message || 'Terjadi kesalahan, coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran.');
        });
    };
}

function generateQris(createdTime) {
    // Panggil generateQris.php dengan createdTime
    fetch(`generateQris.php?createdTime=${createdTime}`)
        .then(response => response.text())
        .then(qrisCode => {
            // Tampilkan QRIS code di modal
            document.getElementById('qrisModalBody').innerHTML = qrisCode; // Asumsikan QRIS code berupa HTML
            const qrisModal = new bootstrap.Modal(document.getElementById('qrisModal'));
            qrisModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghasilkan QRIS.');
        });
}

function displayBillDetails(data) {
    // Tampilkan detail tagihan di modal
    document.getElementById('detailTipeQurban').textContent = data.tipe_qurban;
    document.getElementById('detailJumlahSetoran').textContent = data.jumlah_setoran;
    document.getElementById('detailTanggalTagihan').textContent = data.tanggal_tagihan;
    document.getElementById('detailVaNumber').textContent = data.va_number;

    // Tampilkan modal detail tagihan
    const detailModal = new bootstrap.Modal(document.getElementById('modalDetailTagihan'));
    detailModal.show();
}

function copyVaNumber() {
    const vaNumberElement = document.getElementById('detailVaNumber');
    const vaNumber = vaNumberElement.textContent;

    // Salin VA Number ke clipboard
    navigator.clipboard.writeText(vaNumber).then(() => {
        alert('VA Number telah disalin ke clipboard.');
    }).catch(err => {
        console.error('Gagal menyalin VA Number:', err);
    });
}
