<?php
session_start();
require_once __DIR__ . "/../controllers/AuthController.php";

if (!isset($_SESSION['user'])) {
  header("Location: index.php");
}
$authController = new AuthController();
$message = "";
?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jeu Balai</title>
  <link rel="stylesheet" href="/../assets/css/game.css">
  <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>

<body>
  <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
  <canvas id="gameCanvas"></canvas>
  <div id="manaEffect" class="mana-effect hidden"></div>
  <div id="restartModal" class="modal hidden">
    <div class="modal-content">
      <p id="currentScore"></p>
      <p id="bestScore"></p>
      <p>Tu veux recommencer ?</p>
      <button id="yesBtn">Oui</button>
      <button id="noBtn">Non</button>
    </div>

  </div>

  <script type="module" src="assets/js/game/game.js"></script>
</body>
<script src="/../assets/js/navScript.js"></script>

</html>