<?php
require_once __DIR__ . '/../config/conn.php';

class ProductModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM produk ORDER BY id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function getProductById($id) {
        $id = (int)$id;
        $result = $this->conn->query("SELECT * FROM produk WHERE id=$id");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function createProduct($data) {
        $nama = $this->conn->real_escape_string($data['nama']);
        $kategori = $this->conn->real_escape_string($data['kategori']);
        $harga = (int)$data['harga'];
        $deskripsi = $this->conn->real_escape_string($data['deskripsi']);
        $status = $this->conn->real_escape_string($data['status']);
        $image = $this->conn->real_escape_string($data['image'] ?? '');

        $query = "INSERT INTO produk (nama, kategori, harga, deskripsi, status, image) 
                VALUES ('$nama', '$kategori', $harga, '$deskripsi', '$status', '$image')";
        return $this->conn->query($query);
    }

    public function updateProduct($data) {
        $id = (int)$data['id'];
        $nama = $this->conn->real_escape_string($data['nama']);
        $kategori = $this->conn->real_escape_string($data['kategori']);
        $harga = (int)$data['harga'];
        $deskripsi = $this->conn->real_escape_string($data['deskripsi']);
        $status = $this->conn->real_escape_string($data['status']);

        $query = "UPDATE produk SET nama='$nama', kategori='$kategori', harga=$harga, deskripsi='$deskripsi', status='$status'";

        if (!empty($data['image'])) {
            $image = $this->conn->real_escape_string($data['image']);
            $query .= ", image='$image'";
        }

        $query .= " WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteProduct($id) {
        $id = (int)$id;
        $query = "DELETE FROM produk WHERE id=$id";
        return $this->conn->query($query);
    }

    public function toggleStatus($id, $status) {
        $id = (int)$id;
        $status_val = $this->conn->real_escape_string($status);
        $query = "UPDATE produk SET status='$status_val' WHERE id=$id";
        return $this->conn->query($query);
    }
}
?>