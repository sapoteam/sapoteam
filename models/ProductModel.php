<?php
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
        $stmt = $this->conn->prepare("SELECT * FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function createProduct($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO produk (nama, kategori, harga, deskripsi, status, image) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $nama      = $data['nama'] ?? '';
        $kategori  = $data['kategori'] ?? '';
        $harga     = (int)($data['harga'] ?? 0);
        $deskripsi = $data['deskripsi'] ?? '';
        $status    = $data['status'] ?? 'Tersedia';
        $image     = $data['image'] ?? '';

        $stmt->bind_param("ssisss", $nama, $kategori, $harga, $deskripsi, $status, $image);
        return $stmt->execute();
    }

    public function updateProduct($data) {
        $id        = (int)($data['id'] ?? 0);
        $nama      = $data['nama'] ?? '';
        $kategori  = $data['kategori'] ?? '';
        $harga     = (int)($data['harga'] ?? 0);
        $deskripsi = $data['deskripsi'] ?? '';
        $status    = $data['status'] ?? 'Tersedia';

        if (!empty($data['image'])) {
            $image = $data['image'];
            $stmt = $this->conn->prepare(
                "UPDATE produk SET nama=?, kategori=?, harga=?, deskripsi=?, status=?, image=? WHERE id=?"
            );
            $stmt->bind_param("ssisssi", $nama, $kategori, $harga, $deskripsi, $status, $image, $id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE produk SET nama=?, kategori=?, harga=?, deskripsi=?, status=? WHERE id=?"
            );
            $stmt->bind_param("sissi", $nama, $kategori, $harga, $deskripsi, $status, $id);
        }

        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function toggleStatus($id, $status) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("UPDATE produk SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}
?>