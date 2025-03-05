<?php

require_once __DIR__ . '/../models/User.php'; // Charger ton modèle utilisateur
require_once __DIR__ . '/../config/database.php'; // Configuration de la base de données

class AuthController {
    
    // Fonction d'inscription
    public function register($username, $email, $password) {
        // Validation basique (tu peux ajouter plus de validation ici)
        if (empty($username) || empty($email) || empty($password)) {
            return "Tous les champs sont obligatoires.";
        }

        // Vérification si l'email est déjà utilisé
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            return "L'email est déjà utilisé.";
        }

        // Hash du mot de passe avant de l'enregistrer
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Création de l'utilisateur dans la base de données
        $isRegistered = $userModel->createUser($username, $email, $hashedPassword);
        
        if ($isRegistered) {
            return "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            return "Une erreur s'est produite. Veuillez réessayer.";
        }
    }
    
    // D'autres méthodes comme login, logout, etc. peuvent être ici.
}

?>