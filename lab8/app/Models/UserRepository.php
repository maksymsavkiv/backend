<?php
require_once __DIR__ . '/../../bootstrap.php';
class UserRepository {
    private PDO $db;
    public function __construct() {$this->db=database();}
    public function find(string $username): array|false{
    $sql = "SELECT id, username, password_hash, role
            FROM users
            WHERE username = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$username]);
    return $stmt->fetch();
}
public function create(string $username,string $hash): int {
    $stmt=$this->db->prepare('INSERT INTO users(username,password_hash) VALUES (?,?)');
    $stmt->execute([$username,$hash]);return (int)$this->db->lastInsertId();
    }
}
