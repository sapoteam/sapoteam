<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Review - Oemah Keboen</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">

  <style>

    .review-gallery-page img {
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }
    .review-gallery-page img:hover {
        transform: scale(1.05);
    }

    .lightbox-modal {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background-color: rgba(0, 0, 0, 0.85);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        transition: 0.2s;
    }
    .lightbox-close:hover {
        color: #C1A570;
        transform: scale(1.1);
    }
    .fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
    .fade-enter-from, .fade-leave-to { opacity: 0; }
  </style>
</head>
<body>
  <div id="app">
  <?php include 'navbar.php'; ?>

    <section class="section-padding review-page">
      <div class="container">
        <div class="review-page-topbar">
          <h1 class="review-page-title">Oemah Keboen: Insider Insights and Visitor Experiences</h1>
        </div>

        <div class="review-list-page">
          <div v-if="reviews.length === 0" class="text-center py-5 text-muted">
             <i class="bi bi-chat-square-heart fs-1 d-block mb-3"></i>
             <h4>Belum ada ulasan yang ditampilkan.</h4>
             <p>Jadilah yang pertama membagikan pengalaman seru kamu!</p>
          </div>

          <div class="review-card-page" v-for="(review, index) in reviews" :key="index">
            <div class="review-avatar-page">
              <i class="bi bi-person-fill"></i>
            </div>

            <div class="review-content-page">
              <h3 class="review-name-page">{{ review.name }}</h3>

              <div class="review-rating-page">
                <i v-for="star in 5" :key="star" class="bi" :class="star <= review.rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"></i>
              </div>

              <p class="review-text-page">{{ review.text }}</p>

              <div class="review-gallery-page" v-if="review.images && review.images.length > 0">
                <div class="review-thumb-page" v-for="(img, i) in review.images" :key="i">
                  <img :src="img" alt="Foto review" style="object-fit: cover; width: 100px; height: 100px; border-radius: 8px;" @click="openLightbox(img)">
                </div>
              </div>

              <div class="review-date-page text-muted mt-2 small">{{ review.date }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-padding review-form-section" style="background-color: #f9f9f4;">
      <div class="container">
        <div class="review-form-page-box bg-white p-4 rounded-4 shadow-sm mx-auto" style="max-width: 800px;">
          <h2 class="review-form-page-title mb-4">How was Oemah Keboen?</h2>

          <div v-if="alert.show" class="alert" :class="'alert-' + alert.type" role="alert">
             <i class="bi me-2" :class="alert.type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'"></i>
             {{ alert.message }}
          </div>

          <div class="review-form-group mb-3">
            <label class="fw-bold small mb-1">Nama Lengkap</label>
            <input type="text" class="form-control review-input" v-model="newReview.name" placeholder="Masukkan nama kamu">
          </div>

          <div class="review-form-group mb-3">
            <label class="fw-bold small mb-1">Add your rating</label>
            <div class="review-stars-input fs-4" style="cursor: pointer;">
              <i v-for="star in 5" :key="star" class="bi me-1" :class="star <= newReview.rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'" @click="newReview.rating = star"></i>
            </div>
          </div>

          <div class="review-form-group mb-3">
            <label class="fw-bold small mb-1">Tell us about your visit</label>
            <div class="review-textarea-wrap">
              <div class="review-textarea-note text-muted small mb-2">
                Tell us about your trip to inspire and help other travelers.
              </div>
              <textarea class="form-control review-textarea" rows="5" v-model="newReview.text" placeholder="Ceritakan pengalamanmu..."></textarea>
            </div>
          </div>

          <div class="review-form-group">
            <label>Add Photos (Optional)</label>
            <p class="review-upload-desc">
              Got some cool pics? Drop them here and show what your visit looked like! (Max 2MB per photo)
            </p>

            <div class="review-upload-box" @click="$refs.fileInput.click()">
              <input type="file" class="review-upload-input" multiple accept="image/*" @change="handleReviewImages" ref="fileInput" style="display: none;">
              <i class="bi bi-camera-fill"></i>
              <span>{{ newReview.files.length }}/5</span>
            </div>

            <div class="d-flex gap-2 mt-3 flex-wrap" v-if="newReview.previews.length > 0">
                <div v-for="(prev, index) in newReview.previews" :key="index" style="position: relative;">
                    <img :src="prev" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                    <button class="btn btn-sm btn-danger rounded-circle p-1" style="position: absolute; top: -5px; right: -5px; width: 24px; height: 24px; line-height: 1;" @click.stop="removeImage(index)">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
          </div>

          <div class="d-flex gap-3 flex-wrap pt-3 border-top mt-4">
            <button class="btn fw-bold px-4 py-2 rounded-3 text-white" style="background-color: #C1A570; border: none;" @click="submitReview" :disabled="isSubmitting">
                <span v-if="isSubmitting">Mengirim...</span>
                <span v-else>Submit Review</span>
            </button>
            <button class="btn btn-outline-secondary px-4 py-2 rounded-3" @click="resetForm">Batal</button>
          </div>
        </div>
      </div>
    </section>

    <transition name="fade">
        <div v-if="lightbox.show" class="lightbox-modal" @click="lightbox.show = false">
            <i class="bi bi-x-circle lightbox-close"></i>
            <img :src="lightbox.imageUrl" class="lightbox-content" @click.stop>
        </div>
    </transition>

  <?php include 'footer.php'; ?>

  </div>

  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const { createApp } = Vue;

    createApp({
      data() {
        return {
          reviews: [],
          isSubmitting: false,
          alert: { show: false, message: '', type: 'success' },

          lightbox: {
              show: false,
              imageUrl: ''
          },

          newReview: {
            name: '',
            rating: 0,
            text: '',
            files: [],    
            previews: []  
          }
        }
      },
      methods: {
        showAlert(msg, type) {
            this.alert = { show: true, message: msg, type: type };
            setTimeout(() => { this.alert.show = false; }, 5000);
        },

        openLightbox(imgUrl) {
            this.lightbox.imageUrl = imgUrl;
            this.lightbox.show = true;
        },

        async fetchReviews() {
            try {
                const response = await fetch('../../controllers/UlasanController.php?action=read_approved');
                const data = await response.json();

                this.reviews = data.map(r => ({
                    id: r.id,
                    name: r.nama,
                    rating: parseInt(r.rating),
                    text: r.komentar,
                    date: r.tanggal_format, 
                    images: r.foto 
                }));
            } catch (e) {
                console.error("Gagal load ulasan", e);
            }
        },

        handleReviewImages(event) {
          const selectedFiles = Array.from(event.target.files);
          if (!selectedFiles.length) return;

          if (this.newReview.files.length + selectedFiles.length > 5) {
              this.showAlert('Maksimal hanya boleh upload 5 foto!', 'warning');
              return;
          }

          selectedFiles.forEach(file => {
              if (!file.type.startsWith('image/')) {
                  this.showAlert(`File ${file.name} bukan gambar.`, 'danger'); return;
              }
              if (file.size > 2 * 1024 * 1024) {
                  this.showAlert(`File ${file.name} melebihi 2MB.`, 'danger'); return;
              }

              this.newReview.files.push(file);
              this.newReview.previews.push(URL.createObjectURL(file));
          });

          this.$refs.fileInput.value = ''; 
        },

        removeImage(index) {
            this.newReview.files.splice(index, 1);
            this.newReview.previews.splice(index, 1);
        },

        resetForm() {
          this.newReview = { name: '', rating: 0, text: '', files: [], previews: [] };
          if(this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        async submitReview() {
          this.newReview.name = this.newReview.name.trim();
          this.newReview.text = this.newReview.text.trim();

          const nameRegex = /^[a-zA-Z0-9\s.,'?!()-]+$/;

          if (!this.newReview.name || this.newReview.name.length < 3) {
              this.showAlert('Nama wajib diisi minimal 3 karakter!', 'warning'); return;
          }
          if (!nameRegex.test(this.newReview.name)) {
              this.showAlert('Nama tidak boleh mengandung emoji atau simbol aneh!', 'danger'); return;
          }

          if (this.newReview.rating === 0) {
              this.showAlert('Silakan berikan rating bintang terlebih dahulu!', 'warning'); return;
          }

          if (!this.newReview.text || this.newReview.text.length < 10) {
              this.showAlert('Komentar terlalu singkat, ceritakan lebih banyak pengalamanmu (minimal 10 karakter)!', 'warning'); return;
          }

          this.isSubmitting = true;
          let formData = new FormData();
          formData.append('action', 'create');
          formData.append('nama', this.newReview.name);
          formData.append('rating', this.newReview.rating);
          formData.append('komentar', this.newReview.text);

          this.newReview.files.forEach((file) => {
              formData.append('fotos[]', file);
          });

          try {
              const response = await fetch('../../controllers/UlasanController.php', {
                  method: 'POST',
                  body: formData
              });
              const result = await response.json();

              if (result.status === 'success') {
                  this.showAlert('Ulasan berhasil dikirim! Menunggu persetujuan Admin.', 'success');
                  this.resetForm();
              } else {
                  this.showAlert(result.message || 'Gagal mengirim ulasan.', 'danger');
              }
          } catch (e) {
              this.showAlert('Koneksi server bermasalah.', 'danger');
          } finally {
              this.isSubmitting = false;
          }
        }
      },
      mounted() {
        this.fetchReviews();
      }
    }).mount('#app');
  </script>
</body>
</html>