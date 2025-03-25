<?php
class Photo {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Ajouter une photo
    public function addPhoto($userId, $filename, $filepath) {
        $query = "INSERT INTO photos (user_id, filename, filepath) VALUES (:user_id, :filename, :filepath)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
        $stmt->bindParam(':filepath', $filepath, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getPhotoId($userId, $filename) {
        $query = "SELECT id FROM photos WHERE user_id = :user_id AND filename = :filename";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }


    public function getPhotosByUser($userId) {
        $query = "SELECT * FROM photos WHERE user_id = :user_id ORDER BY uploaded_at DESC";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePhoto($photoId, $userId) {
        $query = "DELETE FROM photos WHERE id = :photo_id AND user_id = :user_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':photo_id', $photoId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function likePhoto($photoId, $userId, $reaction) {
        $stmt = $this->dbConnection->prepare("SELECT reaction FROM photo_likes WHERE photo_id = ? AND user_id = ?");
        $stmt->execute([$photoId, $userId]);
        $existingReaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existingReaction) {
            if ($existingReaction['reaction'] === $reaction) {
                // Supprimer la réaction si elle est la même que celle déjà enregistrée
                $stmt = $this->dbConnection->prepare("DELETE FROM photo_likes WHERE photo_id = ? AND user_id = ?");
                $stmt->execute([$photoId, $userId]);
            } else {
                // Mettre à jour la réaction si elle est différente
                $stmt = $this->dbConnection->prepare("UPDATE photo_likes SET reaction = ? WHERE photo_id = ? AND user_id = ?");
                $stmt->execute([$reaction, $photoId, $userId]);
            }
        } else {
            // Insérer une nouvelle réaction si elle n'existe pas
            $stmt = $this->dbConnection->prepare("INSERT INTO photo_likes (photo_id, user_id, reaction) VALUES (?, ?, ?)");
            $stmt->execute([$photoId, $userId, $reaction]);
        }
    
        // Mettre à jour le nombre de likes et dislikes dans la table photos
        $this->updatePhotoReactionCount($photoId);
    }

    private function updatePhotoReactionCount($photoId) {
        $stmt = $this->dbConnection->prepare("SELECT COUNT(*) FROM photo_likes WHERE photo_id = ? AND reaction = 'like'");
        $stmt->execute([$photoId]);
        $nbLikes = $stmt->fetchColumn();

        $stmt = $this->dbConnection->prepare("SELECT COUNT(*) FROM photo_likes WHERE photo_id = ? AND reaction = 'dislike'");
        $stmt->execute([$photoId]);
        $nbDislikes = $stmt->fetchColumn();

        $stmt = $this->dbConnection->prepare("UPDATE photos SET nb_likes = ?, nb_dislikes = ? WHERE id = ?");
        $stmt->execute([$nbLikes, $nbDislikes, $photoId]);
    }

    public function countLikes($photo_id, $reaction_type) {
        if (!in_array($reaction_type, ['like', 'dislike'])) {
            return 0; // Retourner 0 si le type de réaction est invalide
        }
    
        // Requête pour compter les likes ou dislikes
        $query = "SELECT COUNT(*) as count
                  FROM photo_likes
                  WHERE photo_id = :photo_id AND reaction = :reaction_type";
     
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
        $stmt->bindParam(':reaction_type', $reaction_type, PDO::PARAM_STR);
    
        $stmt->execute();
     
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0; // Retourner 0 si aucun résultat
    }
    
    public function getUserIdByPhotoId($photoId) {
        $query = "SELECT user_id FROM photos WHERE id = :photo_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':photo_id', $photoId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['user_id'] : null;
    }
    
}
