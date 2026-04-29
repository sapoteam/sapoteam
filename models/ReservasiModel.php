<?php

class ReservasiModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getAllReservasi() {
        $query = "SELECT r.*, f.nama as lokasi_nama 
                  FROM reservasi r 
                  LEFT JOIN fasilitas f ON r.fasilitas_id = f.id 
                  ORDER BY r.id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Public endpoint — hanya return data minimal untuk kalender & flatpickr
     */
    public function getBookedDates() {
        $stmt = $this->conn->prepare(
            "SELECT r.tanggal, r.fasilitas_id, r.status, f.nama as lokasi_nama
             FROM reservasi r
             LEFT JOIN fasilitas f ON r.fasilitas_id = f.id
             WHERE r.status != 'Dibatalkan'
             ORDER BY r.tanggal ASC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function checkDoubleBooking($tanggal, $fasilitas_id, $exclude_id = null) {
        $fasilitas_id = (int)$fasilitas_id;

        if ($exclude_id !== null) {
            $exclude_id = (int)$exclude_id;
            $stmt = $this->conn->prepare(
                "SELECT id FROM reservasi 
                 WHERE tanggal=? AND fasilitas_id=? AND status='Lunas' AND id!=?"
            );
            $stmt->bind_param("sii", $tanggal, $fasilitas_id, $exclude_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT id FROM reservasi 
                 WHERE tanggal=? AND fasilitas_id=? AND status='Lunas'"
            );
            $stmt->bind_param("si", $tanggal, $fasilitas_id);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createReservasi($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO reservasi (nama, no_hp, fasilitas_id, tanggal, jumlah_orang, catatan, total_harga, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $nama         = $data['nama'];
        $no_hp        = $data['noHp'] ?? $data['no_hp'] ?? '';
        $fasilitas_id = (int)$data['fasilitas_id'];
        $tanggal      = $data['tanggal'];
        $jumlah_orang = (int)$data['jumlah_orang'];
        $catatan      = $data['catatan'] ?? '';
        $total_harga  = (int)$data['total_harga'];
        $status       = $data['status'] ?? 'Menunggu Review';

        $stmt->bind_param("ssisisis",
            $nama, $no_hp, $fasilitas_id, $tanggal, $jumlah_orang, $catatan, $total_harga, $status
        );

        return $stmt->execute();
    }

    public function updateReservasi($data) {
        $stmt = $this->conn->prepare(
            "UPDATE reservasi 
             SET nama=?, no_hp=?, fasilitas_id=?, tanggal=?, jumlah_orang=?, catatan=?, total_harga=?
             WHERE id=?"
        );

        $id           = (int)$data['id'];
        $nama         = $data['nama'];
        $no_hp        = $data['noHp'] ?? $data['no_hp'] ?? '';
        $fasilitas_id = (int)$data['fasilitas_id'];
        $tanggal      = $data['tanggal'];
        $jumlah_orang = (int)$data['jumlah_orang'];
        $catatan      = $data['catatan'] ?? '';
        $total_harga  = (int)$data['total_harga'];

        $stmt->bind_param("ssisisii",
            $nama, $no_hp, $fasilitas_id, $tanggal, $jumlah_orang, $catatan, $total_harga, $id
        );

        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE reservasi SET status=? WHERE id=?");
        $id = (int)$id;
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function deleteReservasi($id) {
        $stmt = $this->conn->prepare("DELETE FROM reservasi WHERE id=?");
        $id = (int)$id;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getLastError() {
        return $this->conn->error;
    }
}
?>