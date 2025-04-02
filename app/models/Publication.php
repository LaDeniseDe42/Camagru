<?php
class Publication {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    
    public function getPublicationsByUser($userId) {
      $query = "SELECT * FROM publications WHERE user_id = ? ORDER BY uploaded_at DESC";
      $stmt = $this->dbConnection->prepare($query);
      $stmt->execute([$userId]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deletePublication($publicationId, $userId) {
      $query = "DELETE FROM publications WHERE id = ? AND user_id = ?";
      $stmt = $this->dbConnection->prepare($query);
      return $stmt->execute([$publicationId, $userId]);
    }
    
    public function addComment($userId, $publicationId, $content) {
      $stmt = $this->dbConnection->prepare("INSERT INTO comments (user_id, publication_id, content) VALUES (?, ?, ?)");
      return $stmt->execute([$userId, $publicationId, $content]);
    }
    
    public function reactToPub($userId, $publicationId, $reaction) {
      if (!in_array($reaction, ['like', 'dislike'])) {
          return ['success' => false, 'message' => 'Réaction non valide.'];
      }

      $stmt = $this->dbConnection->prepare("SELECT reaction FROM publication_likes WHERE user_id = ? AND publication_id = ?");
      $stmt->execute([$userId, $publicationId]);
      $existingReaction = $stmt->fetchColumn();

      if ($existingReaction) {
          if ($existingReaction == $reaction) {
              $stmt = $this->dbConnection->prepare("DELETE FROM publication_likes WHERE user_id = ? AND publication_id = ?");
              $stmt->execute([$userId, $publicationId]);
              if ($reaction == 'like') {
                  $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_likes = nb_likes - 1 WHERE id = ?");
              } else {
                  $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_dislikes = nb_dislikes - 1 WHERE id = ?");
              }
              $stmt->execute([$publicationId]);
              return ['success' => true, 'message' => 'Réaction supprimée.'];
          } else {
              $stmt = $this->dbConnection->prepare("UPDATE publication_likes SET reaction = ? WHERE user_id = ? AND publication_id = ?");
              $stmt->execute([$reaction, $userId, $publicationId]);
              if ($reaction == 'like') {
                  $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_likes = nb_likes + 1, nb_dislikes = nb_dislikes - 1 WHERE id = ?");
              } else {
                  $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_dislikes = nb_dislikes + 1, nb_likes = nb_likes - 1 WHERE id = ?");
              }
              $stmt->execute([$publicationId]);
              return ['success' => true, 'message' => 'Réaction mise à jour.'];
          }
      } else {
          $stmt = $this->dbConnection->prepare("INSERT INTO publication_likes (user_id, publication_id, reaction) VALUES (?, ?, ?)");
          $stmt->execute([$userId, $publicationId, $reaction]);
          if ($reaction == 'like') {
              $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_likes = nb_likes + 1 WHERE id = ?");
          } else {
              $stmt = $this->dbConnection->prepare("UPDATE publications SET nb_dislikes = nb_dislikes + 1 WHERE id = ?");
          }
          $stmt->execute([$publicationId]);
          return ['success' => true, 'message' => 'Réaction ajoutée.'];
      }
  }

  }
  
  ?>