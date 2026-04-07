<?php
$current_page = 'tiket'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulir Reservasi - Oemah Keboen</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
    .form-card {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(95, 122, 86, 0.08);
        border: 1px solid rgba(95, 122, 86, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }
    .form-control, .form-select {
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        transition: 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--green-main);
        box-shadow: 0 0 0 0.2rem rgba(95, 122, 86, 0.15);
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

    <section class="reservation-hero" style="padding: 60px 0 30px;">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="font-serif fw-bold" style="color: var(--green-main); font-size: 2.5rem;">Formulir Reservasi</h1>
            <p class="text-muted mt-3">
              Lengkapi data di bawah ini. Setelah menyimpan data, Anda akan diarahkan ke WhatsApp Admin untuk konfirmasi DP (Down Payment).
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="section-padding pt-0 mb-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            
            <div class="mb-4">
              <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Pilihan Fasilitas
              </a>
            </div>

            <div class="form-card">
              <form @submit.prevent="submitReservation">
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label">Nama Lengkap Pemesan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.nama" placeholder="Cth: Satria Aegis" required>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" v-model="form.noHp" placeholder="Cth: 081234567890" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Area Fasilitas <span class="text-danger">*</span></label>
                    <select class="form-select" v-model="form.fasilitas" required>
                      <option value="Pendopo">Pendopo Oemah Keboen</option>
                      <option value="Gazebo">Gazebo Privat</option>
                      <option value="Halaman Depan">Halaman Depan (Outdoor)</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="form.tanggal" :min="minDate" required>
                  </div>

                  <div class="col-md-12">
                    <label class="form-label">Perkiraan Jumlah Orang <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" v-model="form.jumlahOrang" placeholder="Cth: 20" min="1" required>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Catatan / Detail Acara (Opsional)</label>
                    <textarea class="form-control" v-model="form.catatan" rows="3" placeholder="Ceritakan singkat acara Anda..."></textarea>
                  </div>

                  <div class="col-12 mt-4" v-if="form.tanggal && form.jumlahOrang">
                    <div class="summary-box">
                      <h5 class="fw-bold mb-3" style="color: var(--green-main);">Rincian Biaya Reservasi</h5>
                      
                      <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Kategori Waktu Booking:</span>
                        <span class="fw-medium">H-{{ daysDifference }} <small v-if="daysDifference < 7" class="text-danger">(Kurang dari H-7)</small></span>
                      </div>

                      <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Harga per Orang:</span>
                        <span class="fw-medium">{{ formatRupiah(hargaPerOrang) }}</span>
                      </div>
                      
                      <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Jumlah Peserta:</span>
                        <span class="fw-medium">{{ form.jumlahOrang }} Orang</span>
                      </div>
                      
                      <hr style="border-color: rgba(95, 122, 86, 0.2);">
                      
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="font-size: 1.1rem; color: var(--text-dark);">Total Estimasi:</span>
                        <span class="fw-bold fs-4" style="color: var(--green-main);">{{ formatRupiah(totalHarga) }}</span>
                      </div>
                      
                      <div class="mt-2 text-end">
                        <small class="text-muted">Wajib Bayar DP 70%: <strong style="color: var(--gold-btn); font-size: 1.05rem;">{{ formatRupiah(totalHarga * 0.7) }}</strong></small>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 mt-2" v-else>
                    <div class="p-3 bg-light rounded text-center border" style="border-style: dashed !important;">
                      <i class="bi bi-info-circle-fill text-warning me-2"></i>
                      <small class="text-muted">Pilih <strong>Tanggal Acara</strong> dan <strong>Jumlah Orang</strong> untuk melihat estimasi biaya dan DP.</small>
                    </div>
                  </div>

                  <div class="col-12 mt-4">
                    <button type="submit" class="btn-submit-wa w-100 d-flex justify-content-center align-items-center gap-2">
                      <i class="bi bi-whatsapp fs-5"></i>
                      Simpan & Lanjut ke WhatsApp Admin
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
          form: {
            nama: '',
            noHp: '',
            fasilitas: 'Pendopo',
            tanggal: '',
            jumlahOrang: '',
            catatan: ''
          },
          minDate: new Date().toISOString().split('T')[0]
        }
      },
      mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const tempatUrl = urlParams.get('tempat');
        if (tempatUrl) {
            this.form.fasilitas = tempatUrl;
        }
      },
      computed: {
        daysDifference() {
            if (!this.form.tanggal) return 0;
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const eventDate = new Date(this.form.tanggal);
            eventDate.setHours(0, 0, 0, 0);
            
            const diffTime = eventDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return diffDays >= 0 ? diffDays : 0;
        },
        hargaPerOrang() {
            return this.daysDifference >= 7 ? 10000 : 15000;
        },
        totalHarga() {
            const jumlah = parseInt(this.form.jumlahOrang) || 0;
            return this.hargaPerOrang * jumlah;
        }
      },
      methods: {
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },
        submitReservation() {
          console.log("Merekam data ke database...", this.form);

          const waNumber = "6285753556422";
          
          let message = `Halo Admin Oemah Keboen, saya ingin melakukan reservasi dengan rincian berikut:\n\n`;
          message += `*Nama:* ${this.form.nama}\n`;
          message += `*No. WA:* ${this.form.noHp}\n`;
          message += `*Fasilitas:* ${this.form.fasilitas}\n`;
          message += `*Tanggal:* ${this.form.tanggal} (H-${this.daysDifference})\n`;
          message += `*Jumlah Peserta:* ${this.form.jumlahOrang} Orang\n\n`;
          message += `*Estimasi Biaya:* ${this.formatRupiah(this.totalHarga)}\n`;
          message += `*Uang Muka (DP 70%):* ${this.formatRupiah(this.totalHarga * 0.7)}\n`;
          
          if(this.form.catatan) {
              message += `\n*Catatan Tambahan:* ${this.form.catatan}\n\n`;
          } else {
              message += `\n\n`;
          }
          
          message += `Mohon info ketersediaan jadwal dan nomor rekening untuk transfer DP ya. Terima kasih!`;

          const encodedMessage = encodeURIComponent(message);
          const waUrl = `https://wa.me/${waNumber}?text=${encodedMessage}`;

          window.open(waUrl, '_blank');
        }
      }
    }).mount('#app');
  </script>
</body>
</html>