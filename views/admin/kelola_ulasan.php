<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->checkAuth(); 

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
            width: 60px; height: 60px;
            object-fit: cover; border-radius: 8px;
            cursor: pointer; transition: 0.2s;
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

        .fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
    </style>
</head>
<body>

<div id="app">
    <?php include 'sidebar.php'; ?>

    <div class="main-content" id="mainContent" :class="{'expanded': isSidebarCollapsed}">
        <?php include 'topbar.php'; ?>

        <div class="content-wrapper">
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
                            <button class="filter-btn" :class="{ active: ratingFilter === 'Semua' }" @click="ratingFilter = 'Semua'">Semua</button>
                            <button class="filter-btn" :class="{ active: ratingFilter === 'Pending' }" @click="ratingFilter = 'Pending'">Perlu Review</button>
                            <button class="filter-btn" :class="{ active: ratingFilter === 5 }" @click="ratingFilter = 5">⭐️ 5</button>
                            <button class="filter-btn" :class="{ active: ratingFilter === 1 }" @click="ratingFilter = 1">⭐️ 1</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Pengunjung</th>
                                        <th>Rating</th>
                                        <th>Komentar</th>
                                        <th>Foto</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <transition-group name="list" tag="tbody">
                                    <tr v-for="rev in filteredReviews" :key="rev.id">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-circle">{{ rev.nama.charAt(0) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ rev.nama }}</div>
                                                    <small class="text-muted">{{ rev.tanggal }}</small>
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
                                            <img v-if="rev.foto" :src="rev.foto" class="review-img-admin" @click="viewImage(rev.foto)">
                                            <span v-else class="text-muted small">No Photo</span>
                                        </td>
                                        <td>
                                            <span class="status-badge-review" :class="rev.status === 'Approved' ? 'status-approved' : 'status-pending'">
                                                {{ rev.status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button v-if="rev.status === 'Pending'" class="action-btn btn-check-dp" title="Setujui" @click="approveReview(rev.id)">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="action-btn btn-outline-secondary border" title="Lihat Detail" @click="openDetail(rev)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="action-btn btn-cancel" title="Hapus" @click="openConfirmDelete(rev.id)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </transition-group>
                            </table>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showModal" style="z-index: 1100;">
                    <div class="modal-box">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0">Detail Ulasan</h4>
                            <button class="btn-close" @click="showModal = false"></button>
                        </div>
                        <div class="mb-4 text-center" v-if="activeReview.foto">
                            <img :src="activeReview.foto" style="width: 100%; max-height: 250px; object-fit: contain; border-radius: 15px;">
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">{{ activeReview.nama }}</span>
                                <div class="small">
                                    <i v-for="i in 5" :key="i" class="bi bi-star-fill" :class="i <= activeReview.rating ? 'star-filled' : 'star-empty'"></i>
                                </div>
                            </div>
                            <p class="text-muted small mb-3">{{ activeReview.tanggal }}</p>
                            <div class="p-3 bg-light rounded" style="font-style: italic;">
                                "{{ activeReview.komentar }}"
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button class="btn btn-outline-secondary px-4" @click="showModal = false">Tutup</button>
                            <button v-if="activeReview.status === 'Pending'" class="btn btn-gold px-4" @click="approveReview(activeReview.id)">Setujui & Tampilkan</button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirm" style="z-index: 1200; background: rgba(0,0,0,0.6);">
                    <div class="modal-box text-center" style="max-width: 350px;">
                        <i class="bi bi-trash text-danger fs-1 mb-3 d-block"></i>
                        <h5 class="fw-bold">Hapus Ulasan?</h5>
                        <p class="text-muted small">Tindakan ini tidak bisa dibatalkan.</p>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button class="btn btn-outline-secondary flex-grow-1" @click="showConfirm = false">Batal</button>
                            <button class="btn btn-danger flex-grow-1" @click="executeDelete">Hapus</button>
                        </div>
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
                showLogoutModal: false, 

                searchQuery: '',
                ratingFilter: 'Semua',
                showModal: false,
                showConfirm: false,
                activeReview: {},
                selectedId: null,
                reviews: [
                    { id: 1, nama: 'Aulia Rahma', rating: 5, komentar: 'Tempatnya asri banget, jambunya manis-manis. Cocok buat bawa anak kecil edukasi.', foto: '../../assets/img/tentang1.jpg', tanggal: '05 April 2026', status: 'Pending' },
                    { id: 2, nama: 'Budi Santoso', rating: 4, komentar: 'Fasilitas pendoponya nyaman, tapi sayang kemarin pas datang lagi ramai banget.', foto: null, tanggal: '04 April 2026', status: 'Approved' },
                    { id: 3, nama: 'Siti Aminah', rating: 5, komentar: 'Pelayanannya ramah, proses reservasi lewat web juga gampang.', foto: '../../assets/img/tentang2.jpg', tanggal: '02 April 2026', status: 'Approved' },
                    { id: 4, nama: 'Agus Pratama', rating: 1, komentar: 'Datang jauh-jauh tapi buahnya lagi habis. Kecewa banget.', foto: null, tanggal: '01 April 2026', status: 'Pending' }
                ]
            }
        },
        computed: {
            filteredReviews() {
                return this.reviews.filter(r => {
                    const matchSearch = r.nama.toLowerCase().includes(this.searchQuery.toLowerCase());
                    let matchRating = true;
                    if(this.ratingFilter === 'Pending') matchRating = r.status === 'Pending';
                    else if(this.ratingFilter !== 'Semua') matchRating = r.rating === this.ratingFilter;
                    return matchSearch && matchRating;
                });
            }
        },
        methods: {
            approveReview(id) {
                const r = this.reviews.find(rev => rev.id === id);
                if(r) r.status = 'Approved';
                this.showModal = false;
            },
            openDetail(rev) {
                this.activeReview = rev;
                this.showModal = true;
            },
            openConfirmDelete(id) {
                this.selectedId = id;
                this.showConfirm = true;
            },
            executeDelete() {
                this.reviews = this.reviews.filter(r => r.id !== this.selectedId);
                this.showConfirm = false;
            },
            viewImage(img) {
                window.open(img, '_blank');
            }
        },
        mounted() {

            const btn = document.getElementById('sidebarToggle');
            if(btn) btn.addEventListener('click', () => { 
                this.isSidebarCollapsed = !this.isSidebarCollapsed;
                document.getElementById('sidebar').classList.toggle('collapsed');
            });

            setTimeout(() => { this.isLoaded = true; }, 100);
        }
    }).mount('#app');
</script>
</body>
</html>