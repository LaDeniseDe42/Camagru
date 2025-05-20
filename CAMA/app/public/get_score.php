<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
$authController = new AuthController();
$best = $authController->getBestScore($_SESSION['user_id']);
$newBest = false;

$score = $_POST['score'] ?? null;
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
