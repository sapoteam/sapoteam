<?php
if (isset($_GET['action']) && $_GET['action'] == 'logout-confirmed') {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit;
}
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->requireRole('Admin, Pegawai');

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_fasilitas.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas - Oemah Keboen</title>
        <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin-style.css">

    <style>
        .facility-card-admin { background: white; border-radius: 20px; border: 1px solid var(--green-main); overflow: hidden; transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .facility-img-admin { width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid var(--green-main); }
        .card-body-fasil { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .desc-fasil { font-size: 0.9rem; color: #666; margin-bottom: 15px; flex-grow: 1; }
        .status-pill { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .status-tersedia { background: #DDE5D1; color: #5F7A56; }
        .status-perbaikan { background: #FFE08F; color: #745B0B; }
        .upload-area { border: 2px dashed #ccc; cursor: pointer; transition: 0.3s; border-radius: 12px; }
        .upload-area:hover { border-color: var(--green-main); background: #f8f9fa !important; }
        .fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
        .filter-btn { background: transparent; border: 1px solid var(--green-main); color: var(--green-main); border-radius: 20px; padding: 6px 16px; font-size: 0.9rem; font-weight: 500; transition: 0.3s; }
        .filter-btn.active, .filter-btn:hover { background: var(--green-main); color: white; }
        .input-rupiah-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    transition: 0.2s;
}
.input-rupiah-wrapper:focus-within {
    border-color: var(--green-main);
    box-shadow: 0 0 0 0.2rem rgba(95, 122, 86, 0.15);
}
.input-rupiah-wrapper span {
    padding: 8px 12px;
    background: #f8f9fa;
    color: #6c757d;
    font-weight: 600;
    border-right: 1px solid #dee2e6;
    white-space: nowrap;
}
.input-rupiah-wrapper input {
    border: none !important;
    box-shadow: none !important;
    outline: none;
    flex: 1;
    padding: 8px 12px;
}
    </style>
</head>
<body>
<?php include '../../views/loading_screen.php'; ?>
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
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Kelola Fasilitas & Area</h3>
                            <p class="text-muted m-0">Atur ketersediaan dan informasi area Oemah Keboen.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari area...">
                            </div>
                            <button class="btn-gold shadow-sm text-nowrap" @click="openAdd">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Area
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div v-show="isLoaded">
                    <div class="d-flex gap-2 mb-4 overflow-auto pb-2 align-items-center border-bottom pb-3">
                        <button class="filter-btn text-nowrap rounded-pill" :class="{ active: currentFilter === 'Semua' }" @click="currentFilter = 'Semua'">
                            Semua Area ({{ facilities.length }})
                        </button>
                        <button class="filter-btn text-nowrap rounded-pill" :class="{ active: currentFilter === 'Tersedia' }" @click="currentFilter = 'Tersedia'">
                            Tersedia ({{ countStatus('Tersedia') }})
                        </button>
                        <button class="filter-btn text-nowrap rounded-pill d-flex align-items-center gap-1" :class="{ active: currentFilter === 'Perbaikan' }" @click="currentFilter = 'Perbaikan'">
                            Perbaikan
                            <span v-if="countStatus('Perbaikan') > 0" class="badge bg-warning text-dark rounded-pill px-2">{{ countStatus('Perbaikan') }}</span>
                            <span v-else class="badge bg-secondary rounded-pill px-2">0</span>
                        </button>
                    </div>
                    <div class="row g-4" v-if="filteredFacilities.length > 0">
                        <div class="col-md-6 col-lg-4" v-for="fasil in paginatedFacilities" :key="fasil.id">
                            <div class="facility-card-admin shadow-sm">
                                <img :src="fasil.image || '../../assets/img/logo.jpg'" 
                                @error="$event.target.src='../../assets/img/logo.png'" 
                                class="facility-img-admin">
                                <div class="card-body-fasil">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold m-0">{{ fasil.nama }}</h5>
                                        <span class="status-pill" :class="fasil.status === 'Tersedia' ? 'status-tersedia' : 'status-perbaikan'">
                                            {{ fasil.status }}
                                        </span>
                                    </div>
                                    <p class="desc-fasil">{{ fasil.deskripsi }}</p>

                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div>
                                            <small class="text-muted d-block">Harga</small>
                                            <span class="fw-bold text-success">{{ formatRupiah(fasil.harga) }}</span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button v-if="userRole === 'Admin'" class="btn btn-sm btn-outline-danger" @click="openConfirm(fasil.id)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-green px-3" @click="openEdit(fasil)">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" v-if="filteredFacilities.length > 0">
                        <small class="text-muted fw-medium">
                            Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredFacilities.length) }} dari {{ filteredFacilities.length }} area
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

                    <div v-if="filteredFacilities.length === 0" class="text-center py-5 text-muted">
                        <i class="bi bi-geo-fill fs-1 d-block mb-2"></i>
                        Tidak ada area fasilitas yang ditemukan.
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showFormModal" style="z-index: 1100;" @click.self="showFormModal = false">
                    <div class="modal-box modal-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">
                                {{ isAddMode ? 'Tambah Area Baru' : 'Edit Area: ' + activeFasil.nama }}
                            </h4>
                            <button class="btn-close" @click="showFormModal = false"></button>
                        </div>

                       <div class="row g-4">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">NAMA AREA</label>
                                    <input type="text" class="form-control" v-model="activeFasil.nama" maxlength="40" placeholder="Misal: Gazebo B">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">DESKRIPSI</label>
                                    <textarea class="form-control" rows="4" v-model="activeFasil.deskripsi" maxlength="200"placeholder="Ceritakan fasilitas yang ada di area ini..."></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">HARGA / KEPALA (RP)</label>
                                        <div class="input-rupiah-wrapper">
                                            <span>Rp</span>
                                            <input type="text" inputmode="numeric"
                                                   maxlength="10"
                                                   :value="formatHargaInput(activeFasil.harga)"
                                                   @input="activeFasil.harga = parseHarga($event.target.value)"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">STATUS</label>
                                        <select class="form-select" v-model="activeFasil.status">
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Perbaikan">Dalam Perbaikan</option>
                                        </select>
                                    </div>
                                </div> </div>

                            <div class="col-md-5">
                                <label class="form-label small fw-bold">FOTO FASILITAS</label>
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="d-none">
                                <div class="upload-area text-center p-2 mb-2 bg-light d-flex flex-column justify-content-center align-items-center" style="min-height: 180px;" @click="$refs.fileInput.click()">
                                    <img v-if="activeFasil.previewImage" :src="activeFasil.previewImage" style="max-height: 160px; object-fit: cover; width: 100%; border-radius: 8px;">
                                    <div v-else>
                                        <i class="bi bi-camera fs-1 text-muted d-block"></i>
                                        <span class="btn btn-sm btn-outline-secondary mt-2">Pilih Foto Area</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center" style="font-size: 0.75rem;">Format: JPG/PNG, Max 2MB</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showFormModal = false">Batal</button>
                            <button class="btn btn-gold px-4 rounded-3 d-flex align-items-center" @click="saveFasil" :disabled="isSubmitting">
                            <template v-if="isSubmitting">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Mengunggah...
                            </template>
                            
                            <template v-else>
                                {{ isAddMode ? 'Simpan Area' : 'Simpan Perubahan' }}
                            </template>
                        </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirmModal" style="z-index: 1200; background: rgba(0,0,0,0.6);" @click.self="showConfirmModal = false">
                    <div class="modal-box text-center shadow-lg" style="max-width: 400px;">
                        <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                        <h4 class="font-serif fw-bold mb-2">Hapus Area?</h4>
                        <p class="text-muted mb-4">Data dan foto area akan dihapus secara permanen dari sistem.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Batal</button>
                            <button class="btn btn-danger px-4 rounded-3" @click="executeDelete">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </transition>

        </div>
        <div class="admin-footer">
            &copy; 2026 Oemah Keboen Samarinda | Area & Facility Management v1.2
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
        async function submitUpdatePassword() {
    const oldPw = document.getElementById('old_pw').value;
    const newPw = document.getElementById('new_pw').value;
    const confirmPw = document.getElementById('confirm_pw').value;
    const errorBox = document.getElementById('pwErrorMsg');
    const successBox = document.getElementById('pwSuccessMsg');
    const btn = document.getElementById('btnSavePw');

    errorBox.classList.add('d-none');
    successBox.classList.add('d-none');

    if(!oldPw || !newPw || !confirmPw) {
        errorBox.textContent = "Semua kolom wajib diisi!";
        errorBox.classList.remove('d-none'); return;
    }
    if(newPw !== confirmPw) {
        errorBox.textContent = "Konfirmasi password tidak cocok!";
        errorBox.classList.remove('d-none'); return;
    }
    if(newPw.length < 8) {
        errorBox.textContent = "Password baru minimal 8 karakter!";
        errorBox.classList.remove('d-none'); return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

    try {
        const res = await fetch('../../controllers/UpdatePassword.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ old_password: oldPw, new_password: newPw })
        });
        const data = await res.json();
        
        if(data.status === 'success') {
            successBox.textContent = data.message;
            successBox.classList.remove('d-none');
            document.getElementById('old_pw').value = '';
            document.getElementById('new_pw').value = '';
            document.getElementById('confirm_pw').value = '';
            
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                if(modal) modal.hide();
                successBox.classList.add('d-none');
            }, 1500);
        } else {
            errorBox.textContent = data.message;
            errorBox.classList.remove('d-none');
        }
    } catch (err) {
        errorBox.textContent = "Koneksi bermasalah!";
        errorBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan';
    }
}

    const { createApp } = Vue;
    createApp({
        data() {
            return {
                isLoaded: false,
                userRole: '<?= $_SESSION['admin_role'] ?? '' ?>',
                isSidebarCollapsed: false,
                isSubmitting: false,
                isSidebarMobileOpen: false,
                showLogoutModal: false,
                currentFilter: 'Semua',
                searchQuery: '',
                showFormModal: false,
                showConfirmModal: false,
                isAddMode: false,
                activeFasil: {},
                pendingDeleteId: null,

                facilities: [],
                currentPage: 1,
                itemsPerPage: 6, 

                toast: { show: false, message: '', type: 'success', icon: 'bi-check-circle' }
            }
        },
        computed: {
            filteredFacilities() {
            let res = this.facilities;
            if (this.currentFilter !== 'Semua') {
                res = res.filter(f => f.status === this.currentFilter);
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                res = res.filter(f => f.nama.toLowerCase().includes(q));
            }
            return res;
            },
            totalPages() { return Math.ceil(this.filteredFacilities.length / this.itemsPerPage) || 1; },
            paginatedFacilities() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredFacilities.slice(start, start + this.itemsPerPage);
            }
        },
        watch: {
            searchQuery() { this.currentPage = 1; }
        },
        methods: {
            openConfirm(id) {
                this.pendingDeleteId = id;
                this.showConfirmModal = true;
            },
            formatHargaInput(n) {
                if (!n && n !== 0) return '';
                return parseInt(n).toLocaleString('id-ID');
            },
            parseHarga(val) {
                const clean = val.replace(/[^0-9]/g, '');
                return clean ? parseInt(clean) : 0;
            },
            countStatus(stat) { return this.facilities.filter(f => f.status === stat).length; },
            showToastMsg(message, type = 'success') {
                this.toast.message = message; this.toast.type = type;
                this.toast.icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },
            formatRupiah(n) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
            },
            async fetchFacilities() {
                try {
                    const response = await fetch('../../controllers/FasilitasController.php?action=read');
                    const rawText = await response.text(); 
                    try {
                        const data = JSON.parse(rawText);
                        if (data.status === 'error') { this.showToastMsg("Akses ditolak!", 'error'); return; }
                        this.facilities = data;
                    } catch (e) { console.error(rawText); }
                } catch (e) { this.showToastMsg("Koneksi gagal!", 'error'); }
            },
            openAdd() {
                this.isAddMode = true;
                this.activeFasil = { id: null, nama: '', deskripsi: '', harga: 10000, status: 'Tersedia', image: '', previewImage: null, fileToUpload: null };
                this.showFormModal = true;
            },
            openEdit(fasil) {
                this.isAddMode = false;
                this.activeFasil = JSON.parse(JSON.stringify(fasil));
                this.activeFasil.previewImage = fasil.image; 
                this.activeFasil.fileToUpload = null;
                this.showFormModal = true;
            },
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (!file.type.startsWith('image/')) {
                    this.showToastMsg('Yang diupload harus berupa gambar!', 'error'); return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    this.showToastMsg('Ukuran maksimal 2MB!', 'error'); return;
                }
                this.activeFasil.fileToUpload = file;
                this.activeFasil.previewImage = URL.createObjectURL(file);
            },
            async saveFasil() {
                this.activeFasil.nama = this.activeFasil.nama ? this.activeFasil.nama.trim() : '';
                this.activeFasil.deskripsi = this.activeFasil.deskripsi ? this.activeFasil.deskripsi.trim() : '';

                const nameRegex = /^[a-zA-Z0-9\s.,]+$/;
                if (!this.activeFasil.nama || this.activeFasil.nama.length < 3) {
                    this.showToastMsg("Nama area minimal 3 karakter!", "warning"); 
                    return;
                }
                if (this.activeFasil.nama.length > 50) {
                    this.showToastMsg("Nama area maksimal 50 karakter!", "warning"); 
                    return;
                }
                if (!nameRegex.test(this.activeFasil.nama)) {
                    this.showToastMsg("Nama area tidak boleh mengandung emoji atau simbol aneh!", "error");
                    return;
                }

                if (!this.activeFasil.harga || this.activeFasil.harga < 1000) {
                    this.showToastMsg("Harga sewa minimal Rp 1.000!", "warning"); 
                    return;
                }
                if (this.activeFasil.harga > 50000000) {
                    this.showToastMsg("Harga sewa tidak wajar!", "warning"); 
                    return;
                }

                if (!this.activeFasil.deskripsi || this.activeFasil.deskripsi.trim() === '') {
                    this.showToastMsg("Deskripsi harus diisi!", "warning"); 
                    return;
                }

                if (this.activeFasil.deskripsi.trim().length < 10) {
                    this.showToastMsg("Deskripsi minimal 10 karakter!", "warning"); 
                    return;
                }

                if (!nameRegex.test(this.activeFasil.deskripsi)) {
                    this.showToastMsg("Deskripsi tidak boleh mengandung emoji atau simbol aneh!", "error");
                    return;
                }

                this.isSubmitting = true;

                let formData = new FormData();
                formData.append('action', this.isAddMode ? 'create' : 'update');
                if (!this.isAddMode) formData.append('id', this.activeFasil.id);
                formData.append('nama', this.activeFasil.nama);
                formData.append('deskripsi', this.activeFasil.deskripsi || '');
                formData.append('harga', this.activeFasil.harga);
                formData.append('status', this.activeFasil.status);

                if (this.activeFasil.fileToUpload) {
                    formData.append('image', this.activeFasil.fileToUpload);
                }

                try {
                    const response = await fetch('../../controllers/FasilitasController.php', {
                        method: 'POST', body: formData 
                    });
                    const rawText = await response.text(); 
                    try {
                        const result = JSON.parse(rawText);
                        if (result.status === 'success') {
                            this.showFormModal = false;
                            this.fetchFacilities(); 
                            this.showToastMsg(result.message, 'success');
                        } else {
                            this.showToastMsg(result.message || "Gagal menyimpan.", 'error');
                        }
                    } catch (e) { this.showToastMsg("Kesalahan sistem dari server.", 'error'); }
                } catch (err) { 
                    this.showToastMsg("Koneksi bermasalah!", 'error'); 
                } finally {

                    this.isSubmitting = false;
                }
            },
            async executeDelete() {
                await fetch('../../controllers/FasilitasController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: this.pendingDeleteId })
                });
                this.showConfirmModal = false;
                this.fetchFacilities();
                this.showToastMsg("Area berhasil dihapus!", "success");
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
            this.fetchFacilities();
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