<?php

require_once __DIR__ . '/../models/User.php'; // Charger ton modèle utilisateur
require_once __DIR__ . '/../config/database.php'; // Configuration de la base de données

class AuthController {

    private $database;
    public function __construct() {
        $this->database = new Database();
    }
    
    public function register($username, $email, $password, $password2, $house) {
        if (empty($username) || empty($email) || empty($password) || empty($password2)) {
            return ['status' => 'error', 'message' => "Tous les champs sont obligatoires."];
        }
        if ($username === $password) {
            return ['status' => 'error', 'message' => "Le nom d'utilisateur et le mot de passe ne peuvent pas être identiques."];
        }
        if (strlen($username) < 3 || strlen($username) > 16) {
            return ['status' => 'error', 'message' => "Le nom d'utilisateur doit contenir entre 4 et 16 caractères."];
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            return ['status' => 'error', 'message' => "Le nom d'utilisateur ne peut contenir que des lettres et des chiffres."];
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
        if (!in_array($house, ['gryffondor', 'poufsouffle', 'serdaigle', 'serpentard', 'Moldu', 'Crakmol'])) {
            return ['status' => 'error', 'message' => "Maison invalide."];
        }
        $con = $this->database->getConnection();
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => "Cet email est déjà utilisé."];
        }
        //verifier si l'username est bien unique
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => "Cet username est déjà utilisé."];
        }
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        //avant de creer un nouvel user, demander une confirmation par mail
        $token = bin2hex(random_bytes(50));

        $userModel = new User($con);
        $isRegistered = $userModel->createUser($username, $email, $hashedPassword, $house, $token);
        error_log($isRegistered);
    
        if ($isRegistered) {
            // Envoyer l'email de confirmation
            $confirmLink = "http://localhost:8080/confirm_email.php?token=$token";
            $subject = "Confirmez votre inscription sur Camagru";
            $message = "Cliquez sur ce lien pour confirmer votre email : <a href='$confirmLink'>$confirmLink</a>";
            $headers = "Content-Type: text/html; charset=UTF-8";

            if (mail($email, $subject, $message, $headers)) {
                return ['status' => 'success', 'message' => "Inscription réussie ! Vérifiez votre email pour activer votre compte."];
            } else {
                return ['status' => 'error', 'message' => "Erreur lors de l'envoi du mail de confirmation."];
            }
        } else {
            return ['status' => 'error', 'message' => "Une erreur s'est produite. Veuillez réessayer."];
        }
    }

    public function giveinfoConcernThisUser($user_id) {
        $userModel = new User($this->database->getConnection());
        $this_user = $userModel->userExist($user_id);
        return $this_user;
    }


    public function confirmEmail($token) {
        $con = $this->database->getConnection();
        $query = "UPDATE users SET is_confirmed = 1 WHERE confirmation_token = :token";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['status' => 'success', 'message' => "Votre compte est maintenant activé !"];
        } else {
            return ['status' => 'error', 'message' => "Lien invalide ou compte déjà activé."];
        }
    }

    public function resend_pass($email)
{
    if (empty($email)) {
        return ['status' => 'error', 'message' => "L'email est requis."];
    }

    $con = $this->database->getConnection();
    // Vérifier si l'email existe
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ['status' => 'error', 'message' => "Cet email n'existe pas."];
    }

    // Générer un token sécurisé
    $token = bin2hex(random_bytes(50));
    // Mettre à jour la base de données avec le token
    $query = "UPDATE users SET confirmation_token = :token WHERE email = :email";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Envoyer l'email de confirmation
        $resetLink = "http://localhost:8080/reset_password.php?token=$token";
        $subject = "Vérification de la réinitialisation de votre mot de passe";
        $message = "
            <html>
            <head>
                <title>Réinitialisation de votre mot de passe</title>
            </head>
            <body>
                <p>Bonjour,</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour confirmer votre demande :</p>
                <p><a href='$resetLink'>Réinitialiser mon mot de passe</a></p>
                <p>Ce lien expirera dans 1 heure.</p>
                <p>Si vous n'avez pas fait cette demande, ignorez cet email.</p>
            </body>
            </html>
        ";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@votre-site.com" . "\r\n";

        if (mail($email, $subject, $message, $headers)) {
            return ['status' => 'success', 'message' => "Un email de confirmation a été envoyé. Vérifiez votre boîte de réception."];
        } else {
            return ['status' => 'error', 'message' => "Erreur lors de l'envoi de l'email."];
        }
    } else {
        return ['status' => 'error', 'message' => "Une erreur s'est produite. Veuillez réessayer."];
    }
}

    public function reset_password($password, $token) {
        if (empty($password)) {
            return ['status' => 'error', 'message' => "Le mot de passe est requis."];
        }
        if (strlen($password) < 6) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins 6 caractères."];
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ['status' => 'error', 'message' => "Le mot de passe doit contenir au moins une lettre majuscule et un chiffre."];
        }

        $con = $this->database->getConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE confirmation_token = :token";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':token', $token); 
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => 'success', 'message' => "Mot de passe mis à jour !"];
        } else {
            return ['status' => 'error', 'message' => "Lien invalide."];
        }
    }

    

    public function resend_mail($email) {
        // Validation basique
        if (empty($email)) {
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
        if ($user['is_confirmed'] == 1) {
            return ['status' => 'error', 'message' => "Votre compte est déjà activé."];
        }
        else
        {
            $token = bin2hex(random_bytes(50));
            $query = "UPDATE users SET confirmation_token = :token WHERE email = :email";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Envoyer l'email de confirmation
                $confirmLink = "http://localhost:8080/confirm_email.php?token=$token";
                $subject = "Confirmez votre inscription sur Camagru";
                $message = "Cliquez sur ce lien pour confirmer votre email : <a href='$confirmLink'>$confirmLink</a>";
                $headers = "Content-Type: text/html; charset=UTF-8";
                if (mail($email, $subject, $message, $headers)) {
                    return ['status' => 'success', 'message' => "Email de confirmation renvoyé ! Vérifiez votre boîte de réception."];
                } else {
                    return ['status' => 'error', 'message' => "Erreur lors de l'envoi du mail de confirmation."];
                }
            } else {
                return ['status' => 'error', 'message' => "Une erreur s'est produite. Veuillez réessayer."];
            }
        }
    }

    public function login($emailorUsername, $password) {
        // Validation basique
        if (empty($emailorUsername) || empty($password)) {
            return ['status' => 'error', 'message' => "Tous les champs sont obligatoires."];
        }
        $email = null;
        $username = null;
        //verifier si emailOrUsername est un email
        if (filter_var($emailorUsername, FILTER_VALIDATE_EMAIL)) {
            $email = $emailorUsername;
        } else {
            $username = $emailorUsername;
        }
        if ($email !== null) {
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
            if ($user['is_confirmed'] == 0) {
                return ['status' => 'error', 'message' => "Veuillez confirmer votre email avant de vous connecter."];
            }    
        }
        else if ($username !== null) {
            // Vérifie si l'username existe
            $con = $this->database->getConnection();
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                return ['status' => 'error', 'message' => "Cet username n'existe pas."];
            }
            if ($user['is_confirmed'] == 0) {
                return ['status' => 'error', 'message' => "Veuillez confirmer votre email avant de vous connecter."];
            }    
        }
        // Vérifie si le mot de passe est correct
        if (password_verify($password, $user['password'])) {
            return [
                'status' => 'success',
                'message' => "Connexion réussie !",
                'username' => $user['username'], // Récupère le nom d'utilisateur
                'user_id' => $user['id'], // Récupère l'id de l'utilisateur
                'house' => $user['house'], // Récupère la maison de l'utilisateur
                'email' => $user['email'] // Récupère l'email de l'utilisateur
            ];
        } else {
            return ['status' => 'error', 'message' => "Mot de passe incorrect."];
        }
    }
}

?>