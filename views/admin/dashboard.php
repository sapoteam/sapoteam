<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->requireRole('Admin, Pegawai'); 

$admin_name = $_SESSION['admin_name'];
$current_page = 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Oemah Keboen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
        .fade-enter-from, .fade-leave-to { opacity: 0; }
        .list-enter-active, .list-leave-active { transition: all 0.3s ease; }
    </style>
</head>
<body>

<div id="app">
    <?php include 'sidebar.php'; ?>

    <div class="main-content" :class="{'expanded': isSidebarCollapsed}">

        <?php include 'topbar.php'; ?>

        <div class="content-wrapper">
            <transition name="toast-slide">
                <div v-if="toast.show" class="toast-custom" :class="'toast-' + toast.type">
                    <i class="bi fs-5" :class="toast.icon"></i>
                    {{ toast.message }}
                </div>
            </transition>

            <transition name="fade" appear>
                <div v-show="isLoaded">
                    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Ringkasan Sistem</h3>
                            <p class="text-muted m-0">Pantau aktivitas Oemah Keboen hari ini.</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm border d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold m-0"><i class="bi bi-tree-fill text-success me-2"></i>Status Musim Panen</h5>
                                <small class="text-muted">Buka atau tutup informasi pengunjung untuk kegiatan memetik buah di kebun.</small>
                            </div>
                            <div class="form-check form-switch fs-3 m-0 d-flex align-items-center">
                                <input class="form-check-input mt-0" type="checkbox" role="switch" id="panenSwitch" v-model="isPanenActive" @change="updateStatusPanen" style="cursor: pointer;">
                                <label class="form-check-label fs-6 ms-3 fw-bold" :class="isPanenActive ? 'text-success' : 'text-danger'" for="panenSwitch" style="cursor: pointer;">
                                    {{ isPanenActive ? 'DIBUKA (Sedang Panen)' : 'DITUTUP (Belum Musim)' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="row g-4 mb-5" v-show="isLoaded">
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                            <div>
                                <h3>{{ stats.totalReservasi }}</h3>
                                <span>Reservasi<br>Bulan Ini</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-chat-square-text"></i></div>
                            <div>
                                <h3>{{ stats.ulasanMenunggu }}</h3>
                                <span>Ulasan<br>Perlu Review</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-box"></i></div>
                            <div>
                                <h3>{{ stats.produkAktif }}</h3>
                                <span>Produk<br>Tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="table-custom" v-show="isLoaded">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-serif fw-bold m-0">Reservasi Menunggu Konfirmasi</h4>
                        <a href="kelola_reservasi.php" class="btn-gold" style="text-decoration: none;">Lihat Semua</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless mt-3 text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-calendar2-event me-2"></i> Tgl Booking</th>
                                    <th><i class="bi bi-person me-2"></i> Nama Pemesan</th>
                                    <th><i class="bi bi-geo-alt me-2"></i> Lokasi</th>
                                    <th><i class="bi bi-pencil-square me-2"></i> Status</th>
                                </tr>
                            </thead>
                            <transition-group name="list" tag="tbody">
                                <tr v-for="(res, index) in recentReservations" :key="res.id" :style="{ transitionDelay: (index * 0.1) + 's' }">
                                    <td>{{ res.tanggal_format }}</td>
                                    <td class="fw-medium">{{ res.nama }}</td>
                                    <td>{{ res.lokasi_nama || 'Area Terhapus' }}</td>
                                    <td>
                                        <span class="badge-status badge-menunggu">
                                            {{ res.status }}
                                        </span>
                                    </td>
                                </tr>
                            </transition-group>
                            <tbody v-if="recentReservations.length === 0">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        Tidak ada reservasi yang menunggu konfirmasi saat ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </transition>
        </div> 

        <div class="admin-footer">
            &copy; 2026 Oemah Keboen | Sistem Manajemen Internal v1.0
        </div>
    </div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                isLoaded: false,
                isSidebarCollapsed: false,
                isSidebarMobileOpen: false,
                showLogoutModal: false,

                isPanenActive: false,
                stats: {
                    totalReservasi: 0,
                    ulasanMenunggu: 0,
                    produkAktif: 0
                },
                recentReservations: [],

                toast: { show: false, message: '', type: 'success', icon: 'bi-check-circle' }
            }
        },
        methods: {
            showToastMsg(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },

            async fetchDashboardData() {
                try {
                    const response = await fetch('../../controllers/DashboardController.php?action=get_data');
                    const result = await response.json();

                    if (result.status === 'success') {
                        this.stats.totalReservasi = result.data.totalReservasi;
                        this.stats.ulasanMenunggu = result.data.ulasanMenunggu;
                        this.stats.produkAktif = result.data.produkAktif;
                        this.isPanenActive = result.data.statusPanen;
                        this.recentReservations = result.data.recentReservations;
                    }
                } catch (e) {
                    console.error("Gagal memuat data dashboard.");
                }
            },

            async updateStatusPanen() {
                try {
                    const payload = {
                        action: 'toggle_panen',
                        statusPanen: this.isPanenActive
                    };

                    const response = await fetch('../../controllers/DashboardController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    if (result.status === 'success') {
                        this.showToastMsg(result.message, 'success');
                    } else {
                        this.isPanenActive = !this.isPanenActive;
                        this.showToastMsg(result.message, 'error');
                    }
                } catch (e) {
                    this.isPanenActive = !this.isPanenActive;
                    this.showToastMsg("Koneksi server bermasalah.", 'error');
                }
            },

            toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar) return;

                if (window.innerWidth <= 768) {
                    this.isSidebarMobileOpen = !this.isSidebarMobileOpen;
                    sidebar.classList.toggle('mobile-open', this.isSidebarMobileOpen);
                    sidebar.classList.remove('collapsed');
                } else {
                    this.isSidebarCollapsed = !this.isSidebarCollapsed;
                    sidebar.classList.toggle('collapsed', this.isSidebarCollapsed);
                }
            },

            closeSidebarMobile() {
                if (window.innerWidth <= 768) {
                    this.isSidebarMobileOpen = false;
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) sidebar.classList.remove('mobile-open');
                }
            },

            handleResize() {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar) return;

                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('collapsed');
                    if (!this.isSidebarMobileOpen) {
                        sidebar.classList.remove('mobile-open');
                    }
                } else {
                    this.isSidebarMobileOpen = false;
                    sidebar.classList.remove('mobile-open');
                    sidebar.classList.toggle('collapsed', this.isSidebarCollapsed);
                }
            }
        },
        mounted() {
            this.fetchDashboardData();

            const btn = document.getElementById('sidebarToggle');
            if (btn) {
                btn.addEventListener('click', this.toggleSidebar);
            }

            document.querySelectorAll('.nav-sidebar .nav-link').forEach(link => {
                link.addEventListener('click', this.closeSidebarMobile);
            });

            window.addEventListener('resize', this.handleResize);
            this.handleResize();

            setTimeout(() => {
                this.isLoaded = true;
            }, 100);
        },
        beforeUnmount() {
            const btn = document.getElementById('sidebarToggle');
            if (btn) {
                btn.removeEventListener('click', this.toggleSidebar);
            }

            document.querySelectorAll('.nav-sidebar .nav-link').forEach(link => {
                link.removeEventListener('click', this.closeSidebarMobile);
            });

            window.removeEventListener('resize', this.handleResize);
        }
    }).mount('#app');
</script>

</body>
</html>