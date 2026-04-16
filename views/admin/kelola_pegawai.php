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
            width: 48px; height: 48px;
            background-color: #E9EDC9; color: #5F7A56;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 700; font-size: 1.3rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .text-hp {
            font-size: 0.85rem; color: #6b7564;
            display: flex; align-items: center; gap: 6px; margin-top: 2px;
        }
        .form-switch .form-check-input {
            width: 2.8em; height: 1.4em; cursor: pointer; transition: 0.3s;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--green-main); border-color: var(--green-main);
        }
        .status-label { font-size: 0.85rem; font-weight: 600; margin-left: 5px; }
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

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <div>
                            <h3 class="font-serif fw-bold" style="color: var(--text-dark);">Daftar Pegawai Operasional</h3>
                            <p class="text-muted m-0">Kelola informasi kontak dan hak akses sistem untuk seluruh tim Oemah Keboen.</p>
                        </div>
                        <button class="btn-gold shadow-sm px-4" @click="openAdd">
                            <i class="bi bi-person-plus-fill me-2"></i> Tambah Pegawai
                        </button>
                    </div>

                    <!-- Table - struktur 1:1 sama dashboard -->
                    <div class="table-custom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="search-wrapper" style="max-width: 400px;">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari nama atau username pegawai...">
                            </div>
                            <span class="text-muted small" v-if="users.length > 0">Total: {{ filteredUsers.length }} pegawai</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless mt-3 text-nowrap align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;"><i class="bi bi-person me-2"></i> Profil Pegawai</th>
                                        <th style="width: 20%;"><i class="bi bi-at me-2"></i> Username Pengguna</th>
                                        <th style="width: 20%;"><i class="bi bi-toggle-on me-2"></i> Status Akun</th>
                                        <th style="width: 20%;" class="text-center"><i class="bi bi-gear me-2"></i> Aksi Pengelola</th>
                                    </tr>
                                </thead>
                                <transition-group name="list" tag="tbody">
                                    <tr v-for="(user, index) in paginatedUsers" :key="user.id" :style="{ transitionDelay: (index * 0.1) + 's' }">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-circle">{{ user.nama.charAt(0) }}</div>
                                                <div>
                                                    <div class="fw-medium">{{ user.nama }}</div>
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
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" v-if="filteredUsers.length > 0">
                            <small class="text-muted fw-medium">
                                Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} dari {{ filteredUsers.length }} data
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

                        <!-- Empty state -->
                        <div v-if="filteredUsers.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">Pegawai Tidak Ditemukan</h5>
                            <p class="small">Tidak ada data pegawai yang cocok dengan kriteria pencarian Anda.</p>
                        </div>
                    </div>

                </div>
            </transition>

            <!-- Modal Form Tambah/Edit -->
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
                                <input type="text" class="form-control p-2" v-model="activeUser.nama" maxlength="50" placeholder="Masukkan nama pegawai...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">NOMOR HP / WHATSAPP</label>
                                <input type="text" class="form-control p-2" v-model="activeUser.no_hp" @input="activeUser.no_hp = activeUser.no_hp.replace(/[^0-9]/g, '')" maxlength="15" placeholder="Contoh: 081234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">USERNAME</label>
                                <input type="text" class="form-control p-2" v-model="activeUser.username" @input="activeUser.username = activeUser.username.replace(/\s/g, '')" maxlength="30" placeholder="user_oemah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                <input type="password" class="form-control p-2" v-model="activeUser.password" placeholder="Masukkan password...">
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

            <!-- Modal Konfirmasi Hapus -->
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

        </div>

        <div class="admin-footer">
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
                isSidebarMobileOpen: false,
                showLogoutModal: false,
                searchQuery: '',
                showFormModal: false,
                showConfirm: false,
                isAddMode: true,
                activeUser: {},
                selectedId: null,
                users: [],
                currentPage: 1,
                itemsPerPage: 10,
                toast: {
                    show: false,
                    message: '',
                    type: 'success',
                    icon: 'bi-check-circle'
                }
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
            },
            totalPages() {
                return Math.ceil(this.filteredUsers.length / this.itemsPerPage) || 1;
            },
            paginatedUsers() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredUsers.slice(start, start + this.itemsPerPage);
            }
        },
        watch: {
            searchQuery() { this.currentPage = 1; }
        },
        methods: {
            showToastMsg(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.icon = type === 'success' ? 'bi-check-circle' : (type === 'error' ? 'bi-exclamation-circle' : 'bi-exclamation-triangle');
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },
            async fetchUsers() {
                try {
                    const response = await fetch('../../controllers/UserController.php?action=read');
                    const rawText = await response.text();
                    try {
                        const data = JSON.parse(rawText);
                        if (data.status === 'error') { this.showToastMsg("Akses ditolak!", 'error'); return; }
                        this.users = data;
                    } catch (e) { console.error("Gagal parse JSON:", rawText); }
                } catch (e) { this.showToastMsg("Koneksi gagal!", 'error'); }
            },
            async toggleStatus(user) {
                const newStatus = !user.is_active;
                await fetch('../../controllers/UserController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle', id: user.id, is_active: newStatus })
                });
                this.fetchUsers();
                this.showToastMsg(`Status ${user.nama} berhasil diubah!`, 'success');
            },
            openAdd() {
                this.isAddMode = true;
                this.activeUser = { nama: '', no_hp: '', username: '', password: '', is_active: true };
                this.showFormModal = true;
            },
            openEdit(user) {
                this.isAddMode = false;
                this.activeUser = JSON.parse(JSON.stringify(user));
                this.activeUser.password = '';
                this.showFormModal = true;
            },
            async saveUser() {
                this.activeUser.nama = this.activeUser.nama ? this.activeUser.nama.trim() : '';
                this.activeUser.username = this.activeUser.username ? this.activeUser.username.trim() : '';

                const nameRegex = /^[a-zA-Z0-9\s.,'-]+$/; 
                const usernameRegex = /^[a-zA-Z0-9_]+$/;  

                if (!this.activeUser.nama || this.activeUser.nama.length < 3) {
                    this.showToastMsg('Nama lengkap minimal 3 karakter!', 'warning'); 
                    return;
                }
                if (!nameRegex.test(this.activeUser.nama)) {
                    this.showToastMsg('Nama tidak boleh mengandung emoji atau simbol aneh!', 'error'); 
                    return;
                }

                if (!this.activeUser.username || this.activeUser.username.length < 4) {
                    this.showToastMsg('Username minimal 4 karakter!', 'warning'); 
                    return;
                }
                if (!usernameRegex.test(this.activeUser.username)) {
                    this.showToastMsg('Username hanya boleh huruf, angka, dan underscore (_)! Spasi/Emoji dilarang.', 'error'); 
                    return;
                }

                if (this.isAddMode && !this.activeUser.password) {
                    this.showToastMsg('Password wajib diisi untuk akun baru!', 'warning'); 
                    return;
                }

                if (!this.activeUser.no_hp || this.activeUser.no_hp.length < 10) {
                    this.showToastMsg('Nomor HP tidak valid (min 10 angka)!', 'warning'); 
                    return;
                }
                try {
                    const response = await fetch('../../controllers/UserController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: this.isAddMode ? 'create' : 'update', ...this.activeUser })
                    });
                    const rawText = await response.text();
                    try {
                        const result = JSON.parse(rawText);
                        if (result.status === 'success') {
                            this.showFormModal = false;
                            this.fetchUsers();
                            this.showToastMsg(this.isAddMode ? "Pegawai berhasil ditambahkan!" : "Data berhasil diperbarui!", 'success');
                        } else {
                            this.showToastMsg(result.message || "Gagal menyimpan. Username mungkin sudah dipakai.", 'error');
                        }
                    } catch (e) { this.showToastMsg("Terjadi kesalahan sistem.", 'error'); }
                } catch (e) { this.showToastMsg("Koneksi bermasalah!", 'error'); }
            },
            openConfirmDelete(id) {
                this.selectedId = id;
                this.showConfirm = true;
            },
            async executeDelete() {
                await fetch('../../controllers/UserController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: this.selectedId })
                });
                this.showConfirm = false;
                this.fetchUsers();
                this.showToastMsg("Pegawai berhasil dihapus permanen.", 'success');
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
            this.fetchUsers();
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