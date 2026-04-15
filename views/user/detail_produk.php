<?php
$encoded = isset($_GET['p']) ? $_GET['p'] : '';
$decoded = base64_decode($encoded);
$product_id = 0;

if ($decoded && strpos($decoded, 'ok_') === 0) {
    $product_id = intval(substr($decoded, 3));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Produk - Oemah Keboen</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
    .detail-card {
        background: #fff; border-radius: 20px; padding: 40px;
        box-shadow: 0 15px 40px rgba(95, 122, 86, 0.08);
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .product-img-main {
        width: 100%; height: auto; max-height: 450px;
        object-fit: cover; border-radius: 16px;
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .badge-category {
        background-color: var(--green-light); color: var(--green-main);
        padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
    }
    .product-price { font-size: 2rem; font-weight: 700; color: var(--green-main); }
    .form-label { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; }
    .form-control {
        padding: 12px 15px; border-radius: 12px;
        border: 1px solid #dee2e6; transition: 0.3s;
    }
    .form-control:focus {
        border-color: var(--green-main);
        box-shadow: 0 0 0 0.2rem rgba(95, 122, 86, 0.15);
    }
    .qty-group {
        display: inline-flex; align-items: center;
        border: 2px solid var(--green-main); border-radius: 12px;
        background: #fff; overflow: hidden;
    }
    .btn-qty {
        width: 45px; height: 45px; border: none; background: transparent;
        color: var(--green-main); font-size: 1.5rem;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s; cursor: pointer;
    }
    .btn-qty:hover { background: var(--green-main); color: white; }
    .qty-input {
        width: 60px; height: 45px; text-align: center;
        font-weight: bold; font-size: 1.2rem; border: none;
        border-left: 2px solid var(--green-main);
        border-right: 2px solid var(--green-main);
        background: transparent; color: var(--text-dark);
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-input[type=number] { -moz-appearance: textfield; appearance: textfield; }
    .btn-submit-wa {
        background-color: #25D366; color: white; border: none;
        border-radius: 12px; padding: 14px; font-weight: 600;
        font-size: 1.1rem; transition: 0.3s;
    }
    .btn-submit-wa:hover {
        background-color: #1da851; color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
    }
    .summary-box {
        background-color: #f8fcf5; border: 1px solid var(--green-light);
        border-radius: 16px; padding: 20px;
    }
    .btn-back {
        color: var(--green-main); font-weight: 600; text-decoration: none;
        display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;
    }
    .btn-back:hover { color: #4a6142; transform: translateX(-5px); }
  </style>
</head>

<body>
  <div id="app">
    <div class="page-content">
      <?php include 'navbar.php'; ?>

      <div style="padding-top: 40px;"></div>

      <section class="section-padding pt-0 mb-5">
        <div class="container">

          <div class="mb-4">
            <a href="produk.php" class="btn-back">
              <i class="bi bi-arrow-left"></i> Kembali ke Katalog Produk
            </a>
          </div>

          <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <div v-else-if="!product" class="text-center py-5 text-muted">
            <i class="bi bi-exclamation-circle fs-1 d-block mb-3"></i>
            <h5>Produk tidak ditemukan atau tidak tersedia.</h5>
          </div>

          <div v-else class="detail-card">
            <div class="row g-5">
              <div class="col-lg-5">
                <img :src="product.image ? product.image : '../../assets/img/produk_default.jpg'" :alt="product.nama" class="product-img-main shadow-sm">
              </div>
              <div class="col-lg-7">
                <div class="mb-2">
                  <span class="badge-category">{{ product.kategori }}</span>
                </div>
                <h1 class="font-serif fw-bold mb-2" style="color: var(--text-dark);">{{ product.nama }}</h1>
                <div class="product-price mb-3">{{ formatRupiah(product.harga) }}</div>
                <p class="text-muted mb-4" style="line-height: 1.6; white-space: pre-line;">{{ product.deskripsi }}</p>

                <hr style="border-color: rgba(95, 122, 86, 0.2); margin-bottom: 25px;">
                <h5 class="fw-bold mb-3">Formulir Pemesanan</h5>

                <form @submit.prevent="submitOrder">
                  <div class="row g-3">
                    <div class="col-12 mb-2">
                      <label class="form-label d-block">Jumlah Pembelian</label>
                      <div class="qty-group">
                        <button type="button" class="btn-qty" @click="decrementQty"><i class="bi bi-dash"></i></button>
                        <input type="number" class="qty-input" v-model.number="form.qty" min="1" required>
                        <button type="button" class="btn-qty" @click="incrementQty"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" :class="{'is-invalid': errors.nama}"
                            v-model="form.nama" placeholder="Masukkan nama...">
                      <div class="invalid-feedback" v-if="errors.nama">{{ errors.nama }}</div>
                    </div>
                    <div class="col-md-6">
                    <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" :class="{'is-invalid': errors.noHp}"
                          v-model="form.noHp" 
                          inputmode="numeric"
                          pattern="[0-9]*"
                          placeholder="Cth: 081234567890"
                          @input="form.noHp = form.noHp.replace(/[^0-9]/g, '')"> <div class="invalid-feedback" v-if="errors.noHp">{{ errors.noHp }}</div>
                  </div>
                    <div class="col-12">  
                      <label class="form-label">Catatan Tambahan (Opsional)</label>
                      <textarea class="form-control" v-model="form.catatan" rows="2" placeholder="Catatan untuk penjual..."></textarea>
                    </div>
                    <div class="col-12 mt-3">
                      <div class="summary-box">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <span class="text-muted d-block mb-1">Total Pembayaran:</span>
                            <small class="text-muted">{{ form.qty || 0 }} x {{ formatRupiah(product.harga) }}</small>
                          </div>
                          <span class="fw-bold fs-3" style="color: var(--green-main);">{{ formatRupiah(totalHarga) }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 mt-2">
                      <div class="p-3 bg-light rounded border text-center" style="border-style: dashed !important;">
                        <i class="bi bi-bag-check-fill text-warning me-2"></i>
                        <small class="text-muted">Pembayaran dan pengambilan produk dilakukan langsung di <strong>Lokasi Oemah Keboen</strong>.</small>
                      </div>
                    </div>
                    <div class="col-12 mt-3">
                      <button type="submit" class="btn-submit-wa w-100 d-flex justify-content-center align-items-center gap-2">
                        <i class="bi bi-whatsapp fs-5"></i>
                        <span>Pesan via WhatsApp</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </section>

      <?php include 'footer.php'; ?>
    </div>
  </div>

  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'navbar_scripts.php'; ?>
  <script>
    const { createApp } = Vue;
    createApp({
      data() {
        return {
          isLoading: true,
          productId: <?php echo $product_id; ?>,
          product: null,
          form: { qty: 1, nama: '', noHp: '', catatan: '' },
          errors: { nama: '', noHp: '' }
        }
      },
      computed: {
        totalHarga() {
          if (!this.product) return 0;
          return this.product.harga * (parseInt(this.form.qty) || 0);
        }
      },
      methods: {
        formatRupiah(number) {
          return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },
        incrementQty() { this.form.qty++; },
        decrementQty() { if (this.form.qty > 1) this.form.qty--; },
        validate() {
          this.errors = { nama: '', noHp: '' };
          let valid = true;

          if (!this.form.nama.trim()) {
            this.errors.nama = 'Nama lengkap wajib diisi.';
            valid = false;
          } else if (this.form.nama.trim().length < 3) {
            this.errors.nama = 'Nama minimal 3 karakter.';
            valid = false;
          } else if (!/^[a-zA-Z\s]+$/.test(this.form.nama.trim())) {
            this.errors.nama = 'Nama hanya boleh huruf dan spasi.';
            valid = false;
          }

            if (!this.form.noHp) {
            this.errors.noHp = 'Nomor WhatsApp wajib diisi.';
            valid = false;
          } else if (this.form.noHp.length < 10) {
            this.errors.noHp = 'Nomor terlalu pendek (Minimal 10 digit).';
            valid = false;
          } else if (this.form.noHp.length > 15) {
            this.errors.noHp = 'Nomor terlalu panjang (Maksimal 15 digit).';
            valid = false;
          } else if (!this.form.noHp.startsWith('08')) {
            this.errors.noHp = 'Nomor harus diawali dengan 08.';
            valid = false;
          }

          return valid;
        },
        async fetchProductDetail() {
          if (this.productId === 0) { this.isLoading = false; return; }
          try {
            const response = await fetch(`../../controllers/ProductController.php?action=get&id=${this.productId}`);
            const text = await response.text();
            const result = JSON.parse(text);
            if (result.status === 'success' && result.data.status === 'Tersedia') {
              this.product = result.data;
            }
          } catch (e) {
            console.error("Gagal load produk", e);
          } finally {
            this.isLoading = false;
          }
        },
        submitOrder() {
          if (!this.validate()) return;
          if (!this.form.qty || this.form.qty < 1) { alert("Jumlah pembelian minimal 1!"); return; }
          const waNumber = "6282252962600";
          let message = `Halo Admin Oemah Keboen, saya ingin memesan produk berikut:\n\n`;
          message += `*Detail Pemesan:*\n- Nama: ${this.form.nama}\n- No. WA: ${this.form.noHp}\n\n`;
          message += `*Detail Pesanan:*\n- Produk: ${this.product.nama}\n- Kategori: ${this.product.kategori}\n`;
          message += `- Jumlah: ${this.form.qty} pcs\n- Harga Satuan: ${this.formatRupiah(this.product.harga)}\n`;
          message += `- *Total Harga: ${this.formatRupiah(this.totalHarga)}*\n`;
          if (this.form.catatan) message += `\n*Catatan:* ${this.form.catatan}\n\n`;
          else message += `\n\n`;
          message += `Mohon konfirmasi ketersediaan stoknya. Terima kasih!`;
          window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`, '_blank');
        }
      },
      mounted() { this.fetchProductDetail(); }
    }).mount('#app');
  </script>
</body>
</html>