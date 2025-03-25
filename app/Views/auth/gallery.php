
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
<?php endif; ?>

