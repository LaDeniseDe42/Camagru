<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PublicationController.php";
$message = "";

if (!isLoggedIn())
{
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $file = isset($_POST['file']) ? basename(htmlspecialchars($_POST['file'])) : '';
  $filePath = isset($_POST['file']) ? htmlspecialchars($_POST['file']) : '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $file = isset($_GET['file']) ? basename(htmlspecialchars($_GET['file'])) : '';
  $filePath = isset($_GET['file']) ? htmlspecialchars($_GET['file']) : '';
} else {
  echo "Méthode de requête non valide.";
  exit;
}


$dbConnection = new Database();
$con = $dbConnection->getConnection();
$UserController = new AuthController();
$my_profile = true;
if (isset($_GET['user']) && !empty($_GET['user']) && $_GET['user'] != $_SESSION['user_id']) {
  $my_profile = false;
  $this_user_id = $_GET['user'];
  $this_user = $UserController->giveinfoConcernThisUser($this_user_id);
  if ($this_user === false) {
    header("Location: gallery.php?message=" . urlencode("Cet utilisateur n'existe pas"));
    exit();
  }
  $this_username = $this_user['username'];
  $this_house = $this_user['house'];
} else {
  $this_user_id = $_SESSION['user_id'];
  $this_username = $_SESSION['username'];
  $this_house = $_SESSION['house'];
  $this_user = $_SESSION['user'];
}
$csrf_token = $_SESSION['csrf_token'];

$publicationController = new PublicationController($con);
$publicationId = $publicationController->getPublicationIdWithFilename($file);
$allcoments = $publicationController->getAllComments($publicationId);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && $_POST['token'] == $csrf_token && !isset($_POST['editComment'])) {
    $content = $_POST['comment'];
    $result = $publicationController->addComment($this_user_id, $publicationId, $content);
    if ($result['success']) {
      // On suppose que l'ID est dans $result['comment_id']
      echo json_encode([
          "success" => true,
          "message" => "Commentaire ajouté",
          "comment_id" => $result['comment_id'], // tu dois t'assurer que ton contrôleur renvoie cet ID
          "username" => $_SESSION['username'],
          "created_at" => date("Y-m-d H:i:s")
      ]);
  } else {
      echo json_encode([
          "success" => false,
          "message" => "Erreur lors de l'ajout du commentaire : " . $result['message']
      ]);
  }
  exit;
    //   if ($result['success']) {
  //     echo "Commentaire ajouté";
  // } else {
  //     echo "Erreur lors de l'ajout du commentaire : " . $result['message'];
  // }
  // exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteComment']) && $_POST['token'] == $csrf_token) {
  $commentId = $_POST['commentId'];
  $user_id_to_send = $publicationController->getAuthorIdOfComment($commentId);
  $result = $publicationController->deleteComment($commentId, $user_id_to_send);
  if ($result['success']) {
    // $message = "Publication supprimée avec succès.";
    echo json_encode([
      "success" => true,
      "message" => "Commentaire supprimé",
      "comment_id" => $commentId
  ]);
} else {
  echo json_encode([
      "success" => false,
      "message" => "Erreur lors de la suppression du commentaire."
  ]);
}
exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editComment']) && $_POST['token'] == $csrf_token) {
  $commentId = $_POST['commentId'];
  $newComment = $_POST['newContent'];
  $result = $publicationController->modifyComment($commentId, $this_user_id, $newComment);
  if ($result['success']) {
    echo json_encode([  // Renvoi la réponse en JSON
        "success" => true,
        "message" => "Commentaire modifié avec succès.",
        "comment_id" => $commentId,
        "new_content" => $newComment // Retourner le nouveau texte
    ]);
} else {
    echo json_encode([  // En cas d'erreur, renvoi également un JSON
        "success" => false,
        "message" => "Erreur lors de la modification du commentaire."
    ]);
}
exit;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Media de <?= htmlspecialchars($this_username) ?></title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <link rel="stylesheet" href="/../assets/css/comment.css">
  </head>
  <body class="<?= htmlspecialchars($this_house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . '/../Views/auth/media.php'; ?>
    
    <footer></footer>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/comments.js"></script>
  </body>
  </html>