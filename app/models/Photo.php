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

    // Récupérer toutes les photos d'un utilisateur
    public function getPhotosByUser($userId) {
        $query = "SELECT * FROM photos WHERE user_id = :user_id ORDER BY uploaded_at DESC";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer une photo spécifique
    public function deletePhoto($photoId, $userId) {
        // Vérifier que l'utilisateur possède bien cette photo avant de la supprimer
        $query = "DELETE FROM photos WHERE id = :photo_id AND user_id = :user_id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':photo_id', $photoId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
