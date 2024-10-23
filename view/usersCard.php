<?php
require '../service/data.php';

session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$kartuQurban = getKartuQurban($conn);
$qurbanProgress = getQurbanProgress($conn);
$transaksiData = getTransaksi($conn);
$last_transaksi = getLastTransaksiDaging($conn);
$last_sedekah = getSedekahDaging($conn);

$user = getLoggedInUser($conn);
$tabunganData = getTabunganAndTarget($conn);

?>

<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>DT Peduli - Tabungan Qurban</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/dtpeduli.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <!-- Link Bootstrap CSS (jika belum ada) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>


    <!-- Link Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>

    <!-- Cek apakah ada notifikasi di session -->
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 'success'): ?>
        <!-- Pop-up atau alert notifikasi sukses -->
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_SESSION['status']) && $_SESSION['status'] == 'error'): ?>
        <!-- Pop-up atau alert notifikasi error -->
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar layout-without-menu">
      <div class="layout-container">
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Title -->
              <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <h3 class="text-primary">DT Peduli - Tabungan Qurban</h3>
              </div>
              </div>
              <!-- /title -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img 
                            src="../assets/img/avatars/<?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'P') ? '5.png' : '1.png'; ?>" 
                            alt="Avatar" 
                            class="w-px-40 h-auto rounded-circle" 
                        />
                    </div>
                </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                            <img 
                            src="../assets/img/avatars/<?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'P') ? '5.png' : '1.png'; ?>" 
                            alt="Avatar" 
                            class="w-px-40 h-auto rounded-circle" 
                            />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block"><?php echo htmlspecialchars($user['nama']); ?></span>
                            <small class="text-muted">Shohibul Qurban</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="userProfile.php">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <!-- <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li> -->
                    <!-- <li>
                      <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          <span class="flex-grow-1 align-middle">Billing</span>
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                        </span>
                      </a>
                    </li> -->
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="login.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
				<div class="row">
				
				   <div class="col-lg-8 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                      <div class="card-body">
                          <?php if ($user): ?>
                              <h5 class="card-title text-primary">Tabungan Qurban - <?php echo htmlspecialchars($user['nama']) . ' ' . htmlspecialchars($user['nama_orang_tua']) ?></h5>
                              <p class="mb-4">
                                  Total Tabungan <span class="fw-bold">Rp <?php echo number_format($tabunganData['total_tabungan'], 0, ',', '.'); ?></span>
                                  Dari Target <span class="fw-bold">Rp <?php echo number_format($tabunganData['target_tabungan'], 0, ',', '.'); ?></span>
                              </p>
                              <div class="d-flex p-4 pt-3">
                                  <div>
                                      <!-- Konten tambahan jika diperlukan -->
                                  </div>
                              </div>
                              <p class="mb-4">
                                  Alamat <span class="fw-bold"><?php echo htmlspecialchars($user['alamat']); ?></span> 
                              </p>
                              <p class="mb-4">
                                  Nomor Handphone <span class="fw-bold"><?php echo htmlspecialchars($user['nomor_hp']); ?></span> 
                              </p>
                          <?php else: ?>
                              <p class="text-danger">Pengguna tidak ditemukan atau belum login.</p>
                          <?php endif; ?>
                      </div>
                  </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="../assets/img/illustrations/grupqurban.png"
                            height="170"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
				
<!-- Expense Overview -->
<div class="col-md-6 col-lg-4 order-1 mb-4">
  <div class="card h-100">
    <div class="card-body px-0">
      <div class="tab-content p-0">
        <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
          
          <!-- Bagian Transaksi Terakhir -->
          <div class="d-flex p-4 pt-3">
            <div class="avatar flex-shrink-0 me-3">
              <img src="../assets/img/icons/unicons/wallet.png" alt="User" />
            </div>
            <div>
              <small class="text-muted d-block">Transaksi Terakhir</small>
              <div class="d-flex align-items-center">
                <?php if ($last_transaksi): ?>
                  <h6 class="mb-0 me-1">Rp <?= number_format($last_transaksi['jumlah_sedekah'], 0, ',', '.') ?></h6>
                  <small class="text-success fw-semibold">
                    <i class="bx bx-chevron-up"></i>
                    <?= date('d F Y', strtotime($last_transaksi['tanggal_sedekah'])) ?>
                  </small>
                <?php else: ?>
                  <h6 class="mb-0 me-1">Belum ada transaksi</h6>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Bagian Sedekah Daging -->
          <div class="d-flex p-4 pt-3">
            <div class="avatar flex-shrink-0 me-3">
              <img src="../assets/img/icons/unicons/wallet.png" alt="User" />
            </div>
            <div>
              <small class="text-muted d-block">Sedekah Daging</small>
              <div class="d-flex align-items-center">
                <?php if ($last_sedekah > 0): ?>
                  <h6 class="mb-0 me-1">Rp <?= number_format($last_sedekah, 0, ',', '.') ?></h6>
                <?php else: ?>
                  <h6 class="mb-0 me-1">Belum ada sedekah</h6>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Expense Overview -->
				</div>
				<div class="row">
				 <!-- Order Statistics -->
				<div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Kartu Qurban</h5>
                        <!-- <small class="text-muted">4 Kartu </small> -->
                      </div>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="orederStatistics"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                          <a class="dropdown-item" href="javascript:void(0);">Tambah Qurban</a>
                          
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-center gap-1">
                          <h2 class="mb-2">Rp <?php echo number_format($tabunganData['target_tabungan'], 0, ',', '.'); ?></h2>
                          <span>Total Qurban</span>
                        </div>
						
                        <div id="orderStatisticsChart"></div>
                      </div>
                      <ul class="p-0 m-0">
                        <?php if ($kartuQurban): ?>
                            <?php foreach ($kartuQurban as $kartu): ?>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-mobile-alt"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0"><?php echo $kartu['tipe_qurban']; ?></h6>
                                            <small class="text-muted"><?php echo $kartu['nama_pengqurban']; ?></small>
                                        </div>
                                        <div class="user-progress">
                                            <small class="fw-semibold">Rp <?php echo number_format($kartu['biaya'], 0, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Tidak ada kartu qurban untuk ditampilkan.</li>
                        <?php endif; ?>
                    </ul>
                    </div>
                  </div>
                </div>
				
				 <!-- Transactions -->
                <div class="col-md-6 col-lg-4 order-2 mb-4">
                  <div class="card overflow-hidden mb-4" style="height: 470px">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">Transaksi</h5>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="transactionID"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                          <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body" id="vertical-example">
					<p>
          <ul class="p-0 m-0">
            <?php if ($transaksiData): ?>
                <?php foreach ($transaksiData as $transaksi): ?>
                    <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <img src="../assets/img/icons/unicons/<?php echo $transaksi['metode_pembayaran'] == 'VA' ? 'paypal.png' : 'wallet.png'; ?>" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <small class="text-muted d-block mb-1"><?php echo date('d F Y', strtotime($transaksi['tanggal_transaksi'])); ?></small>
                                <h6 class="mb-0"><?php echo $transaksi['tipe']; ?></h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <h6 class="mb-0"><?php echo number_format($transaksi['jumlah'], 0, ',', '.'); ?></h6>
                                <span class="text-muted"><?php echo $transaksi['metode_pembayaran']; ?></span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Tidak ada transaksi untuk ditampilkan.</li>
            <?php endif; ?>
        </ul>
					  </p>
                    </div>
                  </div>
                </div>
				 <!-- Expense Overview -->
                <div class="col-md-6 col-lg-4 order-1 mb-4">
                  <div class="card h-100">
                    
                    <div class="card-body px-0">
                      <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                          <div class="d-flex p-4 pt-3">
                            <div class="avatar flex-shrink-0 me-3">
                              <img src="../assets/img/icons/unicons/wallet.png" alt="User" />
                            </div>
                            <?php
                            if ($user) {
                                $saldo = $user['saldo'];
                                // $persentase = $user['persentase'];
                            ?>
                                <div>
                                    <small class="text-muted d-block">Total Saldo</small>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0 me-1">Rp <?= number_format($saldo, 0, ',', '.') ?></h6>
                                    </div>
                                </div>
                            <?php
                            } else {
                                echo "Data saldo tidak tersedia.";
                            }
                            ?>

                          </div>
                          
                    <div class="card-body">
               <?php       
                if ($qurbanProgress) {
                    foreach ($qurbanProgress as $qurban) {
                        ?>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-mobile-alt"></i>
                                </span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-2"><?= htmlspecialchars($qurban['tipe_qurban']) ?></h6>
                                        <small class="text-success fw-semibold">
                                            <i class="bx bx-chevron-up"></i>
                                            Terkumpul: Rp<?= number_format($qurban['jumlah_terkumpul'], 0, ',', '.') ?>
                                        </small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $qurban['progress'] ?>%" aria-valuenow="<?= $qurban['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= round($qurban['progress']) ?>%
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalSetor<?= $qurban['kartu_qurban_id'] ?>">Setor</button>

<!-- Modal Setoran -->
<div class="modal fade" id="modalSetor<?= $qurban['kartu_qurban_id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setor untuk <?= htmlspecialchars($qurban['tipe_qurban']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="setorForm<?= $qurban['kartu_qurban_id'] ?>">
                    <input type="hidden" name="kartu_qurban_id" value="<?= $qurban['kartu_qurban_id'] ?>">
                    <div class="mb-3">
                        <label for="jumlahSetoran" class="form-label">Jumlah Setoran</label>
                        <input type="number" class="form-control" name="jumlah_setoran" id="jumlahSetoran<?= $qurban['kartu_qurban_id'] ?>" placeholder="Masukkan jumlah setoran" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="openPaymentModal(<?= $qurban['kartu_qurban_id'] ?>)">Simpan Setoran</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Setoran -->


<!-- Modal Pilihan Metode Pembayaran -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Metode Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentMethodForm" method="post" action="../service/prosesTagihan.php">
                    <input type="hidden" name="kartu_qurban_id" id="modalQurbanId">
                    <input type="hidden" name="jumlah_setoran" id="modalJumlahSetoran">
                    <!-- Input hidden untuk menyimpan metode pembayaran yang dipilih -->
                    <input type="hidden" name="metode_pembayaran" id="metodePembayaran" required>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <div class="row text-center">
                            <div class="col-6">
                                <!-- Tombol metode pembayaran QRIS dengan ikon -->
                                <button type="button" class="btn btn-outline-primary payment-method-btn" id="qrisButton" onclick="selectPaymentMethod('qris')">
                                    <i class="bi bi-qr-code" style="font-size: 2rem;"></i><br>
                                    QRIS
                                </button>
                            </div>
                            <div class="col-6">
                                <!-- Tombol metode pembayaran Virtual Account dengan ikon -->
                                <button type="button" class="btn btn-outline-primary payment-method-btn" id="vaButton" onclick="selectPaymentMethod('va')">
                                    <i class="bi bi-credit-card" style="font-size: 2rem;"></i><br>
                                    Virtual Account
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="btnBayarSekarang">Bayar Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

                                <div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrisModalLabel">Pembayaran QRIS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="qrisModalBody">
                <!-- QRIS code akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Detail Tagihan -->
<div class="modal fade" id="modalDetailTagihan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Tagihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p><strong>Tipe Qurban:</strong> <span id="detailTipeQurban"></span></p>
                        <p><strong>Jumlah Setoran:</strong> Rp <span id="detailJumlahSetoran"></span></p>
                        <p><strong>Tanggal Tagihan:</strong> <span id="detailTanggalTagihan"></span></p>
                        <h3>Silahkan Lakukan Pembayaran Melalui</h3>
                        <h2><strong>Virtual Account:</strong></h2>
                        
                        <!-- VA Number with Copy Button -->
                        <div class="d-flex justify-content-center align-items-center">
                            <h2><span id="detailVaNumber" class="text-primary"></span></h2>
                            <button class="btn btn-outline-primary btn-sm ms-3" onclick="copyVaNumber()">
                                <i class="fas fa-copy"></i> Salin
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Collapsible Instructions -->
                <div class="accordion mt-4" id="accordionInstructions">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Lihat Langkah-langkah Pembayaran
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionInstructions">
                            <div class="accordion-body">
                                <ol>
                                    <li>Lakukan pembayaran melalui Transfer Antar Bank.</li>
                                    <li>Pilih bank tujuan <strong>BANK MUAMALAT INDONESIA</strong>.</li>
                                    <li>Masukkan Nomor Rekening berupa <strong>Nomor Virtual Account</strong> yang muncul di atas.</li>
                                    <li>Masukkan nominal sesuai jumlah pembayaran yang harus dilakukan.</li>
                                    <li>Klik <strong>Kirim</strong> atau <strong>Lanjutkan Pembayaran</strong>.</li>
                                    <li>Pembayaran selesai.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



                            </div>
                        </li>
                        <?php
                    }
                }
                ?>
				          </div>	  
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Expense Overview -->
				
				
            </div>
            <!-- / Content -->

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
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
    </div>
    <!-- / Layout wrapper -->

<!-- Tombol untuk membuka modal -->
<div class="buy-now">
  <button type="button" class="btn btn-danger btn-buy-now" data-bs-toggle="modal" data-bs-target="#modalTambahKartuQurban">
    Tambah Hewan Qurban
  </button>
</div>

<!-- Modal Tambah Kartu Qurban -->
<div class="modal fade" id="modalTambahKartuQurban" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Hewan Qurban</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="../service/prosesTambahQurban.php">
                    <!-- Input Nama Pengqurban -->
                    <div class="mb-3">
                        <label for="namaPengqurban" class="form-label">Nama Pengqurban</label>
                        <input type="text" class="form-control" name="nama_pengqurban" id="namaPengqurban" placeholder="Masukkan nama pengqurban" required>
                    </div>

                    <!-- Pilihan Qurban -->
                    <div class="mb-3">
                        <label for="qurbanId" class="form-label">Pilih Qurban</label>
                        <select class="form-select" name="qurban_id" id="qurbanId" required>
                            <?php
                            // Ambil data qurban dari database
                            $sql = "SELECT qurban_id, tipe_qurban, biaya, jenis FROM qurban WHERE status = 'aktif'";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                              echo "<option value='{$row['qurban_id']}'>{$row['tipe_qurban']} - Rp " . number_format($row['biaya'], 0, ',', '.') . " <b>(" . $row['jenis'] . ")</b></option>";
                          }
                            ?>
                        </select>
                    </div>

                    <!-- Hidden Input for User ID -->
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user']['user_id'] ?>">

                    <button type="submit" class="btn btn-primary">Tambah Kartu Qurban</button>
                </form>
            </div>
        </div>
    </div>
</div>



    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modalPilihanPayment.js"></script>

      <!-- Page JS -->
    <script src="../assets/js/extended-ui-perfect-scrollbar.js"></script>


    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- css -->
    <style>
    /* Gaya tombol metode pembayaran */
    .payment-method-btn {
        width: 100%;
        padding: 20px;
        font-size: 1.2rem;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    /* Gaya tombol yang aktif */
    .payment-method-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* Hover effect untuk mempercantik tampilan */
    .payment-method-btn:hover {
        background-color: #0d6efd;
        color: white;
    }

    /* Icon size adjustment */
    .payment-method-btn i {
        margin-bottom: 10px;
    }

    /* Penyesuaian modal */
    .modal-body {
        padding: 30px;
    }

    /* Style form label */
    .form-label {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>
  </body>
</html>
<?php
// Hapus session setelah notifikasi ditampilkan
unset($_SESSION['status']);
unset($_SESSION['message']);
?>