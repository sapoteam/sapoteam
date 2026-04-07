<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

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
</head>
<body>

<div id="app">
    <?php include 'sidebar.php'; ?>

    <div class="main-content" :class="{'expanded': isSidebarCollapsed}">

        <?php include 'topbar.php'; ?>

        <div class="content-wrapper">
            <transition name="fade" appear>
                <div v-show="isLoaded">
                    <div class="mb-4">
                        <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Ringkasan Sistem</h3>
                        <p class="text-muted">Pantau aktivitas Oemah Keboen hari ini.</p>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="row g-4 mb-5" v-show="isLoaded">
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                            <div>
                                <h3>{{ totalReservasi }}</h3>
                                <span>Reservasi<br>Bulan Ini</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-chat-square-text"></i></div>
                            <div>
                                <h3>{{ ulasanMenunggu }}</h3>
                                <span>Ulasan<br>Perlu Review</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon"><i class="bi bi-box"></i></div>
                            <div>
                                <h3>{{ produkAktif }}</h3>
                                <span>Produk<br>Aktif</span>
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
                                    <th><i class="bi bi-pencil-square me-2"></i> Status DP</th>
                                </tr>
                            </thead>
                            <transition-group name="list" tag="tbody">
                                <tr v-for="(res, index) in recentReservations" :key="res.id" :style="{ transitionDelay: (index * 0.1) + 's' }">
                                    <td>{{ res.tanggal }}</td>
                                    <td class="fw-medium">{{ res.nama }}</td>
                                    <td>{{ res.lokasi }}</td>
                                    <td>
                                        <span class="badge-status" :class="res.statusDP == 'Lunas' ? 'badge-lunas' : 'badge-menunggu'">
                                            {{ res.statusDP }}
                                        </span>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>
                    </div>
                </div>
            </transition>
        </div> <div class="admin-footer">
            &copy; 2026 Oemah Keboen | Sistem Manajemen Internal v1.0
        </div>
    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                isLoaded: false,
                isSidebarCollapsed: false, 

                showLogoutModal: false,    

                totalReservasi: 14,
                ulasanMenunggu: 3,
                produkAktif: 8,
                recentReservations: [
                    { id: 1, tanggal: '20 April 2026', nama: 'Keluarga Bpk. Budi', lokasi: 'Pendopo', statusDP: 'Menunggu Review' },
                    { id: 2, tanggal: '22 April 2026', nama: 'Komunitas Gowes', lokasi: 'Gazebo', statusDP: 'Lunas' },
                    { id: 3, tanggal: '25 April 2026', nama: 'TK Harapan Bangsa', lokasi: 'Halaman Depan', statusDP: 'Menunggu Review' }
                ]
            }
        },
        methods: {

            handleSidebar() {
                this.isSidebarCollapsed = !this.isSidebarCollapsed;
            }
        },
        mounted() {

            const btn = document.getElementById('sidebarToggle');
            if(btn) {
                btn.addEventListener('click', () => {
                    this.handleSidebar();

                    document.getElementById('sidebar').classList.toggle('collapsed');
                });
            }

            setTimeout(() => {
                this.isLoaded = true;
            }, 100);
        }
    }).mount('#app');
</script>

</body>
</html>