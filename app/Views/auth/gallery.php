<div class="messageSOrF">
    <?php if (isset($_GET['message'])): ?>
        <p class="<?= ($_GET['status'] ?? 'error') === 'success' ? 'success-message' : 'error-message' ?>">
            <?= htmlspecialchars($_GET['message']); ?>
        </p>
    <?php endif; ?>
</div>

<div class="<?= htmlspecialchars($this_sub_house) ?>">
    <h2>Galerie de <?= htmlspecialchars($this_username) ?></h2>
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
                        href="media.php?user=<?= urlencode($this_user_id ?? '') ?>&file=<?= urlencode($publication['filepath']) ?>">
                        <img src="<?= htmlspecialchars($publication['filepath']) ?>"
                            alt="<?= htmlspecialchars($publication['filename']) ?>" style="width:100%">
                    </a>
                <?php elseif ($publication['type'] === 'video'): ?>
                    <a href="media.php?user=<?= $publication['user_id'] ?>&file=<?= urlencode($publication['filepath']) ?>">
                        <video width="100%" controls>
                            <source src="<?= htmlspecialchars($publication['filepath']) ?>" type="video/webm">
                        </video>
                    </a>
                <?php endif; ?>

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
        <label for="uploadFilterSelect">Filtre :</label>
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
        <canvas id="uploadCanvas" style="max-width:100%; border:1px solid #ccc;"></canvas><br>
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

            <div id="videoHide" style="position: relative; width: 640px; height: 480px;">
                <video id="video" width="640" height="480" autoplay></video>
                <img id="filterImage" src="" style="display: none;">
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