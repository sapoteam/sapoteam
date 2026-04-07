<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_reservasi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reservasi - Oemah Keboen</title>

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
        .list-enter-from { opacity: 0; transform: translateX(-20px); }
        .list-leave-to { opacity: 0; transform: translateX(20px); }
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
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Manajemen Reservasi</h3>
                            <p class="text-muted m-0">Pantau jadwal booking, konfirmasi DP, dan kelola database reservasi.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari nama atau ID...">
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
                        <button class="filter-btn" :class="{ active: currentFilter === 'Semua' }" @click="currentFilter = 'Semua'">Semua Reservasi</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Menunggu Review' }" @click="currentFilter = 'Menunggu Review'">Menunggu Review</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Lunas' }" @click="currentFilter = 'Lunas'">Lunas / Terjadwal</button>
                        <button class="filter-btn" :class="{ active: currentFilter === 'Dibatalkan' }" @click="currentFilter = 'Dibatalkan'">Dibatalkan</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless mt-2 text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><i class="bi bi-calendar2-event me-1"></i> Tanggal Acara</th>
                                    <th><i class="bi bi-person me-1"></i> Nama Pemesan</th>
                                    <th><i class="bi bi-geo-alt me-1"></i> Lokasi</th>
                                    <th>Status Pembayaran</th>
                                    <th class="text-center">Aksi Pengelola</th>
                                </tr>
                            </thead>
                            <transition-group name="list" tag="tbody">
                                <tr v-for="(res, index) in filteredReservations" :key="res.id" :style="{ transitionDelay: (index * 0.05) + 's' }">
                                    <td class="text-muted font-monospace">

                                    <td class="fw-medium">{{ res.tanggal }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ res.nama }}</div>
                                        <small class="text-muted"><i class="bi bi-whatsapp me-1"></i>{{ res.noHp }}</small>
                                    </td>
                                    <td><span class="text-secondary">{{ res.lokasi }}</span></td>
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

                        <div v-if="filteredReservations.length === 0" class="text-center py-5 text-muted">
                            <div class="mb-3"><i class="bi bi-search fs-1"></i></div>
                            <h5 class="fw-bold">Data Tidak Ditemukan</h5>
                            <p class="small">Coba ubah kata kunci pencarian atau kategori filter Anda.</p>
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
                                <span v-else>{{ isEditMode ? 'Ubah Data Reservasi' : 'Informasi Reservasi' }} 
                                    <span v-if="activeRes.id">

                                </span>
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
                                <input type="text" class="form-control p-2" v-model="activeRes.noHp" :disabled="!isEditMode && !isAddMode" placeholder="Contoh: 08123456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tanggal Pelaksanaan</label>
                                <input type="text" class="form-control p-2" v-model="activeRes.tanggal" :disabled="!isEditMode && !isAddMode" placeholder="Contoh: 20 April 2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Area / Lokasi Fasilitas</label>
                                <select class="form-select p-2" v-model="activeRes.lokasi" :disabled="!isEditMode && !isAddMode">
                                    <option value="Pendopo">Pendopo Oemah Keboen</option>
                                    <option value="Gazebo">Gazebo Privat</option>
                                    <option value="Halaman Depan">Halaman Depan (Outdoor)</option>
                                </select>
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
                                <button v-if="isAddMode" class="btn btn-gold px-4 rounded-3" @click="saveNew">Simpan Reservasi</button>
                                <button v-if="isEditMode" class="btn btn-gold px-4 rounded-3" @click="saveEdit">Update Perubahan</button>
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
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Batal</button>
                            <button class="btn px-4 rounded-3 text-white shadow-sm" :class="confirmData.btnClass" @click="executeConfirm">
                                {{ confirmData.btnText }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showAlert" style="z-index: 3000;">
                    <div class="modal-box text-center border-warning" style="max-width: 350px; border-top: 5px solid orange;">
                        <i class="bi bi-exclamation-circle text-warning mb-2" style="font-size: 3.5rem;"></i>
                        <h5 class="fw-bold mt-2">Oops! Form Belum Lengkap</h5>
                        <p class="text-muted small">{{ alertMessage }}</p>
                        <button class="btn btn-gold w-100 mt-2" @click="showAlert = false">Perbaiki Sekarang</button>
                    </div>
                </div>
            </transition>

        </div> <div class="admin-footer text-center py-4 border-top mt-auto">
            <small class="text-muted">© 2026 Oemah Keboen Samarinda | Dashboard Management System v1.2</small>
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

                showDetailModal: false,
                isEditMode: false,
                isAddMode: false,
                activeRes: { id: null, nama: '', noHp: '', tanggal: '', lokasi: 'Pendopo', status: '' },

                showConfirmModal: false,
                pendingAction: { type: '', id: null },
                confirmData: { title: '', message: '', icon: '', iconClass: '', btnText: '', btnClass: '' },

                showAlert: false,
                alertMessage: '',

                reservations: [
                    { id: 1, tanggal: '20 April 2026', nama: 'Keluarga Bpk. Budi', noHp: '0812-3456-7890', lokasi: 'Pendopo', status: 'Menunggu Review' },
                    { id: 2, tanggal: '22 April 2026', nama: 'Komunitas Gowes', noHp: '0853-1122-3344', lokasi: 'Gazebo', status: 'Lunas' },
                    { id: 3, tanggal: '25 April 2026', nama: 'TK Harapan Bangsa', noHp: '0811-9988-7766', lokasi: 'Halaman Depan', status: 'Menunggu Review' },
                    { id: 4, tanggal: '28 April 2026', nama: 'Ibu Ratna', noHp: '0822-5555-4444', lokasi: 'Pendopo', status: 'Dibatalkan' },
                    { id: 5, tanggal: '02 Mei 2026', nama: 'Reuni Akbar Alumni 2010', noHp: '0899-7766-5544', lokasi: 'Pendopo', status: 'Menunggu Review' }
                ]
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
                        r.lokasi.toLowerCase().includes(q)
                    );
                }
                return res;
            }
        },
        methods: {
            openAdd() {
                this.isEditMode = false;
                this.isAddMode = true;
                this.activeRes = { id: null, nama: '', noHp: '', tanggal: '', lokasi: 'Pendopo', status: 'Menunggu Review' };
                this.showDetailModal = true;
            },
            saveNew() {
                if (!this.activeRes.nama || !this.activeRes.tanggal || !this.activeRes.noHp) {
                    this.alertMessage = "Semua field harus diisi sebelum menyimpan data baru.";
                    this.showAlert = true; 
                    return;
                }

                const maxId = this.reservations.length > 0 ? Math.max(...this.reservations.map(r => r.id)) : 0;
                this.activeRes.id = maxId + 1;
                this.reservations.unshift({...this.activeRes});
                this.showDetailModal = false;
            },
            openDetail(res, isEdit) {
                this.isAddMode = false;
                this.isEditMode = isEdit;
                this.activeRes = JSON.parse(JSON.stringify(res)); 
                this.showDetailModal = true;
            },
            saveEdit() {
                if (!this.activeRes.nama || !this.activeRes.tanggal) {
                    this.alertMessage = "Nama Pemesan dan Tanggal Acara tidak boleh kosong.";
                    this.showAlert = true; 
                    return;
                }
                const i = this.reservations.findIndex(r => r.id === this.activeRes.id);
                if(i !== -1) this.reservations[i] = this.activeRes;
                this.showDetailModal = false;
            },
            openConfirm(type, id) {
                this.pendingAction = { type, id };
                if (type === 'lunas') {
                    this.confirmData = { title: 'Konfirmasi DP Lunas', message: 'Tandai pembayaran reservasi ini sebagai LUNAS?', icon: 'bi-check-circle-fill', iconClass: 'text-success', btnText: 'Ya, Konfirmasi', btnClass: 'btn-success' };
                } else if (type === 'batal') {
                    this.confirmData = { title: 'Batalkan Reservasi', message: 'Anda yakin ingin membatalkan jadwal booking ini?', icon: 'bi-x-circle-fill', iconClass: 'text-warning', btnText: 'Ya, Batalkan', btnClass: 'btn-warning' };
                } else if (type === 'hapus') {
                    this.confirmData = { title: 'Hapus Permanen', message: 'Data reservasi akan dihapus selamanya dari sistem. Lanjutkan?', icon: 'bi-trash3-fill', iconClass: 'text-danger', btnText: 'Hapus Sekarang', btnClass: 'btn-danger' };
                }
                this.showConfirmModal = true;
            },
            executeConfirm() {
                const { type, id } = this.pendingAction;
                const i = this.reservations.findIndex(r => r.id === id);
                if (i !== -1) {
                    if (type === 'lunas') this.reservations[i].status = 'Lunas';
                    if (type === 'batal') this.reservations[i].status = 'Dibatalkan';
                    if (type === 'hapus') this.reservations.splice(i, 1);
                }
                this.showConfirmModal = false;
                this.showDetailModal = false;
            }
        },
        mounted() {

            const toggle = document.getElementById('sidebarToggle');
            if(toggle) {
                toggle.addEventListener('click', () => {
                    this.isSidebarCollapsed = !this.isSidebarCollapsed;
                    document.getElementById('sidebar').classList.toggle('collapsed');
                });
            }

            setTimeout(() => { this.isLoaded = true; }, 200);
        }
    }).mount('#app');
</script>

</body>
</html>