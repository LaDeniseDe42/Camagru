<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
$authController = new AuthController();

$best = $authController->getBestScore($_SESSION['user_id']) ?? 0;
$newBest = false;

$score = $_POST['score'] ?? 0;

if (!is_numeric($score)) {
  http_response_code(400);
  echo json_encode(['error' => 'Score invalide.']);
  exit;
}

if ($score > $best) {
  $authController->updateBestScore($_SESSION['user_id'], $score);
  $best = $score;
  $newBest = true;
}

echo json_encode([
  'score' => $score,
  'best_score' => $best,
  'new_best' => $newBest
]);
