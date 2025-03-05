<?php
require_once __DIR__ . "/../config/database.php";

class User {
    private $db;

    public function __construct() {
        // Connection à la base de données via PDO
        $this->db = new Database();
    }

    // Vérifie si l'email est déjà utilisé
    public function emailExists($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function verifPasswordAtRegistration($password, $password_confirm) {
        if ($password != $password_confirm) {
            return "Les mots de passe ne correspondent pas.";
        }
    }

    // Crée un nouvel utilisateur
    public function createUser($username, $email, $password) {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        return $stmt->execute();
    }
}
?>