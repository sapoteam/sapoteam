<?php
$current_page = 'produk'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Produk - Oemah Keboen</title>
      <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
    .product-card {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(95, 122, 86, 0.15);
    }
    .product-img-wrapper {
        position: relative;
        width: 100%;
        height: 220px;
        overflow: hidden;
    }
    .product-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-img-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(44, 53, 40, 0.4);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .btn-hover-cart {
        background: #C1A570; color: white; border: none;
        padding: 10px 20px; border-radius: 25px; font-weight: 600;
        font-size: 0.95rem; transform: translateY(20px);
        transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;
    }
    .product-card:hover .product-card-img { transform: scale(1.08); }
    .product-card:hover .product-img-overlay { opacity: 1; }
    .product-card:hover .btn-hover-cart { transform: translateY(0); }
    .product-card-body {
        padding: 20px; flex-grow: 1;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .product-card-title { font-size: 1.25rem; font-weight: 700; color: #2c3528; margin-bottom: 10px; }
    .product-card-desc { font-size: 0.9rem; color: #6c757d; line-height: 1.5; }
    .product-card-price { font-size: 1.3rem; font-weight: 700; color: #4A5D23; margin: 0; }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div id="app">
    <div class="page-content">
      <section class="product-hero">
        <div class="container text-center py-5">
          <h1 class="product-hero-title fw-bold">Produk Oemah Keboen</h1>
          <p class="product-hero-text text-muted">Nikmati berbagai hasil kebun segar dan produk olahan pilihan.</p>
        </div>
      </section>

      <section class="section-padding product-section pb-5">
        <div class="container">
          
          <div v-if="isLoading" class="text-center py-5">
              <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
          </div>

          <div v-else-if="products.length === 0" class="text-center py-5 text-muted">
              <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
              <h5>Belum ada produk yang tersedia saat ini.</h5>
          </div>

          <div v-else class="row g-4 justify-content-center">
              <div class="col-md-6 col-lg-4" v-for="item in products" :key="item.id">
                <div class="product-card" @click="goToDetail(item.id)">
                  <div class="product-img-wrapper">
                    <img :src="item.image ? item.image : '../../assets/img/logo.png'" :alt="item.nama" class="product-card-img">
                    <div class="product-img-overlay">
                      <button class="btn-hover-cart">
                        <i class="bi bi-cart-plus"></i> Pesan Sekarang
                      </button>
                    </div>
                  </div>
                  <div class="product-card-body">
                    <div class="mb-3">
                      <h3 class="product-card-title">{{ item.nama }}</h3>
                      <p class="product-card-desc text-truncate">{{ item.deskripsi }}</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                      <div class="product-card-price">{{ formatRupiah(item.harga) }}</div>
                      <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill">{{ item.kategori }}</span>
                    </div>
                  </div>
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
          products: [],
          isLoading: true
        }
      },
      methods: {
        formatRupiah(number) {
          return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },
        goToDetail(id) {
          const encoded = btoa('ok_' + id);
          this.goWithFade('detail_produk.php?p=' + encoded);
        },
        async fetchProducts() {
          this.isLoading = true;
          try {
            const response = await fetch('../../controllers/ProductController.php?action=read');
            const text = await response.text();
            const data = JSON.parse(text);
            if (Array.isArray(data)) {
              this.products = data.filter(p => p.status === 'Tersedia');
            } else {
              console.error("Data bukan array:", data);
            }
          } catch (e) {
            console.error("Gagal memuat produk:", e);
          } finally {
            this.isLoading = false;
          }
        },
        goWithFade(url) {
          const page = document.querySelector('.page-content');
          if (page) page.classList.add('fade-exit');

          setTimeout(() => {
            window.location.href = url;
          }, 300);
        }
      },
      mounted() {
        this.fetchProducts();
      }
    }).mount('#app');
  </script>
</body>
</html>