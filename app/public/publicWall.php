<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/User.php"; // Inclure la classe User

session_start(); // Démarrer la session pour vérifier si l'utilisateur est connecté

// Vérifier si l'utilisateur est connecté (en fonction de ta gestion des sessions)
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}

// Créer une instance de la classe User pour récupérer la connexion
$user = new User($_SESSION['user_id']); // Assure-toi que $_SESSION['user_id'] contient l'ID de l'utilisateur connecté

// Obtenir la connexion PDO depuis l'instance de l'utilisateur
$pdo = $user->getConnection();
//recupere la dbConnection de l'user


// Gérer l'upload de la photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $uploadDir = __DIR__ . "/../gallery/";

    // Vérifier si le dossier existe, sinon le créer
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = uniqid() . "_" . basename($_FILES['photo']['name']); // Nom unique
    $filePath = $uploadDir . $fileName;

    // Vérification du format
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        header("Location: publicWall.php?message=Format de fichier non autorisé&status=error");
        exit();
    }

    if (move_uploaded_file($fileTmpPath, $filePath)) {
        // Insérer la photo dans la base de données
        $stmt = $pdo->prepare("INSERT INTO photos (user_id, filename, filepath) VALUES (?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $fileName, $filePath])) {
            header("Location: publicWall.php?message=Photo uploadée avec succès&status=success");
            exit();
        } else {
            header("Location: publicWall.php?message=Erreur lors de l'enregistrement en base de données&status=error");
            exit();
        }
    } else {
        header("Location: publicWall.php?message=Erreur lors du téléchargement&status=error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mur Public</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>
<body class="<?= htmlspecialchars($house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/publicWall.php"; ?>

    <!-- Affichage des messages de succès ou d'erreur -->
    <?php if (isset($_GET['message'])) : ?>
        <div class="error-container">
            <p class="<?= ($_GET['status'] ?? 'error') === 'success' ? 'success-message' : 'error-message' ?>">
                <?= htmlspecialchars($_GET['message']); ?>
            </p>
        </div>
    <?php endif; ?>

    <footer></footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>