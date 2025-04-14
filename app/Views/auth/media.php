<body class="<?= htmlspecialchars($this_house) ?>">
    <div class="container">
        <?php if (strpos($filePath, '.webm') !== false): ?>
            <video style="width:70%" controls>
                <source src="<?= htmlspecialchars($filePath) ?>" type="video/webm">
            </video>
        <?php else: ?>
            <img src="<?= htmlspecialchars($filePath) ?>" alt="Média" style="width:70%">
        <?php endif; ?>

        <div class="comments">
            <h3>Commentaires</h3>
                <?php if (empty($allcoments)): ?>
                        <p id="noComment">Aucun commentaire pour le moment.</p>
                    <?php else: ?>
                    <?php foreach ($allcoments as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                            <p><?= htmlspecialchars($comment['content']) ?></p>
                            <span class="timestamp"><?= htmlspecialchars($comment['created_at']) ?></span>
                            <?php if (($comment['user_id'] != $this_user_id) || ($comment['username'] == $_SESSION['username'])): ?>
                                <button class="deleteComment" value="<?= $csrf_token ?>" data-comment-id="<?= htmlspecialchars($comment['id']) ?>">Supprimer</button>

                            <?php endif; ?>
                            <?php if ($comment['username'] == $_SESSION['username']): ?>
                                <button class="editComment" value="<?= $csrf_token ?>" data-comment-id="<?= htmlspecialchars($comment['id']) ?>" data-comment-text="<?= htmlspecialchars($comment['content'], ENT_QUOTES) ?>">✏️ Modifier</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
        <textarea id="theComment" data-post-id="<?= $publicationId ?>" value="<?= $csrf_token ?>" placeholder="Laissez un commentaire..."></textarea>
        <input type="hidden" name="token" data-post-name="<? $this_username ?>" value="<?= $_SESSION['csrf_token'] ?>">
        <button id="newComment">Poster</button>
    </div>
</div>