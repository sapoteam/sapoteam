<?php
$current_page = 'tiket'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulir Reservasi - Oemah Keboen</title>
      <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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
      }, 300);
    });
  });
});
</script>

<body>
        <?php include 'navbar.php'; ?>
  <?php include '../../views/loading_screen.php'; ?>
  <div id="app">
    <div class="page-content">

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
                <a href="tiket_reservasi.php" class="btn-back">
                  <i class="bi bi-arrow-left"></i> Kembali ke Pilihan Fasilitas
                </a>
              </div>

              <div class="form-card">
                <form @submit.prevent="submitReservation">
                  <div class="row g-4">
                    <div class="col-md-6">
                      <label class="form-label">Nama Lengkap Pemesan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" :class="{'is-invalid': errors.nama}" v-model="form.nama" placeholder="Cth: Satria Aegis">
                      <div class="invalid-feedback" v-if="errors.nama">{{ errors.nama }}</div>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" :class="{'is-invalid': errors.noHp}" 
                            v-model="form.noHp" inputmode="numeric" pattern="[0-9]*" 
                            @input="form.noHp = form.noHp.replace(/[^0-9]/g, '')" placeholder="Cth: 081234567890">
                      <div class="invalid-feedback" v-if="errors.noHp">{{ errors.noHp }}</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Area Fasilitas <span class="text-danger">*</span></label>
                      <select class="form-select" v-model="form.fasilitas_id" @change="initFlatpickr" required>
                        <option v-if="facilities.length === 0" value="">Memuat fasilitas...</option>
                        <option v-for="fac in facilities" :key="fac.id" :value="fac.id">
                          {{ fac.nama }} ({{ formatRupiah(fac.harga) }}/org)
                        </option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                      <input type="text" id="tanggalPicker" class="form-control" 
                             :class="form.fasilitas_id ? 'bg-white' : 'bg-light'"
                             v-model="form.tanggal" 
                             :disabled="!form.fasilitas_id"
                             :placeholder="form.fasilitas_id ? 'Pilih tanggal...' : 'Pilih area terlebih dahulu...'"
                             required>
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
                      <button type="submit" class="btn-submit-wa w-100 d-flex justify-content-center align-items-center gap-2" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <i v-else class="bi bi-whatsapp fs-5"></i>
                        <span>{{ isSubmitting ? 'Memproses...' : 'Simpan & Lanjut ke WhatsApp Admin' }}</span>
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
          form: {
            nama: '',
            noHp: '',
            fasilitas_id: '',
            tanggal: '',
            jumlahOrang: '',
            catatan: ''
          },
          facilities: [],
          reservations: [],
          errors: { nama: '', noHp: '' },
          isSubmitting: false,
          minDate: new Date().toISOString().split('T')[0]
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
            const selectedFac = this.facilities.find(f => f.id === this.form.fasilitas_id);
            const baseHarga = selectedFac ? (parseInt(selectedFac.harga) || 0) : 0;

            if (this.daysDifference < 7) {
                return baseHarga + 5000;
            } else {
                return baseHarga;
            }
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
          } else if (String(this.form.noHp).length < 10) {
            this.errors.noHp = 'Nomor terlalu pendek (Minimal 10 digit).';
            valid = false;
          } else if (String(this.form.noHp).length > 15) {
            this.errors.noHp = 'Nomor terlalu panjang (Maksimal 15 digit).';
            valid = false;
          } else if (!String(this.form.noHp).startsWith('08')) {
            this.errors.noHp = 'Nomor harus diawali dengan 08.';
            valid = false;
          }

          return valid;
        },

        async fetchReservations() {
            try {
                const response = await fetch('../../controllers/ReservasiController.php?action=readU');
                const rawText = await response.text();
                try {
                    const data = JSON.parse(rawText);
                    if (data.status !== 'error') {
                        this.reservations = data;
                        this.initFlatpickr(); 
                    }
                } catch (e) { console.error("Gagal load jadwal booking", rawText); }
            } catch (e) { console.error("Koneksi jadwal gagal", e); }
        },

        initFlatpickr() {
            if (!this.form.fasilitas_id) return; 

            const bookedDates = this.reservations
            .filter(r => 
                r.is_booked == 1 && 
                r.fasilitas_id == this.form.fasilitas_id
            )
            .map(r => r.tanggal.split('T')[0]);

            if (this._flatpickrInstance) {
                this._flatpickrInstance.destroy();
            }

            this.$nextTick(() => {
                const el = document.getElementById('tanggalPicker');
                if (!el) return;

                this._flatpickrInstance = flatpickr(el, {
                    locale: 'id',
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    disableMobile: true,
                    disable: bookedDates, 

                    defaultDate: this.form.tanggal || null,
                    onDayCreate: (dObj, dStr, fp, dayElem) => {
                        const dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d"); 

                        if (bookedDates.includes(dateStr)) {
                            dayElem.title = 'Sudah dibooking';
                            dayElem.style.backgroundColor = '#fee2e2';
                            dayElem.style.color = '#dc2626';
                            dayElem.style.borderRadius = '50%';
                            dayElem.style.border = '1px solid #fca5a5';
                        }
                    },
                    onChange: (selectedDates, dateStr) => {
                        this.form.tanggal = dateStr;
                    }
                });
            });
        },

        async fetchFacilities() {
            try {
                const res = await fetch('../../controllers/FasilitasController.php?action=readU');
                const text = await res.text();
                const data = JSON.parse(text);

                if(Array.isArray(data)) {
                    this.facilities = data;

                    const urlParams = new URLSearchParams(window.location.search);
                    const tempatUrl = urlParams.get('tempat');

                    if (tempatUrl && this.facilities.length > 0) {
                        const matched = this.facilities.find(f => f.nama === tempatUrl);
                        if (matched) {
                            this.form.fasilitas_id = matched.id;
                        } else {
                            this.form.fasilitas_id = this.facilities[0].id;
                        }
                    } else if (this.facilities.length > 0) {
                        this.form.fasilitas_id = this.facilities[0].id;
                    }

                    this.$nextTick(() => {
                        if (this.form.fasilitas_id && this.reservations.length > 0) {
                            this.initFlatpickr();
                        }
                    });
                }
            } catch(e) {
                console.error("Gagal load fasilitas", e);
            }
        },

        async submitReservation() {
          if (!this.validate()) return;
          this.isSubmitting = true;

          const payload = {
              action: 'create',
              nama: this.form.nama,
              noHp: this.form.noHp,
              fasilitas_id: this.form.fasilitas_id,
              tanggal: this.form.tanggal,
              jumlah_orang: this.form.jumlahOrang,
              catatan: this.form.catatan,
              total_harga: this.totalHarga,
              status: 'Menunggu Review'
          };

          try {
              const res = await fetch('../../controllers/ReservasiController.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(payload)
              });

              const text = await res.text();
              const responseData = JSON.parse(text);

              if (responseData.status === 'success') {
                  const facName = this.facilities.find(f => f.id === this.form.fasilitas_id)?.nama || 'Fasilitas';
                  const waNumber = "6282252962600";

                  let message = `Halo Admin Oemah Keboen, saya baru saja menyimpan data reservasi di website dengan rincian:\n\n`;
                  message += `*Nama:* ${this.form.nama}\n`;
                  message += `*No. WA:* ${this.form.noHp}\n`;
                  message += `*Fasilitas:* ${facName}\n`;
                  message += `*Tanggal:* ${this.form.tanggal} (H-${this.daysDifference})\n`;
                  message += `*Jumlah Peserta:* ${this.form.jumlahOrang} Orang\n\n`;
                  message += `*Estimasi Biaya:* ${this.formatRupiah(this.totalHarga)}\n`;
                  message += `*Uang Muka (DP 70%):* ${this.formatRupiah(this.totalHarga * 0.7)}\n`;

                  if(this.form.catatan) {
                      message += `\n*Catatan Tambahan:* ${this.form.catatan}\n\n`;
                  } else {
                      message += `\n\n`;
                  }

                  message += `Data saya sudah masuk ke sistem. Mohon info ketersediaan jadwal dan nomor rekening untuk transfer DP ya. Terima kasih!`;

                  const encodedMessage = encodeURIComponent(message);
                  const waUrl = `https://wa.me/${waNumber}?text=${encodedMessage}`;

                  window.open(waUrl, '_blank');
                  this.goWithFade('tiket_reservasi.php');

              } else {
                  alert("Gagal memproses: " + responseData.message);
              }
          } catch(e) {
              alert("Terjadi kesalahan sistem, pastikan jaringan Anda stabil.");
              console.error(e);
          } finally {
              this.isSubmitting = false;
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
        this.fetchFacilities();
        this.fetchReservations();
      }
    }).mount('#app');
  </script>
</body>
</html>