<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PublicationController.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Utilisateur non authentifié.']);
  exit;
}

$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Requête invalide ou fichier manquant.']);
  exit;
}

$type = $_POST['type'] ?? 'photo';

try {
  $authController = new AuthController();
  $pdo = $authController->getConnection();
  $publicationController = new PublicationController($pdo);

  $result = $publicationController->uploadPublication($userId, $_FILES['file'], $type);

  if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Image enregistrée avec succès.']);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result['message']]);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()]);
}
