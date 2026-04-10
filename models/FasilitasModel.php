<?php
require_once __DIR__ . '/../config/conn.php';

class FasilitasModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getAllFasilitas() {
        $query = "SELECT * FROM fasilitas ORDER BY id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAllFasilitasUser() {
        $query = "SELECT * FROM fasilitas WHERE status = 'Tersedia' ORDER BY id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getFasilitasById($id) {
        $id = (int)$id;
        $result = $this->conn->query("SELECT * FROM fasilitas WHERE id=$id");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function createFasilitas($data) {
        $nama = $this->conn->real_escape_string($data['nama']);
        $deskripsi = $this->conn->real_escape_string($data['deskripsi']);
        $harga = (int)$data['harga'];
        $status = $this->conn->real_escape_string($data['status']);
        $image = $this->conn->real_escape_string($data['image'] ?? '');

        $query = "INSERT INTO fasilitas (nama, deskripsi, harga, status, image) 
                VALUES ('$nama', '$deskripsi', $harga, '$status', '$image')";
        return $this->conn->query($query);
    }

    public function updateFasilitas($data) {
        $id = (int)$data['id'];
        $nama = $this->conn->real_escape_string($data['nama']);
        $deskripsi = $this->conn->real_escape_string($data['deskripsi']);
        $harga = (int)$data['harga'];
        $status = $this->conn->real_escape_string($data['status']);

        $query = "UPDATE fasilitas SET nama='$nama', deskripsi='$deskripsi', harga=$harga, status='$status'";

        if (!empty($data['image'])) {
            $image = $this->conn->real_escape_string($data['image']);
            $query .= ", image='$image'";
        }

        $query .= " WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteFasilitas($id) {
        $id = (int)$id;
        $query = "DELETE FROM fasilitas WHERE id=$id";
        return $this->conn->query($query);
    }
}
?>