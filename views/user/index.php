<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Oemah Keboen</title>
      <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const page = document.querySelector('.page-content');
  if (!page) return;

  // FADE IN saat halaman load
  page.classList.add('fade-enter');

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      page.classList.remove('fade-enter');
    });
  });

  // FADE OUT saat pindah halaman
  document.querySelectorAll('a[href]').forEach(link => {
    link.addEventListener('click', function (e) {
      const href = this.getAttribute('href');

      if (
        !href ||
        href.startsWith('#') ||
        href.startsWith('javascript:') ||
        this.target === '_blank' ||
        this.hasAttribute('download') ||
        e.ctrlKey || e.metaKey || e.shiftKey || e.altKey
      ) return;

      const url = new URL(this.href, window.location.href);
      if (url.origin !== window.location.origin) return;

      e.preventDefault();

      page.classList.add('fade-exit');

      setTimeout(() => {
        window.location.href = this.href;
      }, 250);
    });
  });
});
</script>

<style>
/* Animasi scroll */
.anim-fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.anim-fade-left {
    opacity: 0;
    transform: translateX(-40px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.anim-fade-right {
    opacity: 0;
    transform: translateX(40px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.anim-visible {
    opacity: 1 !important;
    transform: translate(0) !important;
}

/* Delay untuk card berurutan */
.anim-delay-1 { transition-delay: 0.1s; }
.anim-delay-2 { transition-delay: 0.2s; }
.anim-delay-3 { transition-delay: 0.3s; }
.anim-delay-4 { transition-delay: 0.4s; }

/* Galeri hover lebih smooth */
.gallery-card {
    overflow: hidden;
}
.gallery-card img {
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
}
.gallery-card:hover img {
    transform: scale(1.08) !important;
}
.gallery-card .gallery-caption {
    transition: color 0.3s ease;
}
.gallery-card:hover .gallery-caption {
    color: var(--primary) !important;
    font-weight: 600;
}

/* Why card hover */
.why-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
}
.why-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 20px 40px rgba(67, 100, 59, 0.15) !important;
}

/* Accordion animasi */
.accordion-item {
    transition: box-shadow 0.3s ease;
}
.accordion-item:hover {
    box-shadow: 0 8px 20px rgba(67, 100, 59, 0.08);
}
</style>

<script>
// Intersection Observer untuk animasi scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('anim-visible');
            observer.unobserve(entry.target); // animasi hanya sekali
        }
    });
}, {
    threshold: 0.15,
    rootMargin: '0px 0px -50px 0px'
});

document.addEventListener('DOMContentLoaded', () => {
    // Section Tentang — foto slide kiri, teks slide kanan
    const aboutImg = document.querySelector('.about-image-wrap');
    const aboutText = document.querySelector('.about-text');
    if (aboutImg) { aboutImg.classList.add('anim-fade-left'); observer.observe(aboutImg); }
    if (aboutText) { aboutText.classList.add('anim-fade-right'); observer.observe(aboutText); }


    // Galeri — fade up berurutan
    document.querySelectorAll('.gallery-card').forEach((card, i) => {
        const delay = (i % 3) + 1;
        card.classList.add('anim-fade-up', `anim-delay-${delay}`);
        observer.observe(card);
    });

    // FAQ accordion — fade up
    document.querySelectorAll('.accordion-item').forEach((item, i) => {
        item.classList.add('anim-fade-up', `anim-delay-${Math.min(i + 1, 4)}`);
        observer.observe(item);
    });
});
</script>

