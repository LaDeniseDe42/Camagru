<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

class ChangeInfo
{
    private $database;
    private $con;

    public function __construct()
    {
        $this->database = new Database();
        $this->con = $this->database->getConnection();
    }

    public function updateUsername($username, $userId)
    {
        if (!$this->basicVerif($username)) {
            return ['status' => 'error', 'message' => "Veuillez renseigner un nom d'utilisateur."];
        }
        if (strlen($username) < 3 || strlen($username) >= 17) {
            return ['status' => 'error', 'message' => "Votre nom d'utilisateur doit contenir entre 4 et 16 caractères."];
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            return ['status' => 'error', 'message' => "Le nom d'utilisateur ne peut contenir que des lettres et des chiffres."];
        }
        // Vérifier si le nom d'utilisateur existe déjà
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => "Ce nom d'utilisateur est déjà utilisé."];
        }

        $query = "UPDATE users SET username = :username WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $_SESSION['user_id']);

        try {
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                return ['status' => 'success', 'message' => "Nom d'utilisateur mis à jour avec succès."];
            } else {
                return ['status' => 'error', 'message' => "Erreur lors de la mise à jour."];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => "Erreur SQL : " . $e->getMessage()];
        }
    }

    public function updateEmail($email)
    {
        if (!$this->basicVerif($email)) {
            return ['status' => 'error', 'message' => "Veuillez renseigner un email."];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => "Veuillez renseigner un email valide."];
        }

        // Vérifier si l'email existe déjà
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => "Cet email est déjà utilisé."];
        }

        // Mettre à jour l'email
        $query = "UPDATE users SET email = :email WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $_SESSION['user'] = $email;
            return ['status' => 'success', 'message' => "Email mis à jour avec succès."];
        } else {
            return ['status' => 'error', 'message' => "Erreur lors de la mise à jour."];
        }
    }

    public function updateHouse($house)
    {
        if (!$this->basicVerif($house)) {
            return ['status' => 'error', 'message' => "Veuillez renseigner une maison."];
        }
        if (!in_array($house, ['Gryffondor', 'Poufsouffle', 'Serdaigle', 'Serpentard', 'Moldu', 'Crakmol'])) {
            return ['status' => 'error', 'message' => "Maison invalide."];
        }

        $query = "UPDATE users SET house = :house WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':house', $house);
        $stmt->bindParam(':id', $_SESSION['user_id']);


        if ($stmt->execute()) {
            $_SESSION['house'] = $house;
            return ['status' => 'success', 'message' => "Maison mise à jour avec succès."];
        } else {
            return ['status' => 'error', 'message' => "Erreur lors de la mise à jour."];
        }
    }

    public function updatePassword($password, $newPassword)
    {
        if (!$this->basicVerif($password) || !$this->basicVerif($newPassword)) {
            return ['status' => 'error', 'message' => "Veuillez renseigner tous les champs."];
        }
        if ($password !== $newPassword) {
            return ['status' => 'error', 'message' => "Les mots de passe ne correspondent pas."];
        }
        if (empty($password)) {
            return ['status' => 'error', 'message' => "Le mot de passe est requis."];
        }
        if (strlen($password) < 6) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins 6 caractères."];
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins une lettre majuscule et un chiffre."];
        }

        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => "Mot de passe mis à jour avec succès."];
        } else {
            return ['status' => 'error', 'message' => "Erreur lors de la mise à jour."];
        }
    }

    private function basicVerif($variable)
    {
        return !empty($variable);
    }

    public function wantEmailNotif($user_id)
    {
        $query = "SELECT notifications FROM users WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        return $stmt->fetchColumn(); // Retourne uniquement la valeur de la colonne "notifications"
    }

    public function updateNotif($want_email_notif)
    {
        $query = "UPDATE users SET notifications = :notifications WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':notifications', $want_email_notif);
        $stmt->bindParam(':id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => "Préférences de notification mises à jour avec succès."];
        } else {
            return ['status' => 'error', 'message' => "Erreur lors de la mise à jour."];
        }
    }
}
