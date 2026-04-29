<?php

require_once __DIR__ . '/../config/conn.php';

class UlasanModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    // ── PUBLIC ──

    public function getApprovedUlasan() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM ulasan WHERE status = 'Approved' ORDER BY id DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['tanggal_format'] = date('d F Y', strtotime($row['tanggal']));
            $row['foto'] = $this->getFotoByUlasanId($row['id']);
            $data[] = $row;
        }
        return $data;
    }

    public function createUlasan($data, $fotoUrls) {
        $stmt = $this->conn->prepare(
            "INSERT INTO ulasan (nama, rating, komentar, status, tanggal)
             VALUES (?, ?, ?, 'Pending', ?)"
        );
        $nama     = $data['nama'];
        $rating   = (int)$data['rating'];
        $komentar = $data['komentar'];
        $tanggal  = date('Y-m-d');

        $stmt->bind_param("siss", $nama, $rating, $komentar, $tanggal);

        if (!$stmt->execute()) return false;

        $ulasan_id = $this->conn->insert_id;

        if (!empty($fotoUrls) && is_array($fotoUrls)) {
            $stmt_foto = $this->conn->prepare(
                "INSERT INTO ulasan_foto (ulasan_id, foto_url) VALUES (?, ?)"
            );
            foreach ($fotoUrls as $url) {
                $stmt_foto->bind_param("is", $ulasan_id, $url);
                $stmt_foto->execute();
            }
        }

        return true;
    }

    // ── ADMIN ──

    public function getAllUlasan() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM ulasan ORDER BY id DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['tanggal_format'] = date('d M Y H:i', strtotime($row['created_at']));
            $row['foto'] = $this->getFotoByUlasanId($row['id']);
            $data[] = $row;
        }
        return $data;
    }

    public function getUlasanById($id) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("SELECT * FROM ulasan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $row['foto'] = $this->getFotoByUlasanId($id);
            return $row;
        }
        return null;
    }

    public function updateStatus($id, $status) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("UPDATE ulasan SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function deleteUlasan($id) {
        $id = (int)$id;

        $stmt = $this->conn->prepare("DELETE FROM ulasan_foto WHERE ulasan_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt2 = $this->conn->prepare("DELETE FROM ulasan WHERE id=?");
        $stmt2->bind_param("i", $id);
        return $stmt2->execute();
    }

    // ── PRIVATE HELPER ──

    private function getFotoByUlasanId($ulasan_id) {
        $ulasan_id = (int)$ulasan_id;
        $stmt = $this->conn->prepare(
            "SELECT foto_url FROM ulasan_foto WHERE ulasan_id = ?"
        );
        $stmt->bind_param("i", $ulasan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fotos = [];
        while ($f = $result->fetch_assoc()) {
            $fotos[] = $f['foto_url'];
        }
        return $fotos;
    }
}
?>