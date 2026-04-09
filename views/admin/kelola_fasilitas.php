<?php
require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);
$auth->checkAuth(); 

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_fasilitas.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas - Oemah Keboen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin-style.css">

    <style>

        .facility-card-admin {
            background: white;
            border-radius: 20px;
            border: 1px solid var(--green-main);
            overflow: hidden;
            transition: 0.3s;
            height: 100%; 
            display: flex;
            flex-direction: column;
        }
        .facility-img-admin {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid var(--green-main);
        }
        .card-body-fasil {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .desc-fasil {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
            flex-grow: 1; 
        }
        .status-pill {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-tersedia { background: #DDE5D1; color: #5F7A56; }
        .status-perbaikan { background: #FFE08F; color: #745B0B; }

        .img-preview-upload {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 2px dashed var(--green-main);
        }

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
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Kelola Fasilitas & Area</h3>
                            <p class="text-muted m-0">Atur ketersediaan dan informasi area Oemah Keboen.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari area...">
                            </div>
                            <button class="btn-gold shadow-sm" @click="openAdd">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Area
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade" appear>
                <div class="row g-4" v-if="filteredFacilities.length > 0" v-show="isLoaded">
                    <div class="col-md-6 col-lg-4" v-for="fasil in filteredFacilities" :key="fasil.id">
                        <div class="facility-card-admin shadow-sm">
                            <img :src="fasil.image || '../../assets/img/logo.png'" class="facility-img-admin">
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
                                        <small class="text-muted d-block">Harga / Org</small>
                                        <span class="fw-bold text-success">{{ formatRupiah(fasil.harga) }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-danger" @click="openConfirm('hapus', fasil.id)">
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

                <div v-else class="text-center py-5 text-muted" v-show="isLoaded">
                    <i class="bi bi-geo-fill fs-1 d-block mb-2"></i>
                    Tidak ada area fasilitas yang ditemukan.
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showFormModal" style="z-index: 1100;">
                    <div class="modal-box modal-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">
                                {{ isAddMode ? 'Tambah Area Baru' : 'Edit Area: ' + activeFasil.nama }}
                            </h4>
                            <button class="btn-close" @click="showFormModal = false"></button>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">NAMA AREA</label>
                                    <input type="text" class="form-control" v-model="activeFasil.nama" placeholder="Misal: Gazebo B">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">DESKRIPSI</label>
                                    <textarea class="form-control" rows="4" v-model="activeFasil.deskripsi" placeholder="Ceritakan suasana area..."></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">HARGA (RP)</label>
                                        <input type="number" class="form-control" v-model="activeFasil.harga">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">STATUS</label>
                                        <select class="form-select" v-model="activeFasil.status">
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Perbaikan">Dalam Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label small fw-bold">FOTO FASILITAS</label>
                                <img :src="activeFasil.image || '../../assets/img/logo.png'" class="img-preview-upload">
                                <div class="input-group">
                                    <input type="file" class="form-control form-control-sm" @change="handleFileUpload">
                                </div>
                                <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Format: JPG/PNG, Max 2MB</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showFormModal = false">Batal</button>
                            <button class="btn btn-gold px-4 rounded-3" @click="saveFasil">
                                {{ isAddMode ? 'Simpan Area' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirmModal" style="z-index: 1200; background: rgba(0,0,0,0.6);">
                    <div class="modal-box text-center" style="max-width: 400px;">
                        <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                        <h4 class="font-serif fw-bold mb-2">Hapus Area?</h4>
                        <p class="text-muted mb-4">Area <b>{{ activeFasil.nama }}</b> akan dihapus secara permanen dari sistem.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirmModal = false">Batal</button>
                            <button class="btn btn-danger px-4 rounded-3" @click="executeDelete">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </transition>

        </div> <div class="admin-footer">
            &copy; 2026 Oemah Keboen | Area & Facility Management v1.1
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
                showFormModal: false,
                showConfirmModal: false,
                isAddMode: false,
                activeFasil: {},
                facilities: [
                    { id: 1, nama: 'Pendopo', deskripsi: 'Area luas dan nyaman untuk acara keluarga atau rombongan besar.', harga: 10000, status: 'Tersedia', image: '../../assets/img/pendopo.jpg' },
                    { id: 2, nama: 'Gazebo', deskripsi: 'Tempat santai privat cocok untuk kumpul skala kecil.', harga: 10000, status: 'Tersedia', image: '../../assets/img/gazebo.jpg' },
                    { id: 3, nama: 'Halaman Depan', deskripsi: 'Area terbuka hijau yang fleksibel untuk berbagai aktivitas luar ruangan.', harga: 10000, status: 'Perbaikan', image: '../../assets/img/halaman-depan.jpg' }
                ]
            }
        },
        computed: {
            filteredFacilities() {
                return this.facilities.filter(f => 
                    f.nama.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
        },
        methods: {
            formatRupiah(n) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
            },
            openAdd() {
                this.isAddMode = true;
                this.activeFasil = { id: Date.now(), nama: '', deskripsi: '', harga: 10000, status: 'Tersedia', image: '' };
                this.showFormModal = true;
            },
            openEdit(fasil) {
                this.isAddMode = false;
                this.activeFasil = JSON.parse(JSON.stringify(fasil));
                this.showFormModal = true;
            },
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.activeFasil.image = URL.createObjectURL(file);
                }
            },
            saveFasil() {
                if(!this.activeFasil.nama) return alert('Nama area wajib diisi!');
                if(this.isAddMode) {
                    this.facilities.push(this.activeFasil);
                } else {
                    const index = this.facilities.findIndex(f => f.id === this.activeFasil.id);
                    if(index !== -1) this.facilities[index] = this.activeFasil;
                }
                this.showFormModal = false;
            },
            openConfirm(type, id) {
                this.activeFasil = this.facilities.find(f => f.id === id);
                this.showConfirmModal = true;
            },
            executeDelete() {
                this.facilities = this.facilities.filter(f => f.id !== this.activeFasil.id);
                this.showConfirmModal = false;
            }
        },
        mounted() {

            const toggleBtn = document.getElementById('sidebarToggle');
            if(toggleBtn) {
                toggleBtn.addEventListener('click', () => {
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