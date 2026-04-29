<?php

require_once __DIR__ . '/../config/conn.php';

class UserModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE username = ? AND is_active = 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare(
            "SELECT id, nama, username, no_hp, role, is_active 
             FROM users WHERE role = 'Pegawai' ORDER BY id DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_active'] = $row['is_active'] == 1 ? true : false;
            $data[] = $row;
        }
        return $data;
    }

    public function createUser($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (nama, username, password, no_hp, role)
             VALUES (?, ?, ?, ?, 'Pegawai')"
        );
        $nama     = $data['nama'];
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $no_hp    = $data['no_hp'];

        $stmt->bind_param("ssss", $nama, $username, $password, $no_hp);
        return $stmt->execute();
    }

    public function updateUser($data) {
        $id       = (int)$data['id'];
        $nama     = $data['nama'];
        $username = $data['username'];
        $no_hp    = $data['no_hp'];

        if (!empty($data['password'])) {
            $stmt = $this->conn->prepare(
                "UPDATE users SET nama=?, username=?, no_hp=?, password=? WHERE id=?"
            );
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("ssssi", $nama, $username, $no_hp, $password, $id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE users SET nama=?, username=?, no_hp=? WHERE id=?"
            );
            $stmt->bind_param("sssi", $nama, $username, $no_hp, $id);
        }

        return $stmt->execute();
    }

    public function deleteUser($id) {
        $id = (int)$id;
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function toggleStatus($id, $status) {
        $id         = (int)$id;
        $status_val = $status ? 1 : 0;
        $stmt = $this->conn->prepare("UPDATE users SET is_active=? WHERE id=?");
        $stmt->bind_param("ii", $status_val, $id);
        return $stmt->execute();
    }
}
?>