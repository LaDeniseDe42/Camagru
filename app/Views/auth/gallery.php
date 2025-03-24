
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
            <div class="col-4">
                    <img src="<?= htmlspecialchars($photo['filepath']) ?>" alt="<?= htmlspecialchars($photo['filename']) ?>" style="width:100%">
                <?php if ($my_profile) : ?>
                <form action="gallery.php" method="POST">
                    <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo['filename']) ?>">
                    <button type="submit">Supprimer</button>
                </form>
                <?php endif; ?>
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

