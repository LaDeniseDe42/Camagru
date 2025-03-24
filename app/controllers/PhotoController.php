<?php
require_once '../models/Photo.php';

class PhotoController {
    private $photoModel;

    public function __construct($dbConnection) {
        $this->photoModel = new Photo($dbConnection);
    }

    // Gérer l'ajout d'une photo
    public function uploadPhoto($userId, $file) {
        if (isset($file['name']) && $file['error'] == 0) {
            $uploadDir = '/gallery/' . $userId . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Crée le dossier s'il n'existe pas
            }
            $filename = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($file['name']));
            $filepath = $uploadDir . $filename;
            // Vérifier que le fichier est une image
             if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $filename)) {
                return false;
             }
            // Déplacer le fichier vers le dossier d'upload
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $this->photoModel->addPhoto($userId, $filename, $filepath);
            }
        }
        return false;
    }

    // Récupérer les photos d'un utilisateur
    public function getUserPhotos($userId) {
        return $this->photoModel->getPhotosByUser($userId);
    }

    // Supprimer une photo
    public function deleteUserPhoto($photoId, $userId) {
        return $this->photoModel->deletePhoto($photoId, $userId);
    }

    public function getUploadAt($user_id, $filename) {
        $directory = "/gallery/$user_id/";
        $filepath = $directory . $filename;
        if (file_exists($filepath)) {
            return filemtime($filepath);
        }
        return false;
    }

    public function getAllImgOfgalleryUserId($user_id) {
        $directory = "/gallery/$user_id/";

        // Vérifie si le dossier existe
        if (!is_dir($directory)) {
            return [];
        }
        // Récupère la liste des fichiers (uniquement images)
        $photos = [];
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file !== "." && $file !== ".." && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $uploadAt =  $this->getUploadAt($user_id, $file);
                $photos[] = [
                    'filename' => $file,
                    'filepath' => $directory . $file,
                    'uploaded_at' => $uploadAt
                ];
            }
        }
    // Trier les photos du plus récent au plus ancien
    usort($photos, function ($a, $b) {
        return $b['uploaded_at'] - $a['uploaded_at']; // Tri décroissant
    });
        return $photos;
    }

    // public function getUsername($user_id) {
    //     $stmt = $this->photoModel->db->prepare("SELECT username FROM users WHERE id = ?");
    //     $stmt->execute([$user_id]);
    //     return $stmt->fetchColumn() ?: "Utilisateur inconnu";
    // }

    public function deleteThisImg($user_id, $filename) {
        $directory = "/gallery/$user_id/";
        $filepath = $directory . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

}
?>
