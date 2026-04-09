<?php

require_once '../../config/conn.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($conn);

$auth->requireRole('Admin'); 

$admin_name = $_SESSION['admin_name'];
$current_page = 'kelola_pegawai.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai - Oemah Keboen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="admin-style.css">

    <style>

        .avatar-circle {
            width: 48px;
            height: 48px;
            background-color: #E9EDC9;
            color: #5F7A56;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .text-hp {
            font-size: 0.85rem;
            color: #6b7564;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .form-switch .form-check-input {
            width: 2.8em;
            height: 1.4em;
            cursor: pointer;
            transition: 0.3s;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--green-main);
            border-color: var(--green-main);
        }
        .status-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 5px;
        }

        .list-enter-active, .list-leave-active {
            transition: all 0.4s ease;
        }
        .list-enter-from {
            opacity: 0;
            transform: translateX(-30px);
        }
        .list-leave-to {
            opacity: 0;
            transform: scale(0.9);
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
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Daftar Pegawai Operasional</h3>
                            <p class="text-muted m-0">Kelola informasi kontak dan hak akses sistem untuk seluruh tim Oemah Keboen.</p>
                        </div>
                        <button class="btn-gold shadow-sm px-4" @click="openAdd">
                            <i class="bi bi-person-plus-fill me-2"></i> Tambah Pegawai
                        </button>
                    </div>

                    <div class="table-custom p-0 bg-transparent shadow-none border-0 mb-3">
                        <div class="search-wrapper w-100" style="max-width: 400px;">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control shadow-sm" v-model="searchQuery" placeholder="Cari nama atau username pegawai...">
                        </div>
                    </div>

                    <div class="table-custom">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Profil Pegawai</th>
                                        <th style="width: 20%;">ID Pengguna</th>
                                        <th style="width: 20%;">Status Akun</th>
                                        <th style="width: 20%;" class="text-center">Aksi Pengelola</th>
                                    </tr>
                                </thead>
                                <transition-group name="list" tag="tbody">
                                    <tr v-for="(user, index) in filteredUsers" :key="user.id" :style="{ transitionDelay: (index * 0.05) + 's' }">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-circle">{{ user.nama.charAt(0) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ user.nama }}</div>
                                                    <div class="text-hp">
                                                        <i class="bi bi-whatsapp text-success"></i> {{ user.no_hp }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-3 py-2" style="border-radius: 8px; font-weight: 500; font-family: monospace;">
                                                @{{ user.username }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox" role="switch" :id="'sw'+user.id" :checked="user.is_active" @change="toggleStatus(user)">
                                                <label class="status-label" :class="user.is_active ? 'text-success' : 'text-danger'" :for="'sw'+user.id">
                                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="action-btn btn-edit" title="Ubah Data Pegawai" @click="openEdit(user)">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="action-btn btn-cancel" title="Hapus Akun Permanen" @click="openConfirmDelete(user.id)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </transition-group>
                            </table>

                            <div v-if="filteredUsers.length === 0" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                <h5 class="fw-bold">Pegawai Tidak Ditemukan</h5>
                                <p class="small">Tidak ada data pegawai yang cocok dengan kriteria pencarian Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showFormModal" style="z-index: 1100;">
                    <div class="modal-box shadow-lg" style="max-width: 500px;">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="font-serif fw-bold m-0" style="color: var(--green-main);">
                                {{ isAddMode ? 'Tambah Pegawai Baru' : 'Perbarui Data Pegawai' }}
                            </h4>
                            <button class="btn-close" @click="showFormModal = false"></button>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                                <input type="text" class="form-control p-2" v-model="activeUser.nama" placeholder="Masukkan nama pegawai...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">NOMOR HP / WHATSAPP</label>
                                <input type="text" class="form-control p-2" v-model="activeUser.no_hp" placeholder="Contoh: 081234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">USERNAME</label>
                                <input type="text" class="form-control p-2" v-model="activeUser.username" placeholder="user_oemah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                <input type="password" class="form-control p-2" v-model="activeUser.password" placeholder="••••••••">
                                <small class="text-muted" style="font-size: 0.7rem;" v-if="!isAddMode">*Kosongkan jika tidak ingin ganti</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showFormModal = false">Batal</button>
                            <button class="btn btn-gold px-4 rounded-3" @click="saveUser">
                                {{ isAddMode ? 'Daftarkan Pegawai' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div class="modal-overlay" v-if="showConfirm" style="z-index: 1200; background: rgba(0,0,0,0.7);">
                    <div class="modal-box text-center shadow-lg" style="max-width: 380px; border-top: 5px solid #dc3545;">
                        <div class="text-danger mb-3" style="font-size: 4rem;">
                            <i class="bi bi-person-x-fill"></i>
                        </div>
                        <h4 class="font-serif fw-bold mb-2">Hapus Akun?</h4>
                        <p class="text-muted mb-4 small">Akun pegawai ini akan dihapus permanen. Seluruh akses ke sistem akan dicabut segera.</p>

                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-secondary px-4 rounded-3" @click="showConfirm = false">Batal</button>
                            <button class="btn btn-danger px-4 rounded-3 shadow-sm" @click="executeDelete">Ya, Hapus Akun</button>
                        </div>
                    </div>
                </div>
            </transition>

        </div> <div class="admin-footer text-center py-4">
            &copy; 2026 Oemah Keboen Samarinda | HR Management System v1.0
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
                showConfirm: false,
                isAddMode: true,
                activeUser: {},
                selectedId: null,

                users: [
                    { id: 101, nama: 'Satria Aegis', no_hp: '0811-5522-124', username: 'satria_aegis', is_active: true },
                    { id: 102, nama: 'Bambang Kusumo', no_hp: '0852-4433-990', username: 'bambang_k', is_active: true },
                    { id: 103, nama: 'Riana Putri', no_hp: '0821-8877-112', username: 'riana_p', is_active: false },
                    { id: 104, nama: 'Ahmad Faisal', no_hp: '0813-2211-009', username: 'faisal_ahmad', is_active: true },
                    { id: 105, nama: 'Siti Nurhaliza', no_hp: '0852-9900-1122', username: 'siti_n', is_active: true }
                ]
            }
        },
        computed: {
            filteredUsers() {
                if (!this.searchQuery) return this.users;
                const q = this.searchQuery.toLowerCase();
                return this.users.filter(u => 
                    u.nama.toLowerCase().includes(q) || 
                    u.username.toLowerCase().includes(q)
                );
            }
        },
        methods: {
            toggleStatus(user) {
                user.is_active = !user.is_active;

            },
            openAdd() {
                this.isAddMode = true;
                this.activeUser = { id: Date.now(), nama: '', no_hp: '', username: '', password: '', is_active: true };
                this.showFormModal = true;
            },
            openEdit(user) {
                this.isAddMode = false;
                this.activeUser = JSON.parse(JSON.stringify(user));
                this.activeUser.password = ''; 

                this.showFormModal = true;
            },
            saveUser() {
                if(!this.activeUser.nama || !this.activeUser.username) {
                    alert('Nama dan Username wajib diisi!');
                    return;
                }

                if(this.isAddMode) {
                    this.users.unshift({...this.activeUser});
                } else {
                    const i = this.users.findIndex(u => u.id === this.activeUser.id);
                    if(i !== -1) this.users[i] = this.activeUser;
                }
                this.showFormModal = false;
            },
            openConfirmDelete(id) {
                this.selectedId = id;
                this.showConfirm = true;
            },
            executeDelete() {
                this.users = this.users.filter(u => u.id !== this.selectedId);
                this.showConfirm = false;
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

            setTimeout(() => { this.isLoaded = true; }, 100);
        }
    }).mount('#app');
</script>

</body>
</html>