<body>
  <div id="app" v-cloak>
    <?php include 'navbar.php'; ?>

    <div class="page-content">
      <section class="hero" id="beranda">
        <div class="container">
          <div class="row">
            <div class="col-lg-8">
              <h1 class="hero-title">Wisata Petik Buah di <span>Oemah Keboen</span></h1>
              <p class="hero-text">
                Nikmati pengalaman masuk kebun, memetik buah segar langsung dari pohonnya,
                menjelajahi suasana alam yang asri, dan menciptakan momen spesial bersama keluarga.
              </p>
              <a href="#tentang" class="btn btn-oemah">Selengkapnya</a>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding" id="tentang">
        <div class="container">
          <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-1 order-2">
              <div id="aboutCarousel" class="carousel slide about-image-wrap" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img src="../../assets/img/tentang1.jpg" alt="1" class="about-image">
                  </div>
                  <div class="carousel-item">
                    <img src="../../assets/img/tentang2.jpg" alt="2" class="about-image">
                  </div>
                  <div class="carousel-item">
                    <img src="../../assets/img/tentang3.jpg" alt="3" class="about-image">
                  </div>
                  <div class="carousel-item">
                    <img src="../../assets/img/tentang4.jpg" alt="4" class="about-image">
                  </div>
                  <div class="carousel-item">
                    <img src="../../assets/img/tentang5.jpg" alt="5" class="about-image">
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
                  <span class="carousel-control-next-icon"></span>
                </button>
              </div>
            </div>
            <div class="col-lg-6 about-text order-lg-2 order-1">
              <h2 class="section-title">Tentang Oemah Keboen</h2>
              <p>Oemah Keboen adalah wisata alam di Samarinda yang menawarkan pengalaman memetik buah langsung dari pohonnya.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding why-section" id="produk">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">Mengapa Oemah Keboen?</h2>
          </div>
          <div class="row g-4 text-center">
            <div class="col-md-6 col-xl-3" v-for="item in whyUs" :key="item.title">
              <div class="why-card">
                <div class="why-icon" :style="{ background: item.bg, color: item.color }">{{ item.icon }}</div>
                <h5>{{ item.title }}</h5>
                <p>{{ item.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding gallery-section" id="galeri">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">Galeri Oemah Keboen</h2>
          </div>
          <div class="row g-4">
            <div class="col-md-6 col-lg-4" v-for="photo in gallery" :key="photo.title">
              <div class="gallery-card">
                <img
                  :src="photo.image"
                  :alt="photo.title"
                  @click="openLightbox(photo.image)"
                >
                <div class="gallery-caption">{{ photo.title }}</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding" id="lokasi">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">Lokasi Oemah Keboen</h2>
            <p class="section-subtitle">
              Kunjungi Oemah Keboen dan nikmati suasana wisata alam yang nyaman di Samarinda.
            </p>
          </div>

          <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
              <div class="location-map h-100">
                <iframe
                  src="https://www.google.com/maps?q=Oemah%20Keboen%20Samarinda&z=15&output=embed"
                  width="100%"
                  height="420"
                  style="border:0;"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="location-info">
                <h5>Informasi Lokasi</h5>
                <p><strong>Nama Tempat:</strong> Oemah Keboen</p>
                <p><strong>Alamat:</strong> Jl. Bendungan No.RT 15, Sambutan, Kec. Sambutan, Kota Samarinda, Kalimantan Timur 75241</p>
                <p><strong>Jam Operasional:</strong> 09.00 - 17.00 WITA</p>
                <p>(Jum'at, Sabtu, dan Minggu)</p>
                <p><strong>Kontak:</strong> 08115522124</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding" id="tiket">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">FAQ</h2>
            <p class="section-subtitle">
              Pertanyaan yang sering ditanyakan pengunjung sebelum datang ke Oemah Keboen.
            </p>
          </div>

          <div class="accordion" id="faqAccordion">
            <div class="accordion-item" v-for="(faq, index) in faqs" :key="index">
              <h2 class="accordion-header">
                <button
                  class="accordion-button"
                  :class="{ collapsed: index !== 0 }"
                  type="button"
                  data-bs-toggle="collapse"
                  :data-bs-target="'#faq' + index">
                  {{ faq.question }}
                </button>
              </h2>
              <div
                :id="'faq' + index"
                class="accordion-collapse collapse"
                :class="{ show: index === 0 }"
                data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  {{ faq.answer }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <teleport to="body">
        <div
          v-if="lightbox.show"
          class="lightbox-modal"
          @click="closeLightbox"
        >
          <button type="button" class="lightbox-close" @click.stop="closeLightbox">
            <i class="bi bi-x-circle-fill"></i>
          </button>

          <img
            :src="lightbox.imageUrl"
            alt="Preview Galeri"
            class="lightbox-content"
            @click.stop
          >
        </div>
      </teleport>

      <?php include 'footer.php'; ?>
    </div>
  </div>

  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'navbar_scripts.php'; ?>

  <script>
    const { createApp } = Vue;

    createApp({
      data() {
        return {
          whyUs: [
            { icon: '🍃', title: 'Petik Buah Langsung', desc: 'Pengunjung dapat memetik buah segar langsung.', bg: '#C7EDB9', color: '#43643B' },
            { icon: '👨‍👩‍👧', title: 'Cocok untuk Keluarga', desc: 'Ideal untuk liburan keluarga.', bg: '#D7EAAB', color: '#546432' },
            { icon: '📚', title: 'Edukasi Berkebun', desc: 'Belajar tentang produk alami.', bg: '#FFE08F', color: '#745B0B' },
            { icon: '🍎', title: 'Fresh dari Kebun', desc: 'Buah lebih segar.', bg: '#ABD19E', color: '#2E4E28' }
          ],
          gallery: [
            { title: 'Area Hijau', image: '../../assets/img/galeri1.jpg' },
            { title: 'Keluarga', image: '../../assets/img/galeri2.jpg' },
            { title: 'Petik Jambu', image: '../../assets/img/galeri3.jpg' },
            { title: 'Spot Foto', image: '../../assets/img/galeri4.jpg' },
            { title: 'Produk Segar', image: '../../assets/img/galeri5.jpg' },
            { title: 'Wisata Edukasi', image: '../../assets/img/galeri6.jpg' }
          ],
          lightbox: {
            show: false,
            imageUrl: ''
          },
          faqs: [
            {
              question: "Apakah boleh memetik buah di kebun?",
              answer: "Boleh, asalkan meminta izin terlebih dahulu kepada petugas atau pengelola."
            },
            {
              question: "Apakah boleh makan buah langsung di kebun?",
              answer: "Tidak diperbolehkan. Jika melanggar, akan dikenakan charge sebesar Rp5.000."
            },
            {
              question: "Kapan waktu terbaik untuk berkunjung?",
              answer: "Waktu terbaik adalah saat musim buah, biasanya pada pertengahan hingga akhir tahun."
            },
            {
              question: "Apakah bisa reschedule untuk reservasi?",
              answer: "Bisa, jadwal kunjungan dapat diatur ulang sesuai kesepakatan dengan pihak pengelola."
            },
            {
              question: "Apakah boleh membawa makanan dari luar?",
              answer: "Boleh, selama pengunjung tetap menjaga kebersihan area wisata."
            },
            {
              question: "Bagaimana cara memetik buah?",
              answer: "Pilih buah yang sudah matang (berwarna cerah), dapat dipandu oleh petani. Setelah dipetik, buah akan ditimbang, dibayar, lalu dapat dikonsumsi."
            }
          ]
        }
      },

      methods: {
        openLightbox(imgUrl) {
          this.lightbox.imageUrl = imgUrl;
          this.lightbox.show = true;
          document.body.classList.add('lightbox-open');
        },
        closeLightbox() {
          this.lightbox.show = false;
          this.lightbox.imageUrl = '';
          document.body.classList.remove('lightbox-open');
        }
      }
    }).mount('#app');
  </script>
</body>
</html>