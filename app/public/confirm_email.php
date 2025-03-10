<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../controllers/AuthController.php";

if (!isset($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];
$auth = new AuthController();
$response = $auth->confirmEmail($token);
if ($response['status'] === 'error') {
    $errorMessage = $response['message'];
    header("Location: login.php?message=" . urlencode($errorMessage));
    exit();
}
header("Location: login.php");
$successMessage = "La confirmation a bien ete effectuee. Vous pouvez maintenant vous connecter.";
header("Location: login.php?message=" . urlencode($successMessage)); 
exit();
?>
