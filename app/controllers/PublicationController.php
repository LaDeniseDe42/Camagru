<?php
require_once '../models/Publication.php';

class PublicationController {
  private $publicationModel;
  private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->publicationModel = new Publication($dbConnection);
    }

    public function getPublications($userId) {
        $stmt = $this->dbConnection->prepare("SELECT * FROM publications WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserReaction($publicationId, $userId) {
        $stmt = $this->dbConnection->prepare("SELECT reaction FROM publication_likes WHERE publication_id = ? AND user_id = ?");
        $stmt->execute([$publicationId, $userId]);
        return $stmt->fetchColumn();
    }

    public function getUserIdByPublicationId($publicationId) {
        // $this->publicationModel->getUserIdByPubId($publicationId);
        $stmt = $this->dbConnection->prepare("SELECT user_id FROM publications WHERE id = ?");
      $stmt->execute([$publicationId]);
      return $stmt->fetchColumn();
    }

    public function uploadPublication($userId, $file, $type) {
        // $this->publicationModel->uploadPublication($userId, $file, $type);
        $allowedTypes = ['photo' => ['jpg', 'jpeg', 'png'], 'video' => ['mp4', 'avi', 'mov', 'webm']];
        if (!isset($allowedTypes[$type])) {
            return ['success' => false, 'message' => 'Type de fichier non valide.'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes[$type])) {
            return ['success' => false, 'message' => 'Extension non autorisée.'];
        }
        $uploadDir = "/gallery/$userId/$type/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . ".$extension";
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $stmt = $this->dbConnection->prepare("INSERT INTO publications (user_id, type, filename, filepath) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $type, $filename, $filepath]);
            return ['success' => true, 'message' => 'Publication envoyée.', 'filename' => $filename];
        }
        return ['success' => false, 'message' => 'Erreur lors du téléchargement.'];
    }

    public function deletePublication($publicationId, $userId) {
        $this->publicationModel->deletePublication($publicationId, $userId);
    }

    public function addComment($userId, $publicationId, $content) {
        $stmt = $this->dbConnection->prepare("INSERT INTO comments (user_id, publication_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $publicationId, $content]);
        return ['success' => true, 'message' => 'Commentaire ajouté.'];
    }

    public function reactToPublication($userId, $publicationId, $reaction) {
        if (!in_array($reaction, ['like', 'dislike'])) {
            return false;
          }
        $this->publicationModel->reactToPub($userId, $publicationId, $reaction);
    }
}






?>
