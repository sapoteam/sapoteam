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
}
?>