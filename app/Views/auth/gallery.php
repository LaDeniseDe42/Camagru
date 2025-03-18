<h2>Mes Photos</h2>
<div class="container">
    <div class="row">
        <?php foreach ($true_photo as $photo) : ?>
            <div class="col-4">
                <img src="<?= htmlspecialchars($photo['filepath']) ?>" alt="<?= htmlspecialchars($photo['filename']) ?>" style="width:100%">
                <form action="gallery.php" method="POST">
                    <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo['filename']) ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<div class=container>
    <form action="gallery.php" method="POST" enctype="multipart/form-data">
        <label for="file">Img</label>
        <br>
        <input type="file" name="file" required>
        <button type="submit">Uploader</button>
    </form>
</div>