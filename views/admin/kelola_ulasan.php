<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->requireRole('Admin, Pegawai');

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_ulasan.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan - Oemah Keboen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin-style.css">

    <style>
        .review-img-admin {
            width: 40px; height: 40px;
            object-fit: cover; border-radius: 8px;
            cursor: pointer; transition: 0.2s; border: 1px solid 

        }
        .review-img-admin:hover { transform: scale(1.1); }
        .star-filled { color: #FFC107; }
        .star-empty { color: #DEE2E6; }
        .status-badge-review {
            padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1e7dd; color: #198754; }

        .avatar-circle {
            width: 40px; height: 40px;
            background: var(--green-light);
            color: var(--green-main);
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: bold;
        }

        .filter-btn { background: transparent; border: 1px solid var(--green-main); color: var(--green-main); border-radius: 20px; padding: 6px 16px; font-size: 0.9rem; font-weight: 500; transition: 0.2s; }
        .filter-btn.active, .filter-btn:hover { background: var(--green-main); color: white; }

        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: none; transition: 0.2s; }
        .btn-check-dp { background: #d1e7dd; color: #198754; } .btn-check-dp:hover { background: #198754; color: white; }
        .btn-cancel { background: #f8d7da; color: #dc3545; } .btn-cancel:hover { background: #dc3545; color: white; }

        .fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
        .list-enter-active, .list-leave-active { transition: all 0.4s ease; }

        .lightbox-modal {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0, 0, 0, 0.85); z-index: 9999;
            display: flex; justify-content: center; align-items: center; padding: 20px;
        }
        .lightbox-content {
            max-width: 90%; max-height: 90%; object-fit: contain;
            border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .lightbox-close {
            position: absolute; top: 20px; right: 30px;
            color: white; font-size: 2rem; cursor: pointer; transition: 0.2s;
        }
        .lightbox-close:hover { color: #C1A570; transform: scale(1.1); }
    </style>
</head>
<body>

<div id="app">
    <?php include 'sidebar.php'; ?>

    <div class="main-content" id="mainContent" :class="{'expanded': isSidebarCollapsed}">
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
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Moderasi Ulasan</h3>
                            <p class="text-muted m-0">Saring dan tanggapi masukan dari pengunjung Oemah Keboen.</p>
                        </div>
                        <div class="search-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari nama pengulas...">
                        </div>
                    </div>

                    <div class="table-custom">
                        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
                            <button class="filter-btn text-nowrap" :class="{ active: ratingFilter === 'Semua' }" @click="ratingFilter = 'Semua'">Semua</button>
                            <button class="filter-btn text-nowrap" :class="{ active: ratingFilter === 'Pending' }" @click="ratingFilter = 'Pending'">Perlu Review</button>
                            <button class="filter-btn text-nowrap" :class="{ active: ratingFilter === 5 }" @click="ratingFilter = 5">⭐️ 5</button>
                            <button class="filter-btn text-nowrap" :class="{ active: ratingFilter === 1 }" @click="ratingFilter = 1">⭐️ 1</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Pengunjung</th>
                                        <th>Rating</th>
                                        <th>Komentar</th>
                                        <th>Foto Bukti</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <transition-group name="list" tag="tbody">
                                    <tr v-for="(rev, index) in paginatedReviews" :key="rev.id" :style="{ transitionDelay: (index * 0.05) + 's' }">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-circle">{{ rev.nama.charAt(0).toUpperCase() }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ rev.nama }}</div>
                                                    <small class="text-muted">{{ rev.tanggal_format }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <i v-for="i in 5" :key="i" class="bi bi-star-fill" :class="i <= rev.rating ? 'star-filled' : 'star-empty'"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;" :title="rev.komentar">
                                                {{ rev.komentar }}
                                            </div>
                                        </td>
                                        <td>
                                            <div v-if="rev.foto && rev.foto.length > 0" class="d-flex align-items-center gap-1">
                                                <img :src="rev.foto[0]" class="review-img-admin" @click="viewImage(rev.foto[0])">
                                                <span v-if="rev.foto.length > 1" class="badge bg-secondary small">+{{ rev.foto.length - 1 }}</span>
                                            </div>
                                            <span v-else class="text-muted small">- Tidak ada -</span>
                                        </td>
                                        <td>
                                            <span class="status-badge-review" :class="rev.status === 'Approved' ? 'status-approved' : 'status-pending'">
                                                {{ rev.status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button v-if="rev.status === 'Pending'" class="action-btn btn-check-dp" title="Setujui" @click="openConfirm('approve', rev.id)">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="action-btn btn-outline-secondary border" title="Lihat Detail" @click="openDetail(rev)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="action-btn btn-cancel" title="Hapus" @click="openConfirm('delete', rev.id)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </transition-group>
                            </table>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" v-if="filteredReviews.length > 0">
                                <small class="text-muted fw-medium">
                                    Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredReviews.length) }} dari {{ filteredReviews.length }} ulasan
                                </small>
                                <div class="d-flex align-items-center gap-3">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" :disabled="currentPage === 1" @click="currentPage--">
                                        <i class="bi bi-chevron-left small me-1"></i> Prev
                                    </button>
                                    <span class="text-muted small fw-bold">
                                        Halaman <span class="text-dark">{{ currentPage }}</span> dari {{ totalPages }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" :disabled="currentPage === totalPages" @click="currentPage++">
                                        Next <i class="bi bi-chevron-right small ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <div v-if="filteredReviews.length === 0" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="bi bi-chat-square-heart fs-1"></i></div>
                                <h5 class="fw-bold">Data Tidak Ditemukan</h5>
                                <p class="small">Tidak ada ulasan yang sesuai dengan filter/pencarian Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showModal" style="z-index: 1100;">
                    <div class="modal-box modal-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">Detail Ulasan</h4>
                            <button class="btn-close" @click="showModal = false"></button>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle fs-4" style="width: 50px; height: 50px;">{{ activeReview.nama.charAt(0).toUpperCase() }}</div>
                                <div>
                                    <span class="fw-bold fs-5 text-dark">{{ activeReview.nama }}</span>
                                    <div class="small">
                                        <i v-for="i in 5" :key="i" class="bi bi-star-fill fs-6" :class="i <= activeReview.rating ? 'star-filled' : 'star-empty'"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small mb-3"><i class="bi bi-clock me-1"></i> {{ activeReview.tanggal_format }}</p>
                            <div class="p-3 bg-light rounded border" style="font-style: italic;">
                                "{{ activeReview.komentar }}"
                            </div>
                        </div>

                        <div class="mb-4 mt-4" v-if="activeReview.foto && activeReview.foto.length > 0">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Foto Lampiran</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <img v-for="(imgUrl, i) in activeReview.foto" :key="i" :src="imgUrl" 
                                     style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; cursor: zoom-in; border: 1px solid #ddd;" 
                                     @click="viewImage(imgUrl)">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showModal = false">Tutup</button>
                            <button v-if="activeReview.status === 'Pending'" class="btn btn-gold px-4 rounded-3" @click="openConfirm('approve', activeReview.id)">Setujui & Tayangkan</button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirm" style="z-index: 1200; background: rgba(0,0,0,0.6);">
                    <div class="modal-box text-center shadow-lg" style="max-width: 350px;">
                        <i class="bi fs-1 mb-3 d-block" :class="confirmData.action === 'delete' ? 'bi-trash text-danger' : 'bi-check-circle text-success'"></i>
                        <h5 class="fw-bold font-serif">{{ confirmData.title }}</h5>
                        <p class="text-muted small">{{ confirmData.message }}</p>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button class="btn btn-outline-secondary flex-grow-1 rounded-3" @click="showConfirm = false">Batal</button>
                            <button class="btn text-white flex-grow-1 rounded-3" :class="confirmData.action === 'delete' ? 'btn-danger' : 'btn-success'" @click="executeAction">
                                {{ confirmData.btnText }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <transition name="fade">
            <div v-if="lightbox.show" class="lightbox-modal" @click="lightbox.show = false">
                <i class="bi bi-x-circle lightbox-close"></i>
                <img :src="lightbox.imageUrl" class="lightbox-content" @click.stop>
            </div>
        </transition>

        <div class="admin-footer text-center py-4 border-top mt-auto">
            <small class="text-muted">© 2026 Oemah Keboen Samarinda | Review Moderation System v1.6</small>
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

                searchQuery: '',
                ratingFilter: 'Semua',

                showModal: false,
                showConfirm: false,
                activeReview: {},

                confirmData: { action: '', id: null, title: '', message: '', btnText: '' },

                lightbox: { show: false, imageUrl: '' },

                reviews: [], 

                currentPage: 1,
                itemsPerPage: 10,

                toast: { show: false, message: '', type: 'success', icon: 'bi-check-circle' }
            }
        },
        computed: {
            filteredReviews() {
                let res = this.reviews;

                if(this.ratingFilter === 'Pending') {
                    res = res.filter(r => r.status === 'Pending');
                } else if(this.ratingFilter !== 'Semua') {
                    res = res.filter(r => r.rating === this.ratingFilter);
                }

                if(this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    res = res.filter(r => r.nama.toLowerCase().includes(q));
                }

                return res;
            },
            totalPages() { return Math.ceil(this.filteredReviews.length / this.itemsPerPage) || 1; },
            paginatedReviews() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredReviews.slice(start, start + this.itemsPerPage);
            }
        },
        watch: {
            searchQuery() { this.currentPage = 1; },
            ratingFilter() { this.currentPage = 1; }
        },
        methods: {
            showToastMsg(message, type = 'success') {
                this.toast.message = message; this.toast.type = type;
                this.toast.icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },
            async fetchReviews() {
                try {
                    const response = await fetch('../../controllers/UlasanController.php?action=read_all');
                    const data = await response.json();

                    if(data.status === 'error') {
                        this.showToastMsg(data.message, 'error'); return;
                    }

                    this.reviews = data; 
                } catch (e) {
                    this.showToastMsg("Gagal memuat data ulasan.", "error");
                }
            },
            openDetail(rev) {
                this.activeReview = rev;
                this.showModal = true;
            },

            viewImage(imgUrl) {
                this.lightbox.imageUrl = imgUrl;
                this.lightbox.show = true;
            },
            openConfirm(actionType, id) {
                this.confirmData.action = actionType;
                this.confirmData.id = id;

                if (actionType === 'approve') {
                    this.confirmData.title = 'Setujui Ulasan?';
                    this.confirmData.message = 'Ulasan ini akan ditampilkan secara publik di website pengunjung.';
                    this.confirmData.btnText = 'Ya, Setujui';
                } else {
                    this.confirmData.title = 'Hapus Ulasan?';
                    this.confirmData.message = 'Ulasan beserta semua foto lampirannya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.';
                    this.confirmData.btnText = 'Ya, Hapus';
                }

                this.showConfirm = true;
            },
            async executeAction() {
                try {
                    const payload = { 
                        action: this.confirmData.action, 
                        id: this.confirmData.id 
                    };

                    const response = await fetch('../../controllers/UlasanController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        this.showConfirm = false;
                        this.showModal = false;
                        this.fetchReviews();
                        this.showToastMsg(result.message, 'success');
                    } else {
                        this.showToastMsg(result.message || "Aksi gagal.", 'error');
                    }
                } catch (e) {
                    this.showToastMsg("Koneksi server bermasalah.", "error");
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
            this.fetchReviews();

            const btn = document.getElementById('sidebarToggle');
            if (btn) {
                btn.addEventListener('click', this.toggleSidebar);
            }

            document.querySelectorAll('.nav-sidebar .nav-link').forEach(link => {
                link.addEventListener('click', this.closeSidebarMobile);
            });

            window.addEventListener('resize', this.handleResize);
            this.handleResize(); 

            setTimeout(() => { this.isLoaded = true; }, 100);
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