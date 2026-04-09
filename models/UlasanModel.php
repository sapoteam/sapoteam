<?php
require_once __DIR__ . '/../config/conn.php';

class UlasanModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    // public section
public function getApprovedUlasan() {
        $query = "SELECT * FROM ulasan WHERE status = 'Approved' ORDER BY id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $row['tanggal_format'] = date('d F Y', strtotime($row['tanggal']));

                $ulasan_id = $row['id'];
                $foto_query = "SELECT foto_url FROM ulasan_foto WHERE ulasan_id = $ulasan_id";
                $foto_result = $this->conn->query($foto_query);

                $fotos = [];
                if ($foto_result && $foto_result->num_rows > 0) {
                    while ($f = $foto_result->fetch_assoc()) {
                        $fotos[] = $f['foto_url'];
                    }
                }
                $row['foto'] = $fotos; 

                $data[] = $row;
            }
        }
        return $data;
    }

    public function createUlasan($data, $fotoUrls) {
        $nama = $this->conn->real_escape_string($data['nama']);
        $rating = (int)$data['rating'];

        $komentar = $this->conn->real_escape_string($data['komentar']); 
        $tanggal_sekarang = date('Y-m-d'); 

        $query = "INSERT INTO ulasan (nama, rating, komentar, status, tanggal) 
                  VALUES ('$nama', $rating, '$komentar', 'Pending', '$tanggal_sekarang')";

        if ($this->conn->query($query)) {
            $ulasan_id = $this->conn->insert_id; 

            if (!empty($fotoUrls) && is_array($fotoUrls)) {
                foreach ($fotoUrls as $url) {
                    $url_escaped = $this->conn->real_escape_string($url);
                    $this->conn->query("INSERT INTO ulasan_foto (ulasan_id, foto_url) VALUES ($ulasan_id, '$url_escaped')");
                }
            }
            return true;
        }
        return false;
    }


    public function getAllUlasan() {
        $query = "SELECT * FROM ulasan ORDER BY id DESC";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['tanggal'] = date('d M Y H:i', strtotime($row['created_at']));

                $row['foto'] = json_decode($row['foto'], true) ?: []; 
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getUlasanById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM ulasan WHERE id = $id";
        $result = $this->conn->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // admin section

    public function updateStatus($id, $status) {
        $id = (int)$id;
        $status = $this->conn->real_escape_string($status);
        $query = "UPDATE ulasan SET status='$status' WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteUlasan($id) {
        $id = (int)$id;
        $query = "DELETE FROM ulasan WHERE id=$id";
        return $this->conn->query($query);
    }
}
?>