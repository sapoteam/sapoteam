<?php
require_once __DIR__ . '/../config/conn.php';

class DashboardModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getDashboardData() {
        $data = [
            'totalReservasi' => 0,
            'ulasanMenunggu' => 0,
            'produkAktif' => 0,
            'statusPanen' => false,
            'recentReservations' => []
        ];

        $q_res = $this->conn->query("SELECT COUNT(*) as total FROM reservasi WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE()) AND status != 'Dibatalkan'");
        if ($q_res) $data['totalReservasi'] = $q_res->fetch_assoc()['total'];

        $q_ulasan = $this->conn->query("SELECT COUNT(*) as total FROM ulasan WHERE status = 'Pending'");
        if ($q_ulasan) $data['ulasanMenunggu'] = $q_ulasan->fetch_assoc()['total'];

        $q_prod = $this->conn->query("SELECT COUNT(*) as total FROM produk WHERE status = 'Tersedia'");
        if ($q_prod) $data['produkAktif'] = $q_prod->fetch_assoc()['total'];

        $q_panen = $this->conn->query("SELECT is_panen FROM status_panen LIMIT 1");
        if ($q_panen && $q_panen->num_rows > 0) {
            $data['statusPanen'] = $q_panen->fetch_assoc()['is_panen'] == 1;
        }

        $q_recent = $this->conn->query("SELECT r.*, f.nama as lokasi_nama FROM reservasi r LEFT JOIN fasilitas f ON r.fasilitas_id = f.id WHERE r.status = 'Menunggu Review' ORDER BY r.id DESC LIMIT 5");
        if ($q_recent && $q_recent->num_rows > 0) {
            while ($row = $q_recent->fetch_assoc()) {
                $row['tanggal_format'] = date('d M Y', strtotime($row['tanggal']));
                $data['recentReservations'][] = $row;
            }
        }

        return $data;
    }

    public function togglePanen($status) {
        $nilai = $status ? 1 : 0;
        return $this->conn->query("UPDATE status_panen SET is_panen = $nilai");
    }
}
?>