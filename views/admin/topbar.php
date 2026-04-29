<div class="admin-topbar">
    <button class="btn-toggle shadow-sm" id="sidebarToggle" title="Sembunyikan/Tampilkan Menu">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="d-flex align-items-center gap-4">
        <!-- <div class="position-relative" style="cursor: pointer;">
            <i class="bi bi-bell fs-4" style="color: var(--gold-btn);"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">Notifikasi Baru</span>
            </span>
        </div> -->

        <div class="d-flex align-items-center gap-2">
                <div class="font-serif fw-bold m-0" style="color: var(--text-dark); font-size: 1rem;">
                    Hi, <?= explode(' ', trim($admin_name ?? 'Admin'))[0] ?>
                </div>
            <i class="bi bi-person-circle fs-2" style="color: var(--green-main);"></i>
        </div>
    </div>
</div>