<?php

class Database {
    private $host = "localhost";
    private $db_name = "mysql_db"; // Nom de ta base de données
    private $username = "root"; // Nom d'utilisateur de ta base de données
    private $password = ""; // Mot de passe de la base de données (ou définis-le si tu en as un)
    private $conn;

    // Méthode pour obtenir la connexion PDO
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Configure le PDO pour gérer les erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Erreur de connexion à la base de données: " . $exception->getMessage();
        }

        return $this->conn;
    }
}