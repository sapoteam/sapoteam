<?php
require_once __DIR__ . '/../config/conn.php';

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

    public function checkDoubleBooking($tanggal, $fasilitas_id, $exclude_id = null) {
        $tanggal = $this->conn->real_escape_string($tanggal);
        $fasilitas_id = (int)$fasilitas_id;

        $query = "SELECT id FROM reservasi WHERE tanggal='$tanggal' AND fasilitas_id=$fasilitas_id AND status != 'Dibatalkan'";

        if ($exclude_id !== null) {
            $exclude_id = (int)$exclude_id;
            $query .= " AND id != $exclude_id";
        }

        $result = $this->conn->query($query);
        return $result && $result->num_rows > 0; 
    }

    public function createReservasi($data) {
        $nama = $this->conn->real_escape_string($data['nama']);
        $no_hp = $this->conn->real_escape_string($data['noHp']);
        $fasilitas_id = (int)$data['fasilitas_id'];
        $tanggal = $this->conn->real_escape_string($data['tanggal']);
        $jumlah_orang = (int)$data['jumlah_orang'];
        $catatan = $this->conn->real_escape_string($data['catatan'] ?? '');
        $total_harga = (int)$data['total_harga'];
        $status = $this->conn->real_escape_string($data['status']);

        $query = "INSERT INTO reservasi (nama, no_hp, fasilitas_id, tanggal, jumlah_orang, catatan, total_harga, status) 
                VALUES ('$nama', '$no_hp', $fasilitas_id, '$tanggal', $jumlah_orang, '$catatan', $total_harga, '$status')";
        return $this->conn->query($query);
    }

    public function updateReservasi($data) {
        $id = (int)$data['id'];
        $nama = $this->conn->real_escape_string($data['nama']);
        $no_hp = $this->conn->real_escape_string($data['noHp']);
        $fasilitas_id = (int)$data['fasilitas_id'];
        $tanggal = $this->conn->real_escape_string($data['tanggal']);
        $jumlah_orang = (int)$data['jumlah_orang'];
        $catatan = $this->conn->real_escape_string($data['catatan'] ?? '');
        $total_harga = (int)$data['total_harga'];

        $query = "UPDATE reservasi SET nama='$nama', no_hp='$no_hp', fasilitas_id=$fasilitas_id, tanggal='$tanggal', jumlah_orang=$jumlah_orang, catatan='$catatan', total_harga=$total_harga WHERE id=$id";
        return $this->conn->query($query);
    }

    public function updateStatus($id, $status) {
        $id = (int)$id;
        $status = $this->conn->real_escape_string($status);
        $query = "UPDATE reservasi SET status='$status' WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteReservasi($id) {
        $id = (int)$id;
        $query = "DELETE FROM reservasi WHERE id=$id";
        return $this->conn->query($query);
    }
}
?>