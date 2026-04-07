<?php
$current_page = 'produk'; 
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
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(95, 122, 86, 0.08);
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .product-img-main {
        width: 100%;
        height: auto;
        max-height: 450px;
        object-fit: cover;
        border-radius: 16px;
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .badge-category {
        background-color: var(--green-light);
        color: var(--green-main);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--green-main);
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }
    .form-control {
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        transition: 0.3s;
    }
    .form-control:focus {
        border-color: var(--green-main);
        box-shadow: 0 0 0 0.2rem rgba(95, 122, 86, 0.15);
    }
    
    .qty-group {
        display: inline-flex;
        align-items: center;
        border: 2px solid var(--green-main);
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
    }
    .btn-qty {
        width: 45px;
        height: 45px;
        border: none;
        background: transparent;
        color: var(--green-main);
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        cursor: pointer;
    }
    .btn-qty:hover {
        background: var(--green-main);
        color: white;
    }
    .qty-input {
        width: 60px;
        height: 45px;
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
        border: none;
        border-left: 2px solid var(--green-main);
        border-right: 2px solid var(--green-main);
        background: transparent;
        color: var(--text-dark);
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .qty-input[type=number] {
        -moz-appearance: textfield; 
        appearance: textfield;
    }

    .btn-submit-wa {
        background-color: #25D366; 
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: 0.3s;
    }
    .btn-submit-wa:hover {
        background-color: #1da851;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
    }
    .btn-submit-wa .wa-icon { display: inline-block; }
    .btn-submit-wa .cart-icon { display: none; }
    
    .btn-submit-wa:hover .wa-icon { display: none; }
    .btn-submit-wa:hover .cart-icon { display: inline-block; animation: popIn 0.3s ease; }

    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .summary-box {
        background-color: #f8fcf5;
        border: 1px solid var(--green-light);
        border-radius: 16px;
        padding: 20px;
    }
    .btn-back {
        color: var(--green-main);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-back:hover {
        color: #4a6142;
        transform: translateX(-5px);
    }
  </style>
</head>
<body>
  <div id="app">
    <?php include 'navbar.php'; ?>

    <div style="padding-top: 100px;"></div>

    <section class="section-padding pt-0 mb-5">
      <div class="container">
        
        <div class="mb-4">
          <a href="javascript:history.back()" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Katalog Produk
          </a>
        </div>

        <div class="detail-card">
          <div class="row g-5">
            <div class="col-lg-5">
              <img :src="product.image" :alt="product.nama" class="product-img-main shadow-sm">
            </div>
            
            <div class="col-lg-7">
              <div class="mb-2">
                <span class="badge-category">{{ product.kategori }}</span>
              </div>
              <h1 class="font-serif fw-bold mb-2" style="color: var(--text-dark);">{{ product.nama }}</h1>
              <div class="product-price mb-3">{{ formatRupiah(product.harga) }}</div>
              
              <p class="text-muted mb-4" style="line-height: 1.6;">
                {{ product.deskripsi }}
              </p>

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
                    <input type="text" class="form-control" v-model="form.nama" placeholder="Masukkan nama..." required>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" v-model="form.noHp" placeholder="Cth: 081234567890" required>
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
                      <span class="wa-icon"><i class="bi bi-whatsapp fs-5"></i></span>
                      <span class="cart-icon"><i class="bi bi-cart-check-fill fs-5"></i></span>
                      <span>Pesan Sekarang</span>
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

  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const { createApp } = Vue;

    createApp({
      data() {
        return {
          product: {
            id: 1,
            nama: 'Little Gardener Kit',
            kategori: 'Paket Edukasi',
            harga: 45000,
            deskripsi: 'Paket edukasi asik buat si kecil! Berisi media tanam, pot mini, bibit pilihan, dan panduan menanam yang interaktif. Cocok untuk mengasah motorik dan rasa cinta lingkungan sejak dini.',
            image: '../../assets/img/produk2.jpg'
          },
          form: {
            qty: 1,
            nama: '',
            noHp: '',
            catatan: ''
          }
        }
      },
      computed: {
        totalHarga() {
            const quantity = parseInt(this.form.qty) || 0;
            return this.product.harga * quantity;
        }
      },
      methods: {
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },
        incrementQty() {
            this.form.qty++;
        },
        decrementQty() {
            if(this.form.qty > 1) {
                this.form.qty--;
            }
        },
        submitOrder() {
          if (!this.form.qty || this.form.qty < 1) {
              alert("Jumlah pembelian minimal 1!");
              return;
          }

          console.log("Menyimpan pesanan...", this.form);

          const waNumber = "6285753556422";
          
          let message = `Halo Admin Oemah Keboen, saya ingin memesan produk berikut:\n\n`;
          message += `*Detail Pemesan:*\n`;
          message += `- Nama: ${this.form.nama}\n`;
          message += `- No. WA: ${this.form.noHp}\n\n`;
          message += `*Detail Pesanan:*\n`;
          message += `- Produk: ${this.product.nama}\n`;
          message += `- Jumlah: ${this.form.qty} pcs\n`;
          message += `- Total Harga: ${this.formatRupiah(this.totalHarga)}\n`;
          
          if(this.form.catatan) {
              message += `\n*Catatan:* ${this.form.catatan}\n\n`;
          } else {
              message += `\n\n`;
          }
          
          message += `Mohon konfirmasi ketersediaan stoknya. Saya akan ambil langsung di lokasi Oemah Keboen. Terima kasih!`;

          const encodedMessage = encodeURIComponent(message);
          const waUrl = `https://wa.me/${waNumber}?text=${encodedMessage}`;

          window.open(waUrl, '_blank');
        }
      }
    }).mount('#app');
  </script>
</body>
</html>