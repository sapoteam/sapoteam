<?php
$current_page = 'tiketreservasi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tiket & Reservasi - Oemah Keboen</title>
  <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div id="app">
    <div class="page-content">
      <?php include 'navbar.php'; ?>

      <section class="reservation-hero">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-9">
              <h1 class="reservation-hero-title">Tiket & Reservasi</h1>
              <p class="reservation-hero-text">
                Pilih tiket masuk reguler untuk kunjungan santai, atau lakukan reservasi acara
                untuk menggunakan fasilitas Oemah Keboen seperti pendopo, gazebo, dan halaman depan.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding">
        <div class="container">
          <div class="ticket-card">

            <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 pb-4" style="border-bottom: 1px solid rgba(195,200,188,0.3);">
              <div>
                <span style="background:#d8f3dc; color:#2d6a4f; padding:6px 14px; border-radius:20px; font-size:0.85rem; font-weight:600;">
                  🎫 Tiket Masuk
                </span>
                <h2 class="ticket-card-title mt-3">Tiket Masuk Reguler</h2>
                <p class="text-muted mt-2" style="max-width:600px;">
                  Nikmati suasana kebun terbuka di Oemah Keboen sambil merasakan pengalaman melihat dan memetik buah langsung dari pohonnya.
                </p>
              </div>
              <div class="ticket-price-box text-end mt-3">
                <span class="ticket-price-label">Mulai dari</span>
                <div class="ticket-price">Rp 5.000</div>
                <small style="color:#888;">per orang</small>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div style="background:#f8fcf5; border:1px solid rgba(95,122,86,0.15); border-radius:16px; padding:20px; height:100%;">
                  <h6 style="font-weight:700; color:#2c3528; margin-bottom:10px;">🌿 Fasilitas</h6>
                  <div style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; align-items:flex-start; gap:8px;">
                      <span style="color:#43643b; font-size:0.9rem;">✓</span>
                      <span style="font-size:0.85rem; color:#555;">Area kebun terbuka</span>
                    </div>
                    <div style="display:flex; align-items:flex-start; gap:8px;">
                      <span style="color:#43643b; font-size:0.9rem;">✓</span>
                      <span style="font-size:0.85rem; color:#555;">Petik buah langsung</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div style="background:#fffdf5; border:1px solid rgba(193,165,112,0.25); border-radius:16px; padding:20px; height:100%;">
                  <h6 style="font-weight:700; color:#2c3528; margin-bottom:10px;">📋 Jam Operasional</h6>
                  <div style="font-size:0.85rem; color:#555;">
                    <strong>Jum'at – Minggu</strong><br>
                    09.00 – 17.00 WITA<br>
                    <small class="text-muted">*Tiket beli langsung di tempat</small>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div style="background:#fff5f5; border:1px solid rgba(220,53,69,0.12); border-radius:16px; padding:20px; height:100%;">
                  <h6 style="font-weight:700; color:#2c3528; margin-bottom:10px;">⚠️ Biaya Tambahan</h6>
                  <div style="font-size:0.85rem; color:#555;">
                    Dikenakan <strong>Rp5.000/jam</strong> per orang jika melebihi jam operasional.
                  </div>
                </div>
              </div>
            </div>

            <div style="background:linear-gradient(135deg, #43643b, #5b7d52); border-radius:16px; padding:24px 30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
              <div>
                <div style="color:rgba(255,255,255,0.8); font-size:0.85rem; margin-bottom:4px;">Mau reservasi acara?</div>
                <div style="color:white; font-weight:700; font-size:1.1rem;">Scroll ke bawah untuk pilih fasilitas & cek jadwal</div>
              </div>
              <a href="#reservasi" style="background:#C1A570; color:white; padding:12px 28px; border-radius:12px; text-decoration:none; font-weight:600; font-size:0.95rem;">
                Lihat Fasilitas ↓
              </a>
            </div>

          </div>
        </div>
      </section>

      <section class="section-padding reservation-facilities" id="reservasi">
        <div class="container">
          <div class="section-heading">
            <h2 class="section-title">Fasilitas Reservasi</h2>
            <p class="section-subtitle">Pilih area yang paling sesuai untuk acara kamu di Oemah Keboen.</p>
          </div>
          <div v-if="isLoadingFacilities" class="text-center py-4">
            <div class="spinner-border text-success" role="status"></div>
          </div>
          <div v-else class="row g-4">
            <div class="col-md-6 col-lg-4" v-for="facility in facilities" :key="facility.id">
              <div class="facility-card">
                <img :src="facility.image ? facility.image : '../../assets/img/produk_default.jpg'" :alt="facility.nama" class="facility-image">
                <div class="facility-body">
                  <h3 class="facility-title">{{ facility.nama }}</h3>
                  <p class="facility-desc">{{ facility.deskripsi }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding">
        <div class="container">
          <div class="reservation-box">
            <div class="reservation-head">
              <h2 class="section-title mb-0">Reservasi Acara</h2>
              <div class="reservation-dp">
                <span class="reservation-dp-label">Ketentuan</span>
                <span class="reservation-dp-value">DP 70%</span>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-lg-8">
                <h3 class="reservation-subtitle">Cek Ketersediaan</h3>
                <div class="calendar-card"> 
                  <div class="calendar-header">
                    <button class="calendar-nav" @click="prevMonth"><i class="bi bi-chevron-left"></i></button>
                    <h4>{{ monthNames[currentMonth] }} {{ currentYear }}</h4>
                    <button class="calendar-nav" @click="nextMonth"><i class="bi bi-chevron-right"></i></button>
                  </div>
                  <div class="calendar-weekdays">
                    <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                  </div>
                  <div class="calendar-grid">
                    <div v-for="(day, index) in calendarDays" :key="index"
                      class="calendar-day"
                      :class="{ 'empty': day.isPrevNext, 'booked': day.booked, 'today': day.isToday }">
                      {{ day.date }}
                      <div v-if="day.booked" class="tooltip-event">Sudah dibooking</div>
                    </div>
                  </div>
                  <div class="calendar-legend mt-4">
                    <span class="legend-dot booked-dot"></span>
                    <span>Sudah Terisi (Booked)</span>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <h3 class="reservation-subtitle">Pilih Tempat</h3>
                <div class="reservation-side-card">
                  <div class="reservation-select-wrap">
                    <select class="form-select reservation-select w-100" v-model="selectedPlace">
                      <option v-if="facilities.length === 0" value="">Memuat data...</option>
                      <option v-for="fac in facilities" :key="fac.id" :value="fac.nama">
                        {{ fac.nama }}
                      </option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                  </div>

                  <div class="selected-place-box mt-4">
                    <div class="selected-place-label">TEMPAT TERPILIH</div>
                    <div class="selected-place-value mt-2" style="font-size: 1.4rem; color: var(--primary);">
                      {{ selectedPlace || 'Belum dipilih' }}
                    </div>
                  </div>

                  <div class="mt-4">
                    <a :href="selectedPlace ? 'form_reservasi.php?tempat=' + encodeURIComponent(selectedPlace) : '#'"
                      class="btn btn-outline-dark w-100 py-3 fw-bold d-flex align-items-center justify-content-center"
                      :class="{'disabled': !selectedPlace}"
                      style="border-radius: 12px; transition: 0.3s; border: 1px solid #ccc;">
                      Lanjut Buat Reservasi <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div class="reservation-details mt-5">
              <div class="row g-3">
                <div class="col-md-6">
                  <div style="background:#f8fcf5; border:1px solid rgba(95,122,86,0.15); border-radius:16px; padding:24px; height:100%;">
                    <div style="font-size:1.5rem; margin-bottom:12px;">🌿</div>
                    <h6 style="font-weight:700; color:#2c3528; margin-bottom:14px;">Yang Kamu Dapatkan</h6>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#43643b;">✓</span>
                        <span style="font-size:0.9rem; color:#555;">Akses ke area kebun untuk kegiatan kelompok</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#43643b;">✓</span>
                        <span style="font-size:0.9rem; color:#555;">Pengalaman melihat dan memetik buah langsung</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#43643b;">✓</span>
                        <span style="font-size:0.9rem; color:#555;">Area khusus untuk kegiatan atau acara</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#43643b;">✓</span>
                        <span style="font-size:0.9rem; color:#555;">Fleksibilitas waktu kunjungan sesuai reservasi</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div style="background:#fffdf5; border:1px solid rgba(193,165,112,0.25); border-radius:16px; padding:24px; height:100%;">
                    <div style="font-size:1.5rem; margin-bottom:12px;">📋</div>
                    <h6 style="font-weight:700; color:#2c3528; margin-bottom:14px;">Wajib Tahu</h6>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#C1A570;">•</span>
                        <span style="font-size:0.9rem; color:#555;"><strong>Jam operasional:</strong> 09.00 – 17.00</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#C1A570;">•</span>
                        <span style="font-size:0.9rem; color:#555;">Tersedia pilihan reservasi H-7 dan kurang dari H-7</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#C1A570;">•</span>
                        <span style="font-size:0.9rem; color:#555;">Reservasi wajib dilakukan sebelum hari kunjungan</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#C1A570;">•</span>
                        <span style="font-size:0.9rem; color:#555;">Pengunjung wajib mengikuti aturan yang berlaku</span>
                      </div>
                      <div style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#C1A570;">•</span>
                        <span style="font-size:0.9rem; color:#555;">Area tertentu mungkin dibatasi jika ada reservasi lain</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div style="background:#f0f5e8; border:1px solid rgba(67,100,59,0.15); border-radius:16px; padding:24px; height:100%;">
                    <div style="font-size:1.5rem; margin-bottom:12px;">💰</div>
                    <h6 style="font-weight:700; color:#2c3528; margin-bottom:14px;">Harga Reservasi</h6>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                      <div style="background:white; border-radius:12px; padding:16px; border:1px solid rgba(67,100,59,0.1); display:flex; justify-content:space-between; align-items:center;">
                        <div>
                          <div style="font-size:0.85rem; color:#888;">Reservasi H-7</div>
                          <div style="font-size:0.9rem; color:#555; margin-top:2px;">Booking 7 hari sebelumnya</div>
                        </div>
                        <div style="font-size:1.2rem; font-weight:700; color:#43643b;">Rp 10.000<span style="font-size:0.75rem; font-weight:400; color:#888;">/org</span></div>
                      </div>
                      <div style="background:white; border-radius:12px; padding:16px; border:1px solid rgba(67,100,59,0.1); display:flex; justify-content:space-between; align-items:center;">
                        <div>
                          <div style="font-size:0.85rem; color:#888;">Reservasi &lt; H-7</div>
                          <div style="font-size:0.9rem; color:#555; margin-top:2px;">Booking kurang dari 7 hari</div>
                        </div>
                        <div style="font-size:1.2rem; font-weight:700; color:#c0392b;">Rp 15.000<span style="font-size:0.75rem; font-weight:400; color:#888;">/org</span></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div style="background:#fff5f5; border:1px solid rgba(220,53,69,0.12); border-radius:16px; padding:24px; height:100%;">
                    <div style="font-size:1.5rem; margin-bottom:12px;">⚠️</div>
                    <h6 style="font-weight:700; color:#2c3528; margin-bottom:14px;">Biaya Tambahan</h6>
                    <div style="background:white; border-radius:12px; padding:16px; border:1px solid rgba(220,53,69,0.1);">
                      <div style="font-size:1.4rem; font-weight:700; color:#c0392b;">Rp 5.000</div>
                      <div style="font-size:0.85rem; color:#888; margin-top:4px;">per orang / per jam</div>
                      <hr style="margin:12px 0; border-color:rgba(0,0,0,0.06);">
                      <p style="font-size:0.88rem; color:#555; margin:0;">Dikenakan jika melebihi jam operasional yang berlaku.</p>
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
  </div>

  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'navbar_scripts.php'; ?>

  <script>
    const { createApp } = Vue;
    createApp({
      data() {
        const today = new Date();
        return {
          isLoadingFacilities: true,
          facilities: [],
          allReservations: [],
          selectedPlace: '',
          currentMonth: today.getMonth(),
          currentYear: today.getFullYear(),
          todayDate: today.getDate(),
          todayMonth: today.getMonth(),
          todayYear: today.getFullYear(),
          monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
        }
      },
      computed: {
        calendarDays() {
          const year = this.currentYear;
          const month = this.currentMonth;
          const firstDay = new Date(year, month, 1).getDay();
          const daysInMonth = new Date(year, month + 1, 0).getDate();
          const prevMonthDays = new Date(year, month, 0).getDate();
          
          const dbBookedThisMonth = this.allReservations.filter(res => {
            if (res.status !== 'Disetujui' && res.status !== 'Lunas') return false;
            if (res.lokasi_nama !== this.selectedPlace) return false;
            const resDate = new Date(res.tanggal);
            return resDate.getFullYear() === year && resDate.getMonth() === month;
          });

          const days = [];
          for (let i = firstDay - 1; i >= 0; i--) {
            days.push({ date: prevMonthDays - i, booked: false, isPrevNext: true });
          }
          for (let i = 1; i <= daysInMonth; i++) {
            let isBooked = false;
            const foundDb = dbBookedThisMonth.find(b => new Date(b.tanggal).getDate() === i);
            if (foundDb) { isBooked = true; }
            const isToday = (i === this.todayDate && month === this.todayMonth && year === this.todayYear);
            days.push({ date: i, booked: isBooked, isToday: isToday, isPrevNext: false });
          }
          let nextMonthDate = 1;
          while (days.length < 42) {
            days.push({ date: nextMonthDate++, booked: false, isPrevNext: true });
          }
          return days;
        }
      },
      methods: {
        prevMonth() {
          if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
          else { this.currentMonth--; }
        },
        nextMonth() {
          if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
          else { this.currentMonth++; }
        },
        async fetchFacilities() {
          try {
            const res = await fetch('../../controllers/FasilitasController.php?action=readU');
            const data = await res.json();
            if (Array.isArray(data)) {
              this.facilities = data;
              if (this.facilities.length > 0) this.selectedPlace = this.facilities[0].nama;
            }
          } catch(e) { console.error("Gagal load fasilitas", e); }
          finally { this.isLoadingFacilities = false; }
        },
        async fetchReservations() {
          try {
            const res = await fetch('../../controllers/ReservasiController.php?action=read');
            const data = await res.json();
            if (Array.isArray(data)) this.allReservations = data;
          } catch(e) { console.error("Gagal load reservasi", e); }
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