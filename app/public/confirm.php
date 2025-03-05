<?php
require_once __DIR__ . "/../app/models/User.php";

if (isset($_GET["token"])) {
    if (User::confirmAccount($_GET["token"])) {
        echo "Compte activé ! Vous pouvez maintenant vous connecter.";
    } else {
        echo "Lien invalide.";
    }
} else {
    echo "Token manquant.";
}
?>