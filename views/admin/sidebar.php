
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img
            src="../../assets/img/logo.png"
            alt="Logo Oemah Keboen"
            class="sidebar-logo"
        >
        <h4 class="font-serif m-0">Oemah Keboen</h4>
        <small style="color: rgba(255,255,255,0.7); font-size: 0.8rem;">Portal Pengelola</small>
    </div>

    <ul class="nav-sidebar">
        <li><a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i> <span>Dashboard</span></a></li>
        <li><a href="kelola_reservasi.php" class="nav-link <?= ($current_page == 'kelola_reservasi.php') ? 'active' : '' ?>"><i class="bi bi-calendar4-week"></i> <span>Kelola Reservasi</span></a></li>
        <li><a href="kelola_produk.php" class="nav-link <?= ($current_page == 'kelola_produk.php') ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> <span>Kelola Produk</span></a></li>
        <li><a href="kelola_fasilitas.php" class="nav-link <?= ($current_page == 'kelola_fasilitas.php') ? 'active' : '' ?>"><i class="bi bi-building"></i> <span>Kelola Fasilitas</span></a></li>
        <li><a href="kelola_ulasan.php" class="nav-link <?= ($current_page == 'kelola_ulasan.php') ? 'active' : '' ?>"><i class="bi bi-chat-left-dots"></i> <span>Kelola Ulasan</span></a></li>
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'Admin'): ?>
            <li>
                <a href="kelola_pegawai.php" class="nav-link <?= ($current_page == 'kelola_pegawai.php') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i> <span>Kelola Pegawai</span>
                </a>
            </li>
        <?php endif; ?>    
    </ul>

    <div class="sidebar-footer">
        <a href="javascript:void(0)" class="btn-logout" @click="showLogoutModal = true">Keluar Sistem</a>
    </div>
</div>

<div 
    class="sidebar-overlay" 
    :class="{ show: isSidebarMobileOpen }" 
    @click="closeSidebarMobile"
></div>

<transition name="fade">
    <div class="modal-overlay" v-if="showLogoutModal" style="z-index: 9999; background: rgba(0,0,0,0.6);" @click.self="showLogoutModal = false">
        <div class="modal-box text-center" style="max-width: 380px;">
            <div class="text-warning mb-3" style="font-size: 3.5rem;"><i class="bi bi-box-arrow-left"></i></div>
            <h4 class="font-serif fw-bold mb-2 text-dark">Konfirmasi Keluar</h4>
            <p class="text-muted mb-4 small">Apakah Anda yakin ingin mengakhiri sesi dan keluar?</p>
            <div class="d-flex justify-content-center gap-3">
                <button class="btn btn-outline-secondary px-4 rounded-3" @click="showLogoutModal = false">Batal</button>
                <a href="?action=logout-confirmed" class="btn btn-gold px-4 rounded-3 text-white" style="text-decoration:none; line-height:2.4;">Ya, Keluar</a>
            </div>
        </div>
    </div>
</transition>