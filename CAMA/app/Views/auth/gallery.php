<div class="messageSOrF">
    <?php if (isset($_GET['message'])): ?>
        <p class="<?= ($_GET['status'] ?? 'error') === 'success' ? 'success-message' : 'error-message' ?>">
            <?= htmlspecialchars($_GET['message']); ?>
        </p>
    <?php endif; ?>
</div>

<div class="<?= htmlspecialchars($this_sub_house) ?>">
    <h2>Galerie de <?= htmlspecialchars($this_username) ?></h2>
    <?php if ($bestScore === 0): ?>
        <h3>Meilleur score en cours de vol : Tu n'as jamais vole sur un balai 🧹? </h3>
    <?php endif; ?>
    <?php if ($bestScore >= 100 && $bestScore <= 199): ?>
        <h3>Meilleur score en cours de vol : <?= htmlspecialchars($bestScore) ?> pas mal pour un débutant !</h3>
    <?php endif; ?>
    <?php if ($bestScore >= 200 && $bestScore <= 299): ?>
        <h3>Meilleur score en cours de vol : <?= htmlspecialchars($bestScore) ?> un score impressionnant !</h3>
    <?php endif; ?>
    <?php if ($bestScore > 299): ?>
        <h3>Meilleur score en cours de vol : <?= htmlspecialchars($bestScore) ?> GOD OF BROOMSTICKS</h3>
    <?php endif; ?>
    <?php if (!isset($this_user_id)): ?>
        <?php $this_user_id = $_SESSION['user_id'] ?? null; ?>
    <?php endif; ?>
    <?php if ($my_profile): ?>
        <div class="upload-buttons" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 15px 0;">
            <input type="file" id="fileInput" accept="image/*" style="flex: 1;">
            <button id="startCamButton" style="flex: 1;">Prendre une photo ou une vidéo</button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($publications as $publication): ?>
            <?php $userReaction = $publicationController->getUserReaction($publication['id'], $this_user_id); ?>
            <div class="col-4">
                <?php if ($publication['type'] === 'photo'): ?>
                    <a
                        href="/media.php?user=<?= urlencode($this_user_id ?? '') ?>&file=<?= urlencode($publication['filepath']) ?>">
                        <img src="<?= htmlspecialchars($publication['filepath']) ?>"
                            alt="<?= htmlspecialchars($publication['filename']) ?>" style="width:100%">
                    </a>
                <?php elseif ($publication['type'] === 'video'): ?>
                    <video width="100%" controls>
                        <source src="<?= htmlspecialchars($publication['filepath']) ?>" type="video/webm">
                    </video>
                <?php endif; ?>
                <a
                    href="/media.php?user=<?= urlencode($this_user_id ?? '') ?>&file=<?= urlencode($publication['filepath']) ?>">
                    <button> Voir plus </button>
                </a>

                <?php if ($my_profile): ?>
                    <form action="gallery.php" method="POST">
                        <input type="hidden" name="deletePublication" value="<?= htmlspecialchars($publication['id']) ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                <?php endif; ?>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="publication_id" value="<?= htmlspecialchars($publication['id']) ?>">
                <div class="reaction-buttons" data-id="<?= $publication['id'] ?>">
                    <button class="like-button <?= ($userReaction === 'like') ? 'active' : '' ?>">👍
                        <?= $publication['nb_likes'] ?></button>
                    <button class="dislike-button <?= ($userReaction === 'dislike') ? 'active' : '' ?>">👎
                        <?= $publication['nb_dislikes'] ?></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($my_profile): ?>
    <div id="previewContainer" style="display:none; margin-top: 10px;">
        <label for="uploadFilterSelect">Filtre couleur :</label>
        <select id="uploadFilterSelect">
            <option value="none">Aucun filtre</option>
            <option value="grayscale(100%)">Noir et blanc</option>
            <option value="sepia(100%)">Sépia</option>
            <option value="invert(100%)">Inversé</option>
            <option value="blur(5px)">Flou</option>
            <option value="contrast(2000%)">Contraste</option>
            <option value="hue-rotate(90deg)">Teinte</option>
            <option value="invert(100%) sepia(100%)">Inversé et sépia</option>
            <option value="invert(100%) sepia(100%) contrast(200%) saturate(200%)">Night Vision</option>
        </select><br><br>
        <div id="stickerContainer" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <img src="../assets/img/filters/chat.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/dob.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/hiboux.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/bag.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/FOG.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/murky.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/qdenizar.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/jeuM.png" class="sticker" style="max-width:10%; max-height: 10%" />
            <img src="../assets/img/filters/jeuB.png" class="sticker" style="max-width:10%; max-height: 10%" />

        </div>

        <canvas id="uploadCanvas" style="border:1px solid #ccc;"></canvas><br>
        <button id="uploadFilteredImage">Uploader l'image</button>
        <button id="cancelUploadBtn">Annuler</button>
    </div>

<?php endif; ?>

<?php if ($my_profile): ?>
    <div class="divCam">
        <div id="cameraContainer" style="display:none;">
            <select id="filterSelect">
                <option value="none">Aucun filtre</option>
                <option value="grayscale(100%)">Noir et blanc</option>
                <option value="sepia(100%)">Sépia</option>
                <option value="invert(100%)">Inversé</option>
                <option value="blur(5px)">Flou</option>
                <option value="contrast(2000%)">Contraste</option>
                <option value="hue-rotate(90deg)">Teinte</option>
                <option value="invert(100%) sepia(100%)">Inversé et sépia</option>
                <option value="invert(100%) sepia(100%) contrast(200%) saturate(200%)">Night Vision</option>
            </select>

            <label for="imageFilterSelect">Filtres Image :</label>
            <select id="imageFilterSelect">
                <option value="none">Aucun filtre</option>
                <option value="hC">Choipeaux magique</option>
                <option value="harry">Vif d'or</option>
                <option value="kiss">Kiss</option>
                <option value="ma">Maugrey</option>
                <option value="hagrid">Hagrid</option>
                <option value="tree">Arbre</option>
                <option value="titan">Titan</option>
                <option value="wanted">Wanted</option>
            </select>

            <label for="filterStickersSelect">Filtre stickers :</label>
            <select id="filterStickersSelect">
                <option value="none">Aucun filtre</option>
                <option value="dob">Dobby</option>
                <option value="FOG">Brouillard</option>
                <option value="hiboux">Hiboux</option>
                <option value="chat">Chat</option>
                <option value="bag">Baguette</option>
                <option value="murky">Murloc</option>
                <option value="qdenizar">qdenizar</option>
                <option value="jeuB">Pnj volant</option>
                <option value="jeuM">Pnj malefique volant</option>
            </select><br><br>

            <div id="videoHide" style="position: relative; width: 640px; height: 480px;">
                <video id="video" width="640" height="480" autoplay></video>
                <img id="filterImage" src="" style="display: none;">
                <img id="filterStickImage" src="" style="display: none;">

                <video id="recordedVideo" controls style="display: none;"></video>
            </div>
            <button id="snap">Snap Photo</button>
            <button id="closeCamButton">Fermer la caméra</button>
            <div id="videoControls">
                <button id="startRecording">Démarrer l'enregistrement</button>
                <button id="stopRecording" disabled>Arrêter l'enregistrement</button>
                <a id="downloadLink" style="display: none;">Télécharger la vidéo</a>
            </div>
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
    </div>
<?php endif; ?>

<div id="previewControls">
    <video id="previewVideo" controls></video>
    <div>
        <button id="publishButton">Publier</button>
        <button id="cancelButton">Annuler</button>
    </div>
</div>