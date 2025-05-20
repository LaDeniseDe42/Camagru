<div class=<?php echo $sub_house ?>>
    <?php if ($isLog): ?>
        <h2>Publications</h2>
    <?php else: ?>
        <h2>Voici une partie des trésors auquel vous pourriez acceder en vous connectant </h2>
    <?php endif; ?>
    <div class="publications">
        <?php foreach ($allPublications as $publication): ?>
            <div class="publication" data-id="<?= $publication['id'] ?>">
                <?php if ($publication['type'] === 'photo'): ?>
                    <img src="<?= htmlspecialchars($publication['filepath']) ?>"
                        alt="<?= htmlspecialchars($publication['filename']) ?>" style="width:50% ; height:50%;">
                <?php else: ?>
                    <video width="50% ; height:50%;" controls>
                        <source src="<?= htmlspecialchars($publication['filepath']) ?>" type="video/webm">
                    </video>
                <?php endif ?>
                <?php if ($isLog): ?>
                    <div class="reaction-buttons" data-id="<?= $publication['id'] ?>">
                        <button class="like-button <?= ($userReaction === 'like') ? 'active' : '' ?>">👍
                            <?= $publication['nb_likes'] ?></button>
                        <button class="dislike-button <?= ($userReaction === 'dislike') ? 'active' : '' ?>">👎
                            <?= $publication['nb_dislikes'] ?></button>
                    </div>
                    <a
                        href="/media.php?user=<?= urlencode($publication['user_id']) ?>&file=<?= urlencode($publication['filepath']) ?>">
                        <button> Voir plus </button>
                    </a>
                    <a href="gallery.php?user=<?= $publication['user_id'] ?>">
                        <p>Posté par : <?php echo htmlspecialchars($publication['username']); ?></p>
                    </a>
                    <p>Le : <?php echo htmlspecialchars($publication['uploaded_at']); ?></p>
            </div>
        <?php endif ?>
    <?php endforeach; ?>
    </div>
    <?php if ($isLog): ?>
        <div id="more-publications"></div>
        <button id="loadMoreBtn">Voir plus</button>
    <?php else: ?>
        <p>Connectez-vous pour voir plus de publications !</p>
        <a style="color: black; font-weight: bold;" href="/login.php">Se connecter</a>
    <?php endif; ?>
</div>