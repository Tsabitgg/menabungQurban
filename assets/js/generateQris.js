src="https://code.jquery.com/jquery-3.6.0.min.js"

    $(document).ready(function() {
        $('#btnBayarSekarang').on('click', function() {
            // Ambil nilai form
            var metodePembayaran = $('#metodePembayaran').val();
            var qurbanId = $('#modalQurbanId').val();
            var jumlahSetoran = $('#modalJumlahSetoran').val();

            if (metodePembayaran === 'qris') {
                // Kirim request AJAX ke API generateQris.php
                $.ajax({
                    url: '../service/generateQris.php', // URL ke API
                    type: 'GET',
                    data: {
                        createdTime: qurbanId // Menggunakan qurbanId sebagai createdTime
                    },
                    success: function(response) {
                        try {
                            // Parse response JSON
                            var responseData = JSON.parse(response);
                            
                            if (responseData.transactionDetail && responseData.transactionDetail.transactionQrId) {
                                var transactionQrId = responseData.transactionDetail.transactionQrId;
                                var rawQrData = responseData.transactionDetail.rawQr; // Asumsikan QRIS data berupa raw QR

                                // Tampilkan QRIS di modal
                                $('#qrisModalBody').html('<img src="data:image/png;base64,' + rawQrData + '" alt="QRIS Code">');
                                $('#qrisModal').modal('show');
                            } else {
                                alert('Transaction QR ID tidak ditemukan dalam response.');
                            }
                        } catch (e) {
                            alert('Error parsing JSON: ' + e.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error: ' + textStatus + ' ' + errorThrown);
                    }
                });
            } else {
                alert('Metode pembayaran belum didukung.');
            }
        });
    });