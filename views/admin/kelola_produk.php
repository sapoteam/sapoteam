<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->requireRole('Admin'); 

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
        
        .upload-area { border: 2px dashed #ccc; cursor: pointer; transition: 0.3s; }
        .upload-area:hover { border-color: var(--green-main); background: #f8f9fa !important; }
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
                    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Manajemen Produk</h3>
                            <p class="text-muted m-0">Kelola daftar buah, makanan, minuman, dan paket edukasi.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari nama produk...">
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
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Semua' }" @click="currentFilter = 'Semua'">Semua</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Buah' }" @click="currentFilter = 'Buah'">Buah-buahan</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Minuman' }" @click="currentFilter = 'Minuman'">Minuman</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Paket Edukasi' }" @click="currentFilter = 'Paket Edukasi'">Paket Edukasi</button>
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
                                <tr v-for="(prod, index) in paginatedProducts" :key="prod.id" :style="{ transitionDelay: (index * 0.05) + 's' }">
                                    <td>
                                        <img :src="prod.image ? prod.image : '../../assets/img/logo.png'" 
                                                @error="$event.target.src='../../assets/img/logo.png'" 
                                                alt="Thumb" class="prod-thumb" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ prod.nama }}</div>
                                        <small class="text-muted">ID: PROD-{{ prod.id.toString().padStart(3, '0') }}</small>
                                    </td>
                                    <td>{{ prod.kategori }}</td>
                                    <td class="fw-bold" style="color: var(--green-main);">{{ formatRupiah(prod.harga) }}</td>
                                    <td>
                                        <span class="badge-status" :class="prod.status == 'Tersedia' ? 'badge-lunas' : 'badge-menunggu'">
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
                                            <button class="action-btn btn-outline-danger border" style="color: #dc3545;" title="Hapus" @click="openConfirm(prod.id)">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" v-if="filteredProducts.length > 0">
                            <small class="text-muted fw-medium">
                                Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} dari {{ filteredProducts.length }} data
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

                        <div v-if="filteredProducts.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 mb-2 d-block"></i>
                            <h5 class="fw-bold">Produk Tidak Ditemukan</h5>
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
                                
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="d-none">
                                <div class="upload-area rounded-4 text-center p-3 mb-3 bg-light d-flex flex-column justify-content-center align-items-center" style="min-height: 160px;" @click="$refs.fileInput.click()">
                                    <img v-if="activeProd.previewImage" :src="activeProd.previewImage" style="max-height: 130px; object-fit: contain; border-radius: 8px;">
                                    <div v-else>
                                        <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-2 d-block"></i>
                                        <span class="btn btn-sm btn-outline-secondary">Pilih Gambar</span>
                                    </div>
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
                            <button class="btn btn-gold px-4 rounded-3" @click="saveProduct">
                                {{ isAddMode ? 'Simpan Produk' : 'Update Perubahan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirmModal" style="z-index: 2050; background: rgba(0,0,0,0.7);">
                    <div class="modal-box text-center shadow-lg" style="max-width: 400px;">
                        <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                        <h4 class="font-serif fw-bold mb-2">Hapus Produk?</h4>
                        <p class="text-muted mb-4">Data dan foto produk akan dihapus secara permanen.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Batal</button>
                            <button class="btn btn-danger px-4 rounded-3" @click="executeConfirm">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </transition>

        </div>
        
        <div class="admin-footer">
            &copy; 2026 Oemah Keboen Samarinda | Inventory Management System v1.2
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
                currentFilter: 'Semua',
                searchQuery: '',
                
                showFormModal: false,
                isAddMode: false,
                activeProd: {}, 
                
                showConfirmModal: false,
                pendingDeleteId: null,

                products: [], 
                currentPage: 1,
                itemsPerPage: 10,

                toast: { show: false, message: '', type: 'success', icon: 'bi-check-circle' }
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
            },
            totalPages() { return Math.ceil(this.filteredProducts.length / this.itemsPerPage) || 1; },
            paginatedProducts() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredProducts.slice(start, start + this.itemsPerPage);
            }
        },
        watch: {
            searchQuery() { this.currentPage = 1; },
            currentFilter() { this.currentPage = 1; }
        },
        methods: {
            showToastMsg(message, type = 'success') {
                this.toast.message = message; this.toast.type = type;
                this.toast.icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },
            formatRupiah(n) { 
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n); 
            },
            
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    this.showToastMsg('Yang diupload harus berupa gambar!', 'error');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.showToastMsg('Ukuran gambar maksimal 2MB!', 'error');
                    return;
                }

                this.activeProd.fileToUpload = file;
                
                this.activeProd.previewImage = URL.createObjectURL(file);
            },

            async fetchProducts() {
                try {
                    const response = await fetch('../../controllers/ProductController.php?action=read');
                    const rawText = await response.text(); 
                    try {
                        const data = JSON.parse(rawText);
                        if (data.status === 'error') { this.showToastMsg("Akses ditolak!", 'error'); return; }
                        this.products = data;
                    } catch (e) { console.error("Gagal parse JSON:", rawText); }
                } catch (e) { this.showToastMsg("Koneksi gagal!", 'error'); }
            },

            async toggleStatus(id) {
                const i = this.products.findIndex(p => p.id === id);
                if (i !== -1) {
                    const newStatus = this.products[i].status === 'Tersedia' ? 'Habis' : 'Tersedia';
                    await fetch('../../controllers/ProductController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'toggle', id: id, status: newStatus })
                    });
                    this.fetchProducts();
                    this.showToastMsg(`Status berhasil diubah!`, 'success');
                }
            },

            openAdd() {
                this.isAddMode = true;
                this.activeProd = { id: null, nama: '', kategori: 'Buah', harga: '', status: 'Tersedia', deskripsi: '', image: '', previewImage: null, fileToUpload: null };
                this.showFormModal = true;
            },

            openEdit(prod) {
                this.isAddMode = false;
                this.activeProd = JSON.parse(JSON.stringify(prod)); 
                this.activeProd.previewImage = prod.image; 
                this.activeProd.fileToUpload = null;
                this.showFormModal = true;
            },

            async saveProduct() {
                this.activeProd.nama = this.activeProd.nama ? this.activeProd.nama.trim() : '';
                this.activeProd.deskripsi = this.activeProd.deskripsi ? this.activeProd.deskripsi.trim() : '';

                const nameRegex = /^[a-zA-Z0-9\s.,-]+$/;

                if (!this.activeProd.nama || this.activeProd.nama.length < 3) {
                    this.showToastMsg("Nama produk minimal 3 karakter!", "warning"); 
                    return;
                }
                if (this.activeProd.nama.length > 50) {
                    this.showToastMsg("Nama produk maksimal 50 karakter!", "warning"); 
                    return;
                }
                if (!nameRegex.test(this.activeProd.nama)) {
                    this.showToastMsg("Nama produk tidak boleh mengandung emoji atau simbol aneh!", "error");
                    return;
                }

                if (!this.activeProd.harga || this.activeProd.harga < 1000) {
                    this.showToastMsg("Harga produk minimal Rp 1.000!", "warning"); 
                    return;
                }
                if (this.activeProd.harga > 50000000) {
                    this.showToastMsg("Harga produk tidak wajar!", "warning"); 
                    return;
                }

                if (this.activeProd.deskripsi && !nameRegex.test(this.activeProd.deskripsi)) {
                    this.showToastMsg("Deskripsi tidak boleh mengandung emoji atau simbol aneh!", "error");
                    return;
                }

                if (!this.activeProd.nama || !this.activeProd.harga) {
                    this.showToastMsg("Nama dan Harga wajib diisi!", "error"); return;
                }

                let formData = new FormData();
                formData.append('action', this.isAddMode ? 'create' : 'update');
                if (!this.isAddMode) formData.append('id', this.activeProd.id);
                
                formData.append('nama', this.activeProd.nama);
                formData.append('kategori', this.activeProd.kategori);
                formData.append('harga', this.activeProd.harga);
                formData.append('deskripsi', this.activeProd.deskripsi || '');
                formData.append('status', this.activeProd.status);

                if (this.activeProd.fileToUpload) {
                    formData.append('image', this.activeProd.fileToUpload);
                }

                try {
                    const response = await fetch('../../controllers/ProductController.php', {
                        method: 'POST',
                        body: formData 
                    });

                    const rawText = await response.text(); 
                    try {
                        const result = JSON.parse(rawText);
                        if (result.status === 'success') {
                            this.showFormModal = false;
                            this.fetchProducts(); 
                            this.showToastMsg(result.message, 'success');
                        } else {
                            this.showToastMsg(result.message || "Gagal menyimpan.", 'error');
                        }
                    } catch (e) { 
                        console.error(rawText);
                        this.showToastMsg("Terjadi kesalahan sistem dari server.", 'error'); 
                    }
                } catch (err) {
                    this.showToastMsg("Koneksi bermasalah!", 'error');
                }
            },

            openConfirm(id) {
                this.pendingDeleteId = id;
                this.showConfirmModal = true;
            },

        async executeConfirm() {
                await fetch('../../controllers/ProductController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: this.pendingDeleteId })
                });
                this.showConfirmModal = false;
                this.fetchProducts();
                this.showToastMsg("Produk berhasil dihapus!", "success");
            }
        },
        mounted() {
            this.fetchProducts();
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