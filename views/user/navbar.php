<?php 
// Mendapatkan nama file yang sedang dibuka
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="../../assets/img/logo.png" alt="Logo Oemah Keboen">
      <span class="brand-text">Oemah Keboen</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto gap-lg-3 text-center align-items-center">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Beranda</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'tiketreservasi.php') ? 'active' : ''; ?>" href="tiketreservasi.php">Tiket & Reservasi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'produk.php') ? 'active' : ''; ?>" href="produk.php">Produk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'review.php') ? 'active' : ''; ?>" href="review.php">Review</a>
        </li>
      </ul>
    </div>
  </div>
</nav>