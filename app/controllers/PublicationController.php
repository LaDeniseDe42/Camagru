<?php
require_once '../models/Publication.php';

class PublicationController
{
    private $publicationModel;
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->publicationModel = new Publication($dbConnection);
    }

    public function getPublications($userId)
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM publications WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewPublications(array $excludedIds = [], int $limit = 5): array
    {
        $placeholders = implode(',', array_fill(0, count($excludedIds), '?'));

        $sql = "SELECT p.*, u.username 
                FROM publications p
                JOIN users u ON p.user_id = u.id ";

        if (!empty($excludedIds)) {
            $sql .= "WHERE p.id NOT IN ($placeholders) ";
        }

        $sql .= "ORDER BY p.uploaded_at DESC LIMIT ?";

        $stmt = $this->dbConnection->prepare($sql);

        // Bind les ids exclus
        foreach ($excludedIds as $i => $id) {
            $stmt->bindValue($i + 1, (int) $id, PDO::PARAM_INT);
        }

        // Bind la limite à la fin
        $stmt->bindValue(count($excludedIds) + 1, $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getPublicationIdWithFilename($filename)
    {
        $stmt = $this->dbConnection->prepare("SELECT id FROM publications WHERE filename = ?");
        $stmt->execute([$filename]);
        return $stmt->fetchColumn();
    }

    public function getUserReaction($publicationId, $userId)
    {
        $stmt = $this->dbConnection->prepare("SELECT reaction FROM publication_likes WHERE publication_id = ? AND user_id = ?");
        $stmt->execute([$publicationId, $userId]);
        return $stmt->fetchColumn();
    }

    public function getUserIdByPublicationId($publicationId)
    {
        // $this->publicationModel->getUserIdByPubId($publicationId);
        $stmt = $this->dbConnection->prepare("SELECT user_id FROM publications WHERE id = ?");
        $stmt->execute([$publicationId]);
        return $stmt->fetchColumn();
    }

    public function uploadPublication($userId, $file, $type)
    {
        // $this->publicationModel->uploadPublication($userId, $file, $type);
        $allowedTypes = ['photo' => ['jpg', 'jpeg', 'png', 'gif'], 'video' => ['mp4', 'avi', 'mov', 'webm']];
        if (!isset($allowedTypes[$type])) {
            return ['success' => false, 'message' => 'Type de fichier non valide.'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes[$type])) {
            return ['success' => false, 'message' => 'Extension non autorisée.'];
        }

        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Fichier trop volumineux. Taille maximale : 10 Mo.'];
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
        return ['success' => false, 'message' => 'Erreur lors du téléchargement. userId : ' . $userId . ' file: ' . $file['name'] . ' type: ' . $type . ' filepath: ' . $filepath . ' $file[tmp_name]=  ' . $file['tmp_name']];
    }

    public function deletePublication($publicationId, $userId)
    {
        $this->publicationModel->deletePublication($publicationId, $userId);
    }


    public function reactToPublication($userId, $publicationId, $reaction)
    {
        if (!in_array($reaction, ['like', 'dislike'])) {
            return false;
        }
        $result = $this->publicationModel->reactToPub($userId, $publicationId, $reaction);
        return $this->getReactionData($publicationId, $userId);
    }

    public function getAllComments($publicationId)
    {
        $stmt = $this->dbConnection->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.publication_id = ? 
        ORDER BY comments.created_at DESC
        ");
        $stmt->execute([$publicationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAuthorIdOfComment($commentId)
    {
        return $this->publicationModel->getAuthorIdOfComment($commentId);
    }

    public function deleteComment($commentId, $userId)
    {
        return $this->publicationModel->deleteComment($commentId, $userId);
    }

    public function addComment($userId, $publicationId, $content)
    {
        $newContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        if (empty($newContent)) {
            return ['success' => false, 'message' => 'Le commentaire ne peut pas être vide.'];
        }
        if (strlen($newContent) > 800) {
            return ['success' => false, 'message' => 'Le commentaire est trop long (MAX 800 caractere).'];
        }
        return $this->publicationModel->addComment($userId, $publicationId, $content);
    }

    public function modifyComment($commentId, $userId, $newContent)
    {
        if (empty($newContent)) {
            return ['success' => false, 'message' => 'Le commentaire ne peut pas être vide.'];
        }
        if (strlen($newContent) > 800) {
            return ['success' => false, 'message' => 'Le commentaire est trop long.'];
        }
        return $this->publicationModel->modifyComment($commentId, $userId, $newContent);
    }

    public function getReactionData($publicationId, $userId)
    {
        $stmt = $this->publicationModel->getPublicationById($publicationId);
        $reaction = $this->getUserReaction($publicationId, $userId);
        return [
            'nb_likes' => $stmt['nb_likes'],
            'nb_dislikes' => $stmt['nb_dislikes'],
            'user_reaction' => $reaction,
        ];
    }

}






?>