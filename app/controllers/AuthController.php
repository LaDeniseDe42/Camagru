<?php

require_once __DIR__ . '/../models/User.php'; // Charger ton modèle utilisateur
require_once __DIR__ . '/../config/database.php'; // Configuration de la base de données

class AuthController {

    private $database;
    public function __construct() {
        $this->database = new Database();
    }
    
    // Fonction d'inscription
    public function register($username, $email, $password, $password2) {

        // Validation basique (tu peux ajouter plus de validation ici)
        if (empty($username) || empty($email) || empty($password) || empty($password2)) {
            return "Tous les champs sont obligatoires.";
        }

        if ($password != $password2) {
            return "Les mots de passe ne correspondent pas.";
        }

        // Vérifie si l'email est déjà utilisé
        $con = $this->database->getConnection();
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        // Vérifie si l'email existe déjà
        if ($stmt->rowCount() > 0) {
            return "Cet email est déjà utilisé.";
        }

        // Hash du mot de passe avant de l'enregistrer
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userModel = new User();
        // Création de l'utilisateur dans la base de données
        $isRegistered = $userModel->createUser($username, $email, $hashedPassword, $con);    
        if ($isRegistered) {
            return "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            return "Une erreur s'est produite. Veuillez réessayer.";
        }
    }
    
    // D'autres méthodes comme login, logout, etc. peuvent être ici.
}

?>