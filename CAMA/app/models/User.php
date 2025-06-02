<?php

class User
{

    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }

    // Crée un nouvel utilisateur
    public function createUser($username, $email, $password, $house, $token)
    {
        $query = "INSERT INTO users (username, email, password, house, confirmation_token, is_confirmed) VALUES (:username, :email, :password, :house, :token, 0)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':house', $house);

        return $stmt->execute();
    }

    public function userExist($user_id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur S'IL existe
    }

    public function getUsername($userId)
    {
        $query = "SELECT username FROM users WHERE id = :id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['username'] : null;
    }
}
