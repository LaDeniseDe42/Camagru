
<div class=<?php echo $sub_house ?>>
    <!-- ici afficher une zone scrollable avec les publications les plus recente des differents user -->
    <h2>Publications</h2>
    <div class="publications">
        <?php foreach ($allPublications as $publication): ?>
            <div class="publication" data-id="<?= $publication['id'] ?>">
                <?php if ($publication['type'] === 'photo'): ?>
                    <a href="media.php?user=<?= urlencode($publication["user_id"]) ?>&file=<?= urlencode($publication['filepath']) ?>">
                    <img src="<?= htmlspecialchars($publication['filepath']) ?>" alt="<?= htmlspecialchars($publication['filename']) ?>" style="width:100%">
                    </a>
                <?php else: ?>
                    <a href="media.php?user=<?= $publication['user_id'] ?>&file=<?= urlencode($publication['filepath']) ?>">
                    <video width="100%" controls>
                            <source src="<?= htmlspecialchars($publication['filepath']) ?>" type="video/webm">
                    </video>
                    </a>
                <?php endif ?>
                <a href="gallery.php?user=<?= $publication['user_id'] ?>">
                <p>Posté par : <?php echo htmlspecialchars($publication['username']); ?></p>
                </a>
                <p>Le : <?php echo htmlspecialchars($publication['uploaded_at']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="more-publications"></div>
    <button id="loadMoreBtn">Voir plus</button>
</div>
