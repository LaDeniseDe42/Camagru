<?php
require_once __DIR__ . "/../app/controllers/AuthController.php";

if ($_SERVER['REQUEST_URI'] === '/register') {
    AuthController::register();
}
?>