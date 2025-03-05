<?php
require_once __DIR__ . "/../config/database.php";

class User {
    // Crée un nouvel utilisateur
    public function createUser($username, $email, $password, $dbConnection) {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $dbConnection->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        return $stmt->execute();
    }
}
?>