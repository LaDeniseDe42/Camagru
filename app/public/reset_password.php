<?php
require_once __DIR__ . "/../app/models/User.php";

if (isset($_GET["token"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE reset_token = ?");
    $stmt->execute([$password, $_GET["token"]]);
    echo "Mot de passe mis à jour.";
}
?>
<form method="post">
    <input type="password" name="password" placeholder="Nouveau mot de passe" required>
    <button type="submit">Réinitialiser</button>
</form>
