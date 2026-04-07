<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_produk.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Oemah Keboen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="admin-style.css">

    <style>
        .filter-btn { background: transparent; border: 1px solid var(--green-main); color: var(--green-main); border-radius: 20px; padding: 6px 16px; font-size: 0.9rem; font-weight: 500; transition: 0.2s; }
        .filter-btn.active, .filter-btn:hover { background: var(--green-main); color: white; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: none; transition: 0.2s; }
        .btn-edit { background: #e2e3e5; color: #495057; } .btn-edit:hover { background: #6c757d; color: white; }
        .btn-toggle-status { background: #fff3cd; color: #856404; } .btn-toggle-status:hover { background: #ffc107; color: white; }

        .prod-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(95, 122, 86, 0.2);
        }

        .fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
        .list-enter-active, .list-leave-active { transition: all 0.2s ease; }
        .list-enter-from, .list-leave-to { opacity: 0; transform: translateY(10px); }
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
                    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Manajemen Produk</h3>
                            <p class="text-muted m-0">Kelola daftar buah, makanan, minuman, dan paket edukasi.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari produk...">
                            </div>
                            <button class="btn-gold shadow-sm text-nowrap" @click="openAdd">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="table-custom" v-show="isLoaded">
                    <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
                        <button class="filter-btn" :class="{ active: currentFilter === 'Semua' }" @click="currentFilter = 'Semua'">Semua</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Buah' }" @click="currentFilter = 'Buah'">Buah-buahan</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Minuman' }" @click="currentFilter = 'Minuman'">Minuman</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Paket Edukasi' }" @click="currentFilter = 'Paket Edukasi'">Paket Edukasi</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless mt-2 text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th><i class="bi bi-tag me-1"></i> Nama Produk</th>
                                    <th>Kategori</th>
                                    <th><i class="bi bi-cash me-1"></i> Harga</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <transition-group name="list" tag="tbody">
                                <tr v-for="prod in filteredProducts" :key="prod.id">
                                    <td>
                                        <img :src="prod.image || '../../assets/img/logo.png'" alt="Thumb" class="prod-thumb">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ prod.nama }}</div>
                                        <small class="text-muted">ID: PROD-{{ prod.id.toString().padStart(3, '0') }}</small>
                                    </td>
                                    <td>{{ prod.kategori }}</td>
                                    <td class="fw-bold" style="color: var(--green-main);">{{ formatRupiah(prod.harga) }}</td>
                                    <td>
                                        <span class="badge-status" 
                                              :class="prod.status == 'Tersedia' ? 'badge-lunas' : 'badge-menunggu'">
                                            {{ prod.status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="action-btn btn-toggle-status" title="Ubah Status" @click="toggleStatus(prod.id)">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <button class="action-btn btn-edit" title="Edit" @click="openEdit(prod)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn btn-outline-danger border" style="color: #dc3545;" title="Hapus" @click="openConfirm('hapus', prod.id)">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>

                        <div v-if="filteredProducts.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 mb-2 d-block"></i>
                            Tidak ada produk yang ditemukan.
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showFormModal" style="z-index: 1070;">
                    <div class="modal-box modal-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">
                                {{ isAddMode ? 'Tambah Produk Baru' : 'Edit Produk' }} 
                                <span v-if="!isAddMode" class="text-muted small">

                            </h4>
                            <button class="btn-close" @click="showFormModal = false"></button>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">NAMA PRODUK</label>
                                        <input type="text" class="form-control" v-model="activeProd.nama">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">KATEGORI</label>
                                        <select class="form-select" v-model="activeProd.kategori">
                                            <option value="Buah">Buah</option>
                                            <option value="Minuman">Minuman</option>
                                            <option value="Paket Edukasi">Paket Edukasi</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">HARGA (RP)</label>
                                        <input type="number" class="form-control" v-model="activeProd.harga">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">DESKRIPSI</label>
                                        <textarea class="form-control" rows="3" v-model="activeProd.deskripsi"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">FOTO PRODUK</label>
                                <div class="border rounded-4 text-center p-3 mb-3 bg-light d-flex flex-column justify-content-center" style="min-height: 160px;">
                                    <i class="bi bi-image fs-1 text-muted mb-2"></i>
                                    <button class="btn btn-sm btn-outline-secondary">Pilih File</button>
                                </div>
                                <label class="form-label small fw-bold text-muted">STATUS STOK</label>
                                <select class="form-select" v-model="activeProd.status">
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Habis">Habis</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showFormModal = false">Batal</button>
                            <button v-if="isAddMode" class="btn btn-gold px-4 rounded-3" @click="saveNew">Simpan Produk</button>
                            <button v-else class="btn btn-gold px-4 rounded-3" @click="saveEdit">Update Perubahan</button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirmModal" style="z-index: 2050; background: rgba(0,0,0,0.7);">
                    <div class="modal-box text-center shadow-lg" style="max-width: 400px;">
                        <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                        <h4 class="font-serif fw-bold mb-2">Hapus Produk?</h4>
                        <p class="text-muted mb-4">Tindakan ini tidak bisa dibatalkan secara permanen.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Batal</button>
                            <button class="btn btn-danger px-4 rounded-3" @click="executeConfirm">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showAlert" style="z-index: 3000;">
                    <div class="modal-box text-center" style="max-width: 350px;">
                        <i class="bi bi-exclamation-triangle text-warning mb-2" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold mt-2">Peringatan</h5>
                        <p class="text-muted">{{ alertMessage }}</p>
                        <button class="btn btn-gold w-100" @click="showAlert = false">Mengerti</button>
                    </div>
                </div>
            </transition>

        </div> <div class="admin-footer">
            &copy; 2026 Oemah Keboen | Inventory Management System v1.2
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

                currentFilter: 'Semua',
                searchQuery: '',

                showFormModal: false,
                isAddMode: false,
                activeProd: {}, 

                showConfirmModal: false,
                pendingDeleteId: null,
                showAlert: false,
                alertMessage: '',

                products: [
                    { id: 1, nama: 'Jambu Kristal Super', kategori: 'Buah', harga: 25000, status: 'Tersedia', deskripsi: 'Jambu organik.', image: '../../assets/img/produk1.jpg' },
                    { id: 2, nama: 'Little Gardener Kit', kategori: 'Paket Edukasi', harga: 45000, status: 'Tersedia', deskripsi: 'Paket edukasi.', image: '../../assets/img/produk2.jpg' },
                    { id: 3, nama: 'Jus Jambu Kristal', kategori: 'Minuman', harga: 15000, status: 'Habis', deskripsi: 'Segar alami.', image: '' }
                ]
            }
        },
        computed: {
            filteredProducts() {
                let res = this.products;
                if (this.currentFilter !== 'Semua') {
                    res = res.filter(p => p.kategori === this.currentFilter);
                }
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    res = res.filter(p => p.nama.toLowerCase().includes(q));
                }
                return res;
            }
        },
        methods: {
            formatRupiah(n) { 
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n); 
            },
            toggleStatus(id) {
                const i = this.products.findIndex(p => p.id === id);
                if (i !== -1) {
                    this.products[i].status = this.products[i].status === 'Tersedia' ? 'Habis' : 'Tersedia';
                }
            },
            openAdd() {
                this.isAddMode = true;
                this.activeProd = { id: null, nama: '', kategori: 'Buah', harga: '', status: 'Tersedia', deskripsi: '', image: '' };
                this.showFormModal = true;
            },
            saveNew() {
                if (!this.activeProd.nama || !this.activeProd.harga) { 
                    this.alertMessage = "Mohon lengkapi Nama dan Harga produk!"; 
                    this.showAlert = true; 
                    return; 
                }
                const maxId = this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) : 0;
                this.activeProd.id = maxId + 1;
                this.products.unshift({...this.activeProd});
                this.showFormModal = false;
            },
            openEdit(prod) {
                this.isAddMode = false;
                this.activeProd = JSON.parse(JSON.stringify(prod)); 
                this.showFormModal = true;
            },
            saveEdit() {
                if (!this.activeProd.nama || !this.activeProd.harga) {
                    this.alertMessage = "Nama dan Harga tidak boleh kosong.";
                    this.showAlert = true;
                    return;
                }
                const i = this.products.findIndex(p => p.id === this.activeProd.id);
                if(i !== -1) this.products[i] = this.activeProd;
                this.showFormModal = false;
            },
            openConfirm(type, id) {
                if (type === 'hapus') {
                    this.pendingDeleteId = id;
                    this.showConfirmModal = true;
                }
            },
            executeConfirm() {
                if (this.pendingDeleteId) {
                    this.products = this.products.filter(p => p.id !== this.pendingDeleteId);
                }
                this.showConfirmModal = false;
            }
        },
        mounted() {

            const btn = document.getElementById('sidebarToggle');
            if(btn) btn.addEventListener('click', () => { 
                this.isSidebarCollapsed = !this.isSidebarCollapsed;
                document.getElementById('sidebar').classList.toggle('collapsed');
            });

            setTimeout(() => { this.isLoaded = true; }, 200);
        }
    }).mount('#app');
</script>

</body>
</html>