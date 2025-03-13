<?php
// require_once __DIR__ . "/../config/database.php";

// class User {
//     // Crée un nouvel utilisateur
//     public function createUser($username, $email, $password, $house, $dbConnection, $token) {
//         $query = "INSERT INTO users (username, email, password, house, confirmation_token, is_confirmed) VALUES (:username, :email, :password, :house, :token, 0)";
//         $stmt = $dbConnection->prepare($query);
//         $stmt->bindParam(':username', $username);
//         $stmt->bindParam(':email', $email);
//         $stmt->bindParam(':password', $password);
//         $stmt->bindParam(':token', $token);
//         $stmt->bindParam(':house', $house);

//         return $stmt->execute();
//     }
// }


class User {
    private $pdo;
    private $user_id; // Déclaration de la propriété user_id

    public function __construct($user_id) {
        // Connexion à la base de données ici avec les credentials de l'utilisateur
        $this->pdo = (new Database())->getConnection(); // Utilisation de la connexion PDO depuis la classe Database
        $this->user_id = $user_id;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function createUser($username, $email, $password, $house, $token) {
        $query = "INSERT INTO users (username, email, password, house, confirmation_token, is_confirmed) VALUES (:username, :email, :password, :house, :token, 0)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':house', $house);

        return $stmt->execute();
    }
        // Getter pour user_id
        public function getUserId() {
            return $this->user_id;
        }
}
?>
