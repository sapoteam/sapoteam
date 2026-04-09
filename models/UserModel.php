<?php

require_once __DIR__ . '/../config/conn.php';

class UserModel {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getUserByUsername($username) {

        $username = $this->conn->real_escape_string($username);

        $query = "SELECT * FROM users WHERE username = '$username' AND is_active = 1";
        $result = $this->conn->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); 

        }
        return false; 

    }
        public function getAllUsers() {
        $query = "SELECT id, nama, username, no_hp, role, is_active FROM users WHERE role = 'Pegawai' ORDER BY id DESC;";
        $result = $this->conn->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $row['is_active'] = $row['is_active'] == 1 ? true : false;
                $data[] = $row;
            }
        }
        return $data;
    }

    public function createUser($data) {
        $nama = $this->conn->real_escape_string($data['nama']);
        $username = $this->conn->real_escape_string($data['username']);
        $no_hp = $this->conn->real_escape_string($data['no_hp']);

        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO users (nama, username, password, no_hp, role) VALUES ('$nama', '$username', '$password', '$no_hp', 'Pegawai')";
        return $this->conn->query($query);
    }

    public function updateUser($data) {
        $id = (int)$data['id'];
        $nama = $this->conn->real_escape_string($data['nama']);
        $username = $this->conn->real_escape_string($data['username']);
        $no_hp = $this->conn->real_escape_string($data['no_hp']);

        $query = "UPDATE users SET nama='$nama', username='$username', no_hp='$no_hp'";

        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $query .= ", password='$password'";
        }

        $query .= " WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteUser($id) {
        $id = (int)$id;
        $query = "DELETE FROM users WHERE id=$id";
        return $this->conn->query($query);
    }

    public function toggleStatus($id, $status) {
        $id = (int)$id;
        $status_val = $status ? 1 : 0;
        $query = "UPDATE users SET is_active=$status_val WHERE id=$id";
        return $this->conn->query($query);
    }
}
?>