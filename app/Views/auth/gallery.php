
<div class="messageSOrF">
    <?php if (isset($_GET['message'])) : ?>
        <p class="<?= ($_GET['status'] ?? 'error') === 'success' ? 'success-message' : 'error-message' ?>">
            <?= htmlspecialchars($_GET['message']); ?>
        </p>
    <?php endif; ?>
</div>

<div class=<?php echo $this_sub_house; ?>>
<h2>Galerie de <?= htmlspecialchars($this_username) ?></h2>

    <div class="row">
    <?php foreach ($true_photo as $photo) : ?>
    <?php $userReaction = $photoController->getUserReaction($photo['photo_id'], $this_user_id); ?>
    <div class="col-4">
        <img src="<?= htmlspecialchars($photo['filepath']) ?>" alt="<?= htmlspecialchars($photo['filename']) ?>" style="width:100%">
        
        <?php if ($my_profile) : ?>
        <form action="gallery.php" method="POST">
            <input type="hidden" name="delete" value="<?= htmlspecialchars($photo['filename']) ?>">
            <button type="submit">Supprimer</button>
        </form>
        <?php endif; ?>

        <!-- Afficher les likes et dislikes -->
        <form action="gallery.php" method="POST">
            <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo['photo_id']) ?>">
            
            <button type="submit" name="like" 
                class="<?= ($userReaction === 'like') ? 'active' : '' ?>">
                👍 <?= htmlspecialchars($photo['nb_likes']) ?>
            </button>
            
            <button type="submit" name="dislike" 
                class="<?= ($userReaction === 'dislike') ? 'active' : '' ?>">
                👎 <?= htmlspecialchars($photo['nb_dislikes']) ?>
            </button>
        </form>
    </div>
<?php endforeach; ?>


    </div>
</div>

<?php if ($my_profile) : ?>
<div class=<?php echo $this_sub_house; ?>>
<h2 class="title"> Ajouter une nouvelle photo </h2>
    <form action="gallery.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Uploader</button>
    </form>
</div>


<!-- Bouton pour ouvrir la webcam -->
<div class="<?php echo $this_sub_house; ?>">
    <h2 class="title"> Prendre une photo avec la webcam </h2>
    <button id="startCamButton">Prendre une photo avec la caméra</button>
    <div id="cameraContainer" style="display:none;">
        <video id="video" width="640" height="480" autoplay></video>
        <button id="snap">Snap Photo</button>
    </div>
    
<div id="photoModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Prévisualisation de la photo</h3>
        <img id="capturedImage" width="640" height="480" />
        <form action="gallery.php" id="camForm" method="POST">
            <input type="hidden" name="photocam" id="photocam">
            <button type="submit" id="cToi">Uploader</button>
            <button type="button" id="discard">Annuler</button>
        </form>
    </div>
</div>


<!-- Style CSS pour le modal -->
<style>
    /* Style du modal */
.modal {
    position: fixed;
    top:0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Fond semi-transparent */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* S'assurer que le modal soit au-dessus de tout */
    overflow: auto;
}

/* Contenu du modal */
.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    width: 70%;
    max-width: 700px;
    overflow-y: auto;
}

/* Cacher la caméra quand on est dans le modal */
#cameraContainer {
    display: none;
}

</style>

<?php endif; ?>

