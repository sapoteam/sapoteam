<?php 
$current_page = basename($_SERVER['PHP_SELF']); 
$active_tiket = in_array($current_page, ['tiket_reservasi.php', 'form_reservasi.php']);
$active_produk = in_array($current_page, ['produk.php', 'detail_produk.php']);
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
            <a class="nav-link <?= $active_tiket ? 'active' : ''; ?>" href="tiket_reservasi.php">Tiket & Reservasi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_produk ? 'active' : ''; ?>" href="produk.php">Produk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'review.php') ? 'active' : ''; ?>" href="review.php">Review</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div id="harvestBanner" class="harvest-banner">
    <div class="harvest-content">
        <i class="bi bi-stars pulse-icon"></i>
        <span>Oemah Keboen sedang musim panen. Ayo datang dan petik buah segar langsung dari pohonnya!</span>
    </div>
    <button class="close-banner" aria-label="Close" onclick="closeHarvestBanner()">
        <i class="bi bi-x"></i>
    </button>
</div>