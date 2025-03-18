<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";

//recupere les infos de session
$user = $_SESSION['user'];
$email = $_SESSION['email'];
$username =$_SESSION['username'];
$user_id = $_SESSION['user_id'];
$house = $_SESSION['house'];
$sub_house = strtolower($house);



if (!isLoggedIn())
{
    header("Location: login.php");
    exit();
}

$message = "";


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
    <link rel="stylesheet" href="/../assets/css/profile.css">
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