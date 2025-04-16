<?php
require_once __DIR__ . '/../controllers/PublicationController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$existingIds = $input['existingIds'] ?? [];

$authController = new AuthController();
$con = $authController->getConnection();
$publicationController = new PublicationController($con); // adapte si besoin

$newPublications = $publicationController->getNewPublications($existingIds, 5);
echo json_encode($newPublications);
?>
