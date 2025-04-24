<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ChangeInfo.php';

if ($_SESSION['csrf_token'] === null) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // header('Content-Type: application/json');
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Token CSRF invalide.']);
        exit;
    }
    $userId = $_SESSION['user_id'];
    $change = new ChangeInfo();

    // Modification du nom d'utilisateur
    if (isset($_POST['username'])) {
        // Modification du mot de passe mis ici a cause de chrome qui oblige a mettre username dans le champ
        if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
            $result = $change->updatePassword($password, $confirm);

            if ($result['status'] === 'success') {
                echo json_encode([
                    'status' => 'success',
                    'updatedValue' => '********',
                    'targetId' => 'password',
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
            exit;
        } else {
            $username = trim($_POST['username']);
            $result = $change->updateUsername($username, $userId);
            if ($result['status'] === 'success') {
                echo json_encode([
                    'status' => 'success',
                    'updatedValue' => $username,
                    'targetId' => 'username',
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
            exit;
        }
    }

    // Modification de l'email
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $result = $change->updateEmail($email);

        if ($result['status'] === 'success') {
            echo json_encode([
                'status' => 'success',
                'updatedValue' => $email,
                'targetId' => 'email',
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
        exit;
    }

    // Modification de la maison
    if (isset($_POST['house'])) {
        $house = trim($_POST['house']);
        $result = $change->updateHouse($house);

        if ($result['status'] === 'success') {
            echo json_encode([
                'status' => 'success',
                'updatedValue' => $house,
                'targetId' => 'house',
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
        exit;
    }


    // Modification de la préférence de notifications
    if (isset($_POST['want_email_notif'])) {
        $notif = $_POST['want_email_notif'];
        $result = $change->updateNotif($notif);

        if ($result['status'] === 'success') {
            $notifValue = $notif == 1 ? 'Oui' : 'Non';
            echo json_encode([
                'status' => 'success',
                'updatedValue' => $notifValue,
                'targetId' => 'notif',
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Requête invalide.']);
    exit;
}

// Récupération des infos utilisateur à afficher
$auth = new AuthController();
$userInfo = $auth->giveinfoConcernThisUser($_SESSION['user_id']);
$username = $userInfo['username'];
$user_mail = $userInfo['email'];
$house = $userInfo['house'];
$sub_house = strtolower($house);
$want_email_notif = $auth->wantEmailNotif($_SESSION['user_id']);
$want_email_notif = $want_email_notif == 1 ? 'Oui' : 'Non';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profil</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <link rel="stylesheet" href="/../assets/css/modalProfile.css">
</head>

<body id="house" class="Theme-<?= htmlspecialchars($house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . '/../Views/auth/profile.php'; ?>

    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/update_profile.js"></script>
    <script src="assets/js/navScript.js"></script>
</body>

</html>