<?php
$current_page = 'tiketreservasi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tiket & Reservasi - Oemah Keboen</title>

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
            <div class="ticket-card-head">
              <h2 class="ticket-card-title">Tiket Masuk Reguler</h2>
              <div class="ticket-price-box">
                <span class="ticket-price-label">Mulai dari</span>
                <span class="ticket-price">Rp 5.000</span>
              </div>
            </div>

            <div class="ticket-info-block">
              <p>Nikmati suasana kebun terbuka di Oemah Keboen sambil merasakan pengalaman melihat dan memetik buah langsung dari pohonnya.</p>
            </div>

            <div class="ticket-info-block">
              <h3>Yang Kamu Dapatkan</h3>
              <ul class="ticket-list">
                <li>Area terbuka untuk bersantai</li>
                <li>Pengalaman melihat dan memetik buah langsung</li>
                <li>Area nyaman untuk rekreasi keluarga</li>
              </ul>
            </div>

            <div class="ticket-info-block">
              <h3>Wajib Tahu</h3>
              <ul class="ticket-list">
                <li><strong>Jam operasional:</strong> Jum’at – Minggu, 09.00 – 17.00</li>
                <li>Pembelian tiket dilakukan secara langsung di tempat</li>
                <li>Pengunjung wajib mengikuti aturan yang berlaku</li>
                <li>Area tertentu mungkin dibatasi jika terdapat reservasi acara</li>
              </ul>
            </div>

            <div class="ticket-info-block">
              <h3>Biaya Tambahan</h3>
              <p>Dikenakan biaya Rp5.000/orang jika melebihi jam operasional.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding reservation-facilities">
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

            <div class="row g-4 align-items-start">
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
                      :class="{ 'empty': !day.date, 'booked': day.booked, 'today': day.isToday }">
                      {{ day.date || '' }}
                      <div v-if="day.event" class="tooltip-event">{{ day.event }}</div>
                    </div>
                  </div>
                </div>

                <div class="calendar-legend">
                  <span class="legend-dot booked-dot"></span>
                  <span>Sudah Terisi (Booked)</span>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="reservation-side-card">
                  <h3 class="reservation-subtitle">Pilih Tempat</h3>

                  <div class="reservation-select-wrap">
                    <select class="form-select reservation-select" v-model="selectedPlace">
                      <option v-if="facilities.length === 0" value="">Memuat data...</option>
                      <option v-for="fac in facilities" :key="fac.id" :value="fac.nama">
                        {{ fac.nama }}
                      </option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                  </div>

                  <div class="selected-place-box mt-3">
                    <div class="selected-place-label">Tempat Terpilih</div>
                    <div class="selected-place-value">{{ selectedPlace || 'Belum dipilih' }}</div>
                  </div>

                  <a :href="selectedPlace ? 'form_reservasi.php?tempat=' + encodeURIComponent(selectedPlace) : '#'" 
                    class="btn btn-gold w-100 mt-4 py-2 fw-bold" 
                    :class="{'disabled': !selectedPlace}"
                    style="border-radius: 12px; font-size: 1.1rem;">
                    Lanjut Buat Reservasi <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </div>

            <div class="reservation-details mt-5">
              <div class="reservation-detail-block">
                <p>
                  Nikmati pengalaman wisata di Oemah Keboen dengan suasana kebun terbuka yang cocok
                  untuk kegiatan kelompok dan acara bersama.
                </p>
              </div>

              <div class="reservation-detail-block">
                <h3>Yang Kamu Dapatkan</h3>
                <ul class="ticket-list">
                  <li>Akses ke area kebun untuk kegiatan kelompok</li>
                  <li>Pengalaman melihat dan memetik buah langsung</li>
                  <li>Area khusus untuk kegiatan atau acara</li>
                  <li>Fleksibilitas waktu kunjungan sesuai reservasi</li>
                </ul>
              </div>

              <div class="reservation-detail-block">
                <h3>Harga Reservasi</h3>
                <ul class="ticket-list">
                  <li><strong>Reservasi H-7:</strong> Rp10.000/orang</li>
                  <li><strong>Reservasi &lt; H-7:</strong> Rp15.000/orang</li>
                </ul>
              </div>

              <div class="reservation-detail-block">
                <h3>Wajib Tahu</h3>
                <ul class="ticket-list">
                  <li><strong>Jam operasional:</strong> 09.00 – 17.00</li>
                  <li>Tersedia pilihan reservasi H-7 dan kurang dari H-7</li>
                  <li>Reservasi wajib dilakukan sebelum hari kunjungan</li>
                  <li>Pengunjung wajib mengikuti aturan yang berlaku</li>
                  <li>Area tertentu mungkin dibatasi jika terdapat reservasi acara</li>
                </ul>
              </div>

              <div class="reservation-detail-block">
                <h3>Biaya Tambahan</h3>
                <p>Dikenakan biaya Rp5.000/orang jika melebihi jam operasional.</p>
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
          monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
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
            days.push({ date: prevMonthDays - i, booked: false, event: null, isToday: false });
          }

          for (let i = 1; i <= daysInMonth; i++) {
            let isBooked = false;
            let eventName = null;

            const foundDb = dbBookedThisMonth.find(b => new Date(b.tanggal).getDate() === i);
            if (foundDb) {
                isBooked = true;
                eventName = 'Sudah dibooking'; 
            }

            const isToday = (i === this.todayDate && month === this.todayMonth && year === this.todayYear);

            days.push({
              date: i,
              booked: isBooked,
              event: eventName, 
              isToday: isToday
            });
          }

          while (days.length < 42) {
            days.push({ date: days.length - (firstDay + daysInMonth) + 1, booked: false, event: null, isToday: false });
          }

          return days;
        }
      },
      methods: {
        prevMonth() {
          if (this.currentMonth === 0) {
            this.currentMonth = 11;
            this.currentYear--;
          } else {
            this.currentMonth--;
          }
        },
        nextMonth() {
          if (this.currentMonth === 11) {
            this.currentMonth = 0;
            this.currentYear++;
          } else {
            this.currentMonth++;
          }
        },
        async fetchFacilities() {
            try {
                const res = await fetch('../../controllers/FasilitasController.php?action=readU');
                const text = await res.text();
                const data = JSON.parse(text);

                if(Array.isArray(data)) {
                    this.facilities = data;

                    if(this.facilities.length > 0) {
                        this.selectedPlace = this.facilities[0].nama; 
                    }
                }
            } catch(e) {
                console.error("Gagal load fasilitas", e);
            } finally {
                this.isLoadingFacilities = false;
            }
        },
        async fetchReservations() {
            try {
                const res = await fetch('../../controllers/ReservasiController.php?action=read');
                const text = await res.text();
                const data = JSON.parse(text);

                if(Array.isArray(data)) {
                    this.allReservations = data;
                }
            } catch(e) {
                console.error("Gagal load kalender reservasi", e);
            }
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