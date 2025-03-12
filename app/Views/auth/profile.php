
<div class=<?php echo $sub_house ?>>
    <h2>Mon Profil</h2>
    <br>
    <h3>Informations personnelles</h3>
    <br><br>
    <p>Nom d'utilisateur: <strong><?= htmlspecialchars($username); ?></strong>
    <button onclick="openModal('userModal')">Modifier</button>
</p>
<br>
<p>Email: <strong><?= htmlspecialchars($user_mail); ?></strong>
<button onclick="openModal('emailModal')">Modifier</button>
</p>
<br>
<p>Maison: <strong><?= htmlspecialchars($house); ?></strong>
<button onclick="openModal('houseModal')">Modifier</button>
</p>
<?php if (!empty($successMessage)) : ?>
    <div class="error-container">
        <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($errorMessage)) : ?>
    <div class="error-container">
        <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
    </div>
<?php endif; ?>
<?php if ($message) : ?>
    <div class="error-container">
        <p class="success-message"><?= htmlspecialchars($message); ?></p>
    </div>
<?php endif; ?>
</div>

<div id="houseModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier la maison</h2>
        <form action="profile.php" method="post">
            <select name="house" id="modalInput">
                <option value="Gryffondor">Gryffondor</option>
                <option value="Poufsouffle">Poufsouffle</option>
                <option value="Serdaigle">Serdaigle</option>
                <option value="Serpentard">Serpentard</option>
                <option value="Crakmol">Crakmol</option>
                <option value="Moldu">Moldu</option>
            </select>
        <button type="submit" name="update_house">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>

<div id="userModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier l'username</h2>
        <form action="profile.php" method="post">
            <input type="text" name="username" id="modalInput" placeholder="Nouveau nom d'utilisateur">
            <button type="submit" name="update_username">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>


<div id="emailModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier l'email</h2>
        <form action="profile.php" method="post">
            <input type="text" name="email" id="modalInput" placeholder="Nouvel email">
        <button type="submit" name="update_email">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>
