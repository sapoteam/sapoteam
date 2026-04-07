<?php
$current_page = 'produk'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Produk - Oemah Keboen</title>

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
        height: 220px; /* Sesuaikan tinggi gambar */
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
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(44, 53, 40, 0.4); 
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .btn-hover-cart {
        background: var(--gold-btn);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        transform: translateY(20px); 
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-card:hover .product-card-img {
        transform: scale(1.08); 
    }
    
    .product-card:hover .product-img-overlay {
        opacity: 1; 
    }
    
    .product-card:hover .btn-hover-cart {
        transform: translateY(0); 
    }

    .product-card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .product-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 10px;
    }
    .product-card-desc {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.5;
    }
    
    .product-card-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--green-main);
        margin: 0;
    }
  </style>
</head>
<body>
  <div id="app">
  <?php include 'navbar.php'; ?>

    <section class="product-hero">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="product-hero-title">Produk Oemah Keboen</h1>
            <p class="product-hero-text">
              Nikmati berbagai hasil kebun segar dan produk olahan pilihan dari Oemah Keboen,
              mulai dari buah segar, minuman, hingga paket edukasi yang cocok untuk keluarga.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="section-padding product-section">
    <div class="container-produk">
        <div class="product-scroll">
        <div class="product-track row g-4 justify-content-center">

            <div class="col-md-6 col-lg-4 product-item" v-for="item in products" :key="item.id">
              <div class="product-card" @click="goToDetail(item.id)">
                  
                  <div class="product-img-wrapper">
                      <img :src="item.image" :alt="item.name" class="product-card-img">
                      <div class="product-img-overlay">
                          <button class="btn-hover-cart">
                              <i class="bi bi-cart-plus"></i> Pesan Sekarang
                          </button>
                      </div>
                  </div>

                  <div class="product-card-body">
                    <div class="product-card-content mb-3">
                        <h3 class="product-card-title">{{ item.name }}</h3>
                        <p class="product-card-desc">{{ item.desc }}</p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3" style="border-color: rgba(95, 122, 86, 0.1) !important;">
                        <div class="product-card-price">{{ item.price }}</div>
                    </div>
                  </div>

              </div>
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
          products: [
            {
              id: 1, 
              name: 'Jambu Kristal',
              desc: 'Buah segar hasil petik langsung dari kebun dengan rasa manis dan tekstur renyah.',
              price: 'Rp 25.000',
              image: '../../assets/img/produk1.jpg'
            },
            {
              id: 2,
              name: 'Little Gardener Kit',
              desc: 'Set lengkap alat dan bahan berkebun yang dirancang khusus untuk anak-anak.',
              price: 'Rp 45.000',
              image: '../../assets/img/produk2.jpg'
            },
            {
              id: 3,
              name: 'Jus Jambu Kristal',
              desc: 'Minuman segar dari jambu kristal pilihan dengan rasa manis alami yang menyegarkan.',
              price: 'Rp 20.000',
              image: '../../assets/img/produk2.jpg' 
            }
          ]
        }
      },
      methods: {
        goToDetail(id) {
            window.location.href = 'detail_produk.php?id=' + id;
        }
      }
    }).mount('#app');
  </script>
</body>
</html>