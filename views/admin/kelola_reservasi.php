<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->requireRole('Admin, Pegawai');

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_reservasi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reservasi - Oemah Keboen</title>
        <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin-style.css">

    <style>
        .filter-btn { background: transparent; border: 1px solid var(--green-main); color: var(--green-main); border-radius: 20px; padding: 6px 16px; font-size: 0.9rem; font-weight: 500; transition: 0.3s; }
        .filter-btn.active, .filter-btn:hover { background: var(--green-main); color: white; }
        .badge-tolak { background-color: #dc3545; color: white; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: none; transition: 0.2s; }
        .btn-check-dp { background: #d1e7dd; color: #198754; } .btn-check-dp:hover { background: #198754; color: white; }
        .btn-cancel { background: #f8d7da; color: #dc3545; } .btn-cancel:hover { background: #dc3545; color: white; }
        .btn-edit { background: #e2e3e5; color: #495057; } .btn-edit:hover { background: #6c757d; color: white; }
        .list-enter-active, .list-leave-active { transition: all 0.4s ease; }
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
                    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Manajemen Reservasi</h3>
                            <p class="text-muted m-0">Pantau jadwal booking, konfirmasi DP, dan kelola database reservasi.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari nama pemesan...">
                            </div>
                            <button class="btn-gold shadow-sm text-nowrap" @click="openAdd">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Manual
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="table-custom" v-show="isLoaded">
                    <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Semua' }" @click="currentFilter = 'Semua'">Semua Reservasi</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Menunggu Review' }" @click="currentFilter = 'Menunggu Review'">Menunggu Review</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Lunas' }" @click="currentFilter = 'Lunas'">Lunas / Terjadwal</button>
                        <button class="filter-btn text-nowrap" :class="{ active: currentFilter === 'Dibatalkan' }" @click="currentFilter = 'Dibatalkan'">Dibatalkan</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless mt-2 text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><i class="bi bi-calendar2-event me-1"></i> Tanggal</th>
                                    <th><i class="bi bi-person me-1"></i> Pemesan</th>
                                    <th><i class="bi bi-geo-alt me-1"></i> Area</th>
                                    <th><i class="bi bi-people me-1"></i> Org</th>
                                    <th><i class="bi bi-cash me-1"></i> Total</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <transition-group name="list" tag="tbody">
                                <tr v-for="(res, index) in paginatedReservations" :key="res.id" :style="{ transitionDelay: (index * 0.05) + 's' }">
                                    <td class="text-muted font-monospace">#{{ res.id }}</td>
                                    <td class="fw-medium">{{ formatTanggalCantik(res.tanggal) }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ res.nama }}</div>
                                        <small class="text-muted"><i class="bi bi-whatsapp me-1"></i>{{ res.no_hp || res.noHp }}</small>
                                    </td>
                                    <td><span class="text-secondary">{{ res.lokasi_nama || 'Area Terhapus' }}</span></td>
                                    <td class="text-center">{{ res.jumlah_orang }}</td>
                                    <td class="fw-bold" style="color: var(--green-main);">{{ formatRupiah(res.total_harga) }}</td>
                                    <td>
                                        <span class="badge-status" 
                                              :class="{ 
                                                'badge-lunas': res.status == 'Lunas', 
                                                'badge-menunggu': res.status == 'Menunggu Review', 
                                                'badge-tolak': res.status == 'Dibatalkan' 
                                              }">
                                            {{ res.status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <template v-if="res.status === 'Menunggu Review'">
                                                <button class="action-btn btn-check-dp" title="Konfirmasi Lunas" @click="openConfirm('lunas', res.id)">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="action-btn btn-cancel" title="Batalkan Reservasi" @click="openConfirm('batal', res.id)">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                                <button class="action-btn btn-edit" title="Edit Data" @click="openDetail(res, true)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </template>
                                            <button class="action-btn btn-outline-secondary border" title="Lihat Detail Lengkap" @click="openDetail(res, false)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" v-if="filteredReservations.length > 0">
                            <small class="text-muted fw-medium">
                                Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredReservations.length) }} dari {{ filteredReservations.length }} data
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

                        <div v-if="filteredReservations.length === 0" class="text-center py-5 text-muted">
                            <div class="mb-3"><i class="bi bi-search fs-1"></i></div>
                            <h5 class="fw-bold">Data Tidak Ditemukan</h5>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showDetailModal" style="z-index: 1060;">
                    <div class="modal-box modal-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">
                                <span v-if="isAddMode">Buat Reservasi Baru</span>
                                <span v-else>{{ isEditMode ? 'Ubah Data Reservasi' : 'Informasi Reservasi' }}</span>
                            </h4>
                            <button class="btn-close" @click="showDetailModal = false"></button>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap Pemesan</label>
                                <input type="text" class="form-control p-2" v-model="activeRes.nama" :disabled="!isEditMode && !isAddMode" placeholder="Masukkan nama...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor WhatsApp / HP</label>
                                <input type="text" class="form-control p-2" v-model="activeRes.noHp" @input="activeRes.noHp = activeRes.noHp.replace(/[^0-9]/g, '')" :disabled="!isEditMode && !isAddMode" placeholder="Contoh: 08123456789">
                            </div>

                        <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tanggal Pelaksanaan</label>
                                <input type="date" class="form-control p-2" v-model="activeRes.tanggal" :min="todayDate" @change="calculateTotal" :disabled="!isEditMode && !isAddMode">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Area / Lokasi Fasilitas</label>
                                <select class="form-select p-2" v-model="activeRes.fasilitas_id" @change="calculateTotal" :disabled="!isEditMode && !isAddMode">
                                    <option value="" disabled>-- Pilih Area Fasilitas --</option>
                                    <option v-for="fasil in fasilitasOptions" :key="fasil.id" :value="fasil.id">
                                        {{ fasil.nama }} ({{ formatRupiah(fasil.harga) }}/org)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Jumlah Orang</label>
                                <input type="number" class="form-control p-2" v-model="activeRes.jumlah_orang" min="1" @input="calculateTotal" :disabled="!isEditMode && !isAddMode">
                            </div>
                            <div class="col-md-9">
                                <label class="form-label small fw-bold text-muted text-uppercase">Total Harga (Otomatis)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" readonly class="form-control p-2 border-start-0" v-model="activeRes.total_harga" :disabled="!isEditMode && !isAddMode">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Catatan Tambahan</label>
                                <textarea class="form-control p-2" rows="2" v-model="activeRes.catatan" :disabled="!isEditMode && !isAddMode" placeholder="Ada request khusus? Tulis di sini..."></textarea>
                            </div>

                            <div class="col-12" v-if="!isEditMode && !isAddMode">
                                <div class="p-3 bg-light rounded-4 border border-dashed">
                                    <span class="small text-muted d-block mb-1 fw-bold">STATUS PEMBAYARAN SAAT INI:</span>
                                    <span class="badge-status d-inline-block px-3 py-2" 
                                          :class="{ 
                                            'badge-lunas': activeRes.status == 'Lunas', 
                                            'badge-menunggu': activeRes.status == 'Menunggu Review', 
                                            'badge-tolak': activeRes.status == 'Dibatalkan' 
                                          }">
                                        {{ activeRes.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 pt-3 border-top">
                            <button v-if="!isEditMode && !isAddMode" class="btn btn-outline-danger px-4 rounded-3" @click="openConfirm('hapus', activeRes.id)">
                                <i class="bi bi-trash3 me-2"></i>Hapus Permanen
                            </button>
                            <div v-else></div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-green px-4 rounded-3" @click="showDetailModal = false">Tutup</button>
                                <button v-if="isAddMode || isEditMode" class="btn btn-gold px-4 rounded-3" @click="saveReservasi">
                                    {{ isAddMode ? 'Simpan Reservasi' : 'Update Perubahan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirmModal" style="z-index: 2050; background: rgba(0,0,0,0.7);">
                    <div class="modal-box text-center shadow-lg" style="max-width: 400px; border: 2px solid white;">
                        <div class="modal-icon-big mb-3" :class="confirmData.iconClass">
                            <i :class="confirmData.icon"></i>
                        </div>
                        <h4 class="font-serif fw-bold mb-2">{{ confirmData.title }}</h4>
                        <p class="text-muted mb-4">{{ confirmData.message }}</p>

                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Tutup</button>
                            <button class="btn px-4 rounded-3 text-white shadow-sm" :class="confirmData.btnClass" @click="executeConfirm">
                                {{ confirmData.btnText }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

        </div> 
        <div class="admin-footer text-center py-4 border-top mt-auto">
            <small class="text-muted">© 2026 Oemah Keboen Samarinda | Reservation Management System v1.5</small>
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
                currentFilter: 'Semua',
                searchQuery: '',

                showDetailModal: false,
                isEditMode: false,
                isAddMode: false,
                activeRes: {},

                showConfirmModal: false,
                pendingAction: { type: '', id: null },
                confirmData: { title: '', message: '', icon: '', iconClass: '', btnText: '', btnClass: '' },

                reservations: [],
                fasilitasOptions: [], 

                currentPage: 1,
                itemsPerPage: 10,
                todayDate: new Date().toISOString().split('T')[0],

                toast: { show: false, message: '', type: 'success', icon: 'bi-check-circle' }
            }
        },
        computed: {
            filteredReservations() {
                let res = this.reservations;
                if (this.currentFilter !== 'Semua') {
                    res = res.filter(r => r.status === this.currentFilter);
                }
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    res = res.filter(r => 
                        r.nama.toLowerCase().includes(q) || 
                        r.id.toString().includes(q) ||
                        (r.lokasi_nama && r.lokasi_nama.toLowerCase().includes(q))
                    );
                }
                return res;
            },
            totalPages() { return Math.ceil(this.filteredReservations.length / this.itemsPerPage) || 1; },
            paginatedReservations() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredReservations.slice(start, start + this.itemsPerPage);
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
            formatTanggalCantik(tgl) {
                if(!tgl) return '';
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return new Date(tgl).toLocaleDateString('id-ID', options);
            },
            formatRupiah(n) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n || 0);
            },

            calculateTotal() {

                if (this.activeRes.fasilitas_id && this.activeRes.jumlah_orang && this.activeRes.tanggal) {
                    const fasil = this.fasilitasOptions.find(f => f.id === this.activeRes.fasilitas_id);
                    if (fasil) {
                        let hargaPerKepala = parseInt(fasil.harga);
                        const bookingDate = new Date(this.activeRes.tanggal);
                        const orderDate = new Date(this.todayDate);
                        const selisihWaktu = bookingDate.getTime() - orderDate.getTime();
                        const selisihHari = Math.ceil(selisihWaktu / (1000 * 3600 * 24));

                        if (selisihHari < 7) {
                            hargaPerKepala += 5000;
                        }
                        this.activeRes.total_harga = hargaPerKepala * this.activeRes.jumlah_orang;
                    }
                }
            },

            async fetchReservations() {
                try {
                    const response = await fetch('../../controllers/ReservasiController.php?action=read');
                    const rawText = await response.text();
                    try {
                        const data = JSON.parse(rawText);
                        if (data.status === 'error') { this.showToastMsg("Akses ditolak!", 'error'); return; }
                        this.reservations = data;
                    } catch (e) { console.error(rawText); }
                } catch (e) { this.showToastMsg("Koneksi gagal!", 'error'); }
            },
            async fetchFasilitasOptions() {
                try {
                    const response = await fetch('../../controllers/FasilitasController.php?action=read');
                    const rawText = await response.text();
                    try {
                        const data = JSON.parse(rawText);
                        if (data.status !== 'error') {
                            this.fasilitasOptions = data.filter(f => f.status === 'Tersedia');
                        }
                    } catch (e) { console.error("Gagal load fasilitas"); }
                } catch (e) { console.error("Koneksi load fasilitas gagal"); }
            },

            openAdd() {
                this.isEditMode = false;
                this.isAddMode = true;
                this.activeRes = { id: null, nama: '', noHp: '', tanggal: '', fasilitas_id: '', jumlah_orang: 1, total_harga: 0, catatan: '', status: 'Menunggu Review' };
                this.showDetailModal = true;
            },
            openDetail(res, isEdit) {
                this.isAddMode = false;
                this.isEditMode = isEdit;
                this.activeRes = JSON.parse(JSON.stringify(res)); 
                this.activeRes.noHp = res.noHp || res.no_hp; 
                this.showDetailModal = true;
            },

            async saveReservasi() {
                this.activeRes.nama = this.activeRes.nama ? this.activeRes.nama.trim() : '';
                const nameRegex = /^[a-zA-Z0-9\s.,'-]+$/;

                if (!this.activeRes.nama || this.activeRes.nama.length < 3) {
                    this.showToastMsg("Nama pemesan minimal 3 karakter!", "warning"); return;
                }
                if (!nameRegex.test(this.activeRes.nama)) {
                    this.showToastMsg("Nama pemesan dilarang mengandung emoji!", "error"); return;
                }
                if (!this.activeRes.noHp || this.activeRes.noHp.length < 10) {
                    this.showToastMsg("Nomor HP minimal 10 angka!", "warning"); return;
                }
                if (!this.activeRes.tanggal) {
                    this.showToastMsg("Tanggal wajib dipilih!", "warning"); return;
                }
                if (!this.activeRes.fasilitas_id) {
                    this.showToastMsg("Area fasilitas wajib dipilih!", "warning"); return;
                }
                if (this.activeRes.tanggal < this.todayDate) {
                    this.showToastMsg("Tidak bisa membooking untuk tanggal masa lalu!", "error"); 
                    return;
                }

                const bentrok = this.reservations.find(r => 
                    r.tanggal === this.activeRes.tanggal && 
                    r.fasilitas_id === this.activeRes.fasilitas_id && 
                    r.status !== 'Dibatalkan' && 
                    r.id !== this.activeRes.id
                );

                if (bentrok) {
                    this.showToastMsg("Area ini sudah dibooking pada tanggal tersebut!", "error");
                    return;
                }

                try {
                    const payload = { action: this.isAddMode ? 'create' : 'update', ...this.activeRes };
                    const response = await fetch('../../controllers/ReservasiController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const rawText = await response.text();
                    try {
                        const result = JSON.parse(rawText);
                        if (result.status === 'success') {
                            this.showDetailModal = false;
                            this.fetchReservations();
                            this.showToastMsg(result.message, 'success');
                        } else {
                            this.showToastMsg(result.message || "Gagal menyimpan data.", 'error');
                        }
                    } catch (e) { this.showToastMsg("Kesalahan sistem server.", 'error'); }
                } catch (e) { this.showToastMsg("Koneksi bermasalah!", 'error'); }
            },

            openConfirm(type, id) {
                this.pendingAction = { type, id };
                if (type === 'lunas') {
                    this.confirmData = { title: 'Konfirmasi Lunas', message: 'Tandai pembayaran reservasi ini sebagai LUNAS?', icon: 'bi-check-circle-fill', iconClass: 'text-success', btnText: 'Ya, Konfirmasi', btnClass: 'btn-success' };
                } else if (type === 'batal') {
                    this.confirmData = { title: 'Batalkan Reservasi', message: 'Anda yakin ingin membatalkan jadwal booking ini?', icon: 'bi-x-circle-fill', iconClass: 'text-warning', btnText: 'Ya, Batalkan', btnClass: 'btn-warning' };
                } else if (type === 'hapus') {
                    this.confirmData = { title: 'Hapus Permanen', message: 'Data reservasi akan dihapus selamanya dari sistem. Lanjutkan?', icon: 'bi-trash3-fill', iconClass: 'text-danger', btnText: 'Hapus Sekarang', btnClass: 'btn-danger' };
                }
                this.showConfirmModal = true;
            },

            async executeConfirm() {
                const { type, id } = this.pendingAction;
                let payload = { id: id };
                if (type === 'hapus') {
                    payload.action = 'delete';
                } else {
                    payload.action = 'update_status';
                    payload.status = type === 'lunas' ? 'Lunas' : 'Dibatalkan';
                }

                try {
                    const response = await fetch('../../controllers/ReservasiController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const rawText = await response.text();
                    try {
                        const result = JSON.parse(rawText);
                        if (result.status === 'success') {
                            this.showConfirmModal = false;
                            this.showDetailModal = false;
                            this.fetchReservations();
                            this.showToastMsg(result.message, 'success');
                        } else {
                            this.showToastMsg(result.message || "Gagal mengeksekusi perintah.", 'error');
                        }
                    } catch (e) { this.showToastMsg("Kesalahan sistem server.", 'error'); }
                } catch (e) { this.showToastMsg("Koneksi bermasalah!", 'error'); }
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
            this.fetchReservations();
            this.fetchFasilitasOptions();
            const btn = document.getElementById('sidebarToggle');
            if (btn) {
                btn.addEventListener('click', this.toggleSidebar);
            }
            document.querySelectorAll('.nav-sidebar .nav-link').forEach(link => {
                link.addEventListener('click', this.closeSidebarMobile);
            });
            window.addEventListener('resize', this.handleResize);
            this.handleResize(); 
            setTimeout(() => { this.isLoaded = true; }, 200);
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