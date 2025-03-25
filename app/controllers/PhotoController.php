<?php
require_once '../models/Photo.php';

class PhotoController {
    private $photoModel;
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->photoModel = new Photo($dbConnection);
        $this->dbConnection = $dbConnection;
    }

    public function uploadPhoto($userId, $file) {
        if (isset($file['name']) && $file['error'] == 0) {
            $uploadDir = '/gallery/' . $userId . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Crée le dossier s'il n'existe pas
            }
            
            $filename = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($file['name']));
            $filepath = $uploadDir . $filename;
            
            if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $filename)) {
                return false;
            }
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $this->photoModel->addPhoto($userId, $filename, $filepath);
            }
        }
        return false;
    }

    public function getUserPhotos($userId) {
        return $this->photoModel->getPhotosByUser($userId);
    }

    public function deletePhotoWithFile($photoInfo, $userId) {
        //Supprimer l'image du répertoire de fichiers
        $filepath = '/gallery/' . $userId . '/' . $photoInfo;
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                return false;
            }
        } else {
            return false;
        }
        $photoId = $this->photoModel->getPhotoId($userId, $photoInfo);
        //Supprimer la photo de la base de données
        return $this->photoModel->deletePhoto($photoId, $userId);
    }
    

    public function getAllImgOfgalleryUserId($user_id) {
        $directory = "/gallery/$user_id/";
        if (!is_dir($directory)) {
            return [];
        }

        $photos = [];
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $uploadAt = file_exists($directory . $file) ? filemtime($directory . $file) : null;
                $photo_id = $this->photoModel->getPhotoId($user_id, $file);
                $nb_likes = $this->photoModel->countLikes($photo_id, 'like');
                $nb_dislikes = $this->photoModel->countLikes($photo_id,'dislike');
                
                $photos[] = [
                    'filename' => $file,
                    'filepath' => '/gallery/' . $user_id . '/' . $file,
                    'uploaded_at' => $uploadAt,
                    'photo_id' => $photo_id,
                    'nb_likes' => $nb_likes,
                    'nb_dislikes' => $nb_dislikes
                ];
            }
        }
        
        usort($photos, fn($a, $b) => $b['uploaded_at'] <=> $a['uploaded_at']);
        return $photos;
    }


    public function viewPhoto($photoId) {
        $photo = $this->photoModel->getPhotoById($photoId);
        if (!$photo) {
            return "Photo introuvable.";
        }
        return "Photo : " . $photo['filename'] . "<br>Likes : " . $photo['nb_likes'] . " | Dislikes : " . $photo['nb_dislikes'];
    }

    public function likePhoto($photoId, $userId, $reaction) {
        if (!in_array($reaction, ['like', 'dislike'])) {
            return "Réaction invalide.";
        }
        
        return $this->photoModel->likePhoto($photoId, $userId, $reaction) ? "Réaction enregistrée." : "Erreur lors de l'ajout du " . $reaction;
    }

    public function getUserReaction($photo_id, $user_id) {
        $stmt = $this->dbConnection->prepare("SELECT reaction FROM photo_likes WHERE photo_id = ? AND user_id = ?");
        $stmt->execute([$photo_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['reaction'] : null;
    }

    public function removeReaction($photo_id, $user_id) {
        $stmt = $this->dbConnection->prepare("DELETE FROM photo_likes WHERE photo_id = ? AND user_id = ?");
        return $stmt->execute([$photo_id, $user_id]);
    }

    public function getLikesCount($photoId, $reaction) {
        if (!in_array($reaction, ['like', 'dislike'])) {
            return "Réaction invalide.";
        }
        if ($reaction === 'like') {
            return $this->photoModel->countLikes($photoId, 'like');
        }
        return $this->photoModel->countLikes($photoId, 'dislike');
    }

    public function getUserIdByPhotoId($photoId) {
        return $this->photoModel->getUserIdByPhotoId($photoId);
    }
}