<?php
// require_once __DIR__ . "/../config/database.php";

class User {

    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function getConnection() {
        return $this->dbConnection;
    }

    // Crée un nouvel utilisateur
    public function createUser($username, $email, $password, $house, $token) {
        $query = "INSERT INTO users (username, email, password, house, confirmation_token, is_confirmed) VALUES (:username, :email, :password, :house, :token, 0)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':house', $house);

        return $stmt->execute();
    }
}

?>
