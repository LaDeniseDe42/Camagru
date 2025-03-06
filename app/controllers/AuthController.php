<?php

require_once __DIR__ . '/../models/User.php'; // Charger ton modèle utilisateur
require_once __DIR__ . '/../config/database.php'; // Configuration de la base de données

class AuthController {

    private $database;
    public function __construct() {
        $this->database = new Database();
    }
    
    public function register($username, $email, $password, $password2) {
        if (empty($username) || empty($email) || empty($password) || empty($password2)) {
            return ['status' => 'error', 'message' => "Tous les champs sont obligatoires."];
        }
    
        if ($password !== $password2) {
            return ['status' => 'error', 'message' => "Les mots de passe ne correspondent pas."];
        }
    
        if (strlen($password) < 6) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins 6 caractères."];
        }
    
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins une lettre majuscule et un chiffre."];
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => "Email invalide."];
        }
    
        $con = $this->database->getConnection();
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => "Cet email est déjà utilisé."];
        }
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userModel = new User();
        $isRegistered = $userModel->createUser($username, $email, $hashedPassword, $con);
    
        if ($isRegistered) {
            return ['status' => 'success', 'message' => "Inscription réussie ! Vous pouvez maintenant vous connecter."];
        } else {
            return ['status' => 'error', 'message' => "Une erreur s'est produite. Veuillez réessayer."];
        }
    }
    
    // D'autres méthodes comme login, logout, etc. peuvent être ici.

    public function login($email, $password) {
        // Validation basique
        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => "Tous les champs sont obligatoires."];
        }

        // Vérifie si l'email existe
        $con = $this->database->getConnection();
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['status' => 'error', 'message' => "Cet email n'existe pas."];
        }

        // Vérifie si le mot de passe est correct
        if (password_verify($password, $user['password'])) {
            return [
                'status' => 'success',
                'message' => "Connexion réussie !",
                'username' => $user['username'], // Récupère le nom d'utilisateur
                'user_id' => $user['id'] // Récupère l'id de l'utilisateur
            ];
        } else {
            return ['status' => 'error', 'message' => "Mot de passe incorrect."];
        }
    }
}

?>