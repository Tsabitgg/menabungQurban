<?php
require '../config/data.php';
session_start();


$user = getLoggedInUser($conn);


$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['user_id'];
        
        // Mengambil data dari form
        $nama = $_POST['nama'];
        $nama_orang_tua = $_POST['nama_orang_tua'];
        $email = $_POST['email'];
        $alamat = $_POST['alamat'];
        $kota = $_POST['kota'];
        $nomor_hp = $_POST['nomor_hp'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tanggal_lahir = $_POST['tanggal_lahir'];

        // Panggil fungsi untuk memperbarui profil
        if (updateUserProfile($conn, $userId, [
            'nama' => $nama,
            'nama_orang_tua' => $nama_orang_tua,
            'email' => $email,
            'alamat' => $alamat,
            'kota' => $kota,
            'nomor_hp' => $nomor_hp,
            'jenis_kelamin' => $jenis_kelamin,
            'tanggal_lahir' => $tanggal_lahir
        ])) {
            // Perbarui session dengan data baru
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['nama_orang_tua'] = $nama_orang_tua;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['alamat'] = $alamat;
            $_SESSION['user']['kota'] = $kota;
            $_SESSION['user']['nomor_hp'] = $nomor_hp;
            $_SESSION['user']['jenis_kelamin'] = $jenis_kelamin;
            $_SESSION['user']['tanggal_lahir'] = $tanggal_lahir;

            // Set success flag untuk notifikasi
            $_SESSION['updateSuccess'] = true;
        } else {
            $_SESSION['updateSuccess'] = false;
        }
        header('Location: userProfile.php');
    } else {
        echo "Anda harus login untuk mengedit profil.";
    }
}

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

    <!-- Core CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
          <a href="https://www.dtpeduli.org" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="../assets/img/favicon/dtpeduli.png" alt="Logo" width="150">
            </span>
        </a>



            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item">
              <a href="usersCard.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <li class="menu-item active open">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings">Account Settings</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item active">
                  <a href="#" class="menu-link">
                    <div data-i18n="Account">Account</div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

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
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="auth-login-basic.html">
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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <!-- Account -->
                    <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                          src="../assets/img/avatars/<?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'P') ? '5.png' : '1.png'; ?>"
                          alt="user-avatar"
                          class="d-block rounded"
                          height="100"
                          width="100"
                          id="uploadedAvatar"
                        />
                      </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                    <form id="formAccountSettings" method="POST">
                        <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input
                            class="form-control"
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= isset($user['nama']) ? htmlspecialchars($user['nama']) : '' ?>"
                            autofocus
                            />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="nama_orang_tua" class="form-label">Nama Orang Tua</label>
                            <input
                            class="form-control"
                            type="text"
                            name="nama_orang_tua"
                            id="nama_orang_tua"
                            value="<?= isset($user['nama_orang_tua']) ? htmlspecialchars($user['nama_orang_tua']) : '' ?>"
                            />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                            class="form-control"
                            type="email"
                            id="email"
                            name="email"
                            value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>"
                            placeholder="john.doe@example.com"
                            />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input
                            type="text"
                            class="form-control"
                            id="alamat"
                            name="alamat"
                            value="<?= isset($user['alamat']) ? htmlspecialchars($user['alamat']) : '' ?>"
                            />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="kota">Kota</label>
                            <div class="input-group input-group-merge">
                            <input
                                type="text"
                                id="kota"
                                name="kota"
                                class="form-control"
                                value="<?= isset($user['kota']) ? htmlspecialchars($user['kota']) : '' ?>"
                                placeholder="Nama Kota"
                            />
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="nomor_hp" class="form-label">No. Hp</label>
                            <input
                            type="text"
                            class="form-control"
                            id="nomor_hp"
                            name="nomor_hp"
                            pattern="^(\\+62|62|08)[0-9]{8,}$"
                            title="Format Nomor Handphone Tidak Valid!"
                            value="<?= isset($user['nomor_hp']) ? htmlspecialchars($user['nomor_hp']) : '' ?>"
                            placeholder="Nomor Hp"
                            />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="" disabled <?= empty($user['jenis_kelamin']) ? 'selected' : '' ?>>Pilih Jenis Kelamin</option>
                                <option value="L" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'P') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input
                            type="date"
                            class="form-control"
                            id="tanggal_lahir"
                            name="tanggal_lahir"
                            value="<?= isset($user['tanggal_lahir']) ? htmlspecialchars($user['tanggal_lahir']) : '' ?>"
                            placeholder="Tanggal Lahir"
                            />
                        </div>
                        </div>
                        <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        </div>
                    </form>
                    </div>
                    <!-- /Account -->
                  </div>
                </div>
              </div>
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

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->


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
    
        <!-- toast edit -->
    <?php
// Cek jika ada status update di session
if (isset($_SESSION['updateSuccess'])) {
    $updateSuccess = $_SESSION['updateSuccess'];
    // Hapus flash message dari session
    unset($_SESSION['updateSuccess']);
} else {
    $updateSuccess = null;
}
?>

<script>
    // Tampilkan toast jika ada status update dari session
    <?php if ($updateSuccess === true): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Profil berhasil diperbarui.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    <?php elseif ($updateSuccess === false): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Gagal memperbarui profil. Silakan coba lagi.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    <?php endif; ?>
</script>


    <!-- Page JS -->
    <script src="../assets/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
