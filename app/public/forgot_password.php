<?php
require_once __DIR__ . "/../app/models/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    if (User::setPasswordResetToken($email)) {
        echo "Un email de réinitialisation a été envoyé.";
    } else {
        echo "Email non trouvé.";
    }
}
?>
<form action="forgot_password.php" method="post">
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Envoyer</button>
</form>
