<div class="admin-topbar">
    <button class="btn-toggle shadow-sm" id="sidebarToggle" title="Sembunyikan/Tampilkan Menu">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="d-flex align-items-center gap-4">
        <!-- Dropdown Profil -->
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 position-relative" style="cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="font-serif fw-bold m-0" style="color: var(--text-dark); font-size: 1rem;">
                    Hi, <?= explode(' ', trim($admin_name ?? 'Admin'))[0] ?>
                </div>
                <i class="bi bi-person-circle fs-2" style="color: var(--green-main);"></i>
            </div>
            
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                <li class="px-3 py-2 border-bottom mb-1 bg-light">
                    <div class="fw-bold" style="color: var(--text-dark); font-size: 0.9rem;"><?= $admin_name ?? 'Admin' ?></div>
                    <small class="text-muted"><?= $_SESSION['admin_role'] ?? '' ?></small>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        <i class="bi bi-key text-secondary"></i> Ganti Password
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="?action=logout-confirmed">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Ganti Password (Bootstrap Murni) -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title font-serif fw-bold m-0" style="color: var(--green-main);">
                    <i class="bi bi-key me-2"></i>Ganti Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Alert Notifikasi -->
                <div class="alert alert-danger d-none py-2 small" id="pwErrorMsg"></div>
                <div class="alert alert-success d-none py-2 small" id="pwSuccessMsg"></div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password Lama</label>
                    <input type="password" class="form-control" id="old_pw" placeholder="Masukkan password lama">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password Baru</label>
                    <input type="password" class="form-control" id="new_pw" placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="confirm_pw" placeholder="Ulangi password baru">
                </div>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-50 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-gold w-50 rounded-pill fw-bold" id="btnSavePw" onclick="submitUpdatePassword()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>