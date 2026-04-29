<?php

class FasilitasModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getAllFasilitas() {
        $stmt = $this->conn->prepare("SELECT * FROM fasilitas WHERE status != 'Dihapus' ORDER BY id DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllFasilitasUser() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM fasilitas WHERE status = 'Tersedia' ORDER BY id DESC"
        );
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getFasilitasById($id) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("SELECT * FROM fasilitas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function createFasilitas($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO fasilitas (nama, deskripsi, harga, status, image)
             VALUES (?, ?, ?, ?, ?)"
        );
        $nama      = $data['nama'] ?? '';
        $deskripsi = $data['deskripsi'] ?? '';
        $harga     = (int)($data['harga'] ?? 0);
        $status    = $data['status'] ?? 'Tersedia';
        $image     = $data['image'] ?? '';

        $stmt->bind_param("ssiss", $nama, $deskripsi, $harga, $status, $image);
        return $stmt->execute();
    }

    public function updateFasilitas($data) {
        $id        = (int)$data['id'];
        $nama      = $data['nama'] ?? '';
        $deskripsi = $data['deskripsi'] ?? '';
        $harga     = (int)($data['harga'] ?? 0);
        $status    = $data['status'] ?? 'Tersedia';

        if (!empty($data['image'])) {
            $stmt = $this->conn->prepare(
                "UPDATE fasilitas SET nama=?, deskripsi=?, harga=?, status=?, image=? WHERE id=?"
            );
            $image = $data['image'];
            $stmt->bind_param("ssissi", $nama, $deskripsi, $harga, $status, $image, $id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE fasilitas SET nama=?, deskripsi=?, harga=?, status=? WHERE id=?"
            );
            $stmt->bind_param("ssisi", $nama, $deskripsi, $harga, $status, $id);
        }

        return $stmt->execute();
    }

    public function deleteFasilitas($id) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("UPDATE fasilitas SET status = 'Dihapus' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function isNameExists($nama, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT id FROM fasilitas WHERE nama = ? AND status != 'Dihapus' AND id != ?");
            $stmt->bind_param("si", $nama, $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM fasilitas WHERE nama = ? AND status != 'Dihapus'");
            $stmt->bind_param("s", $nama);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>