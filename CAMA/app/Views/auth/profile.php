<div class="<?= htmlspecialchars($sub_house) ?>">
    <h2>Mon Profil</h2>
    <br>
    <h3>Informations personnelles</h3>
    <br><br>
    <p>Nom d'utilisateur: <strong id="username"><?= htmlspecialchars($username); ?></strong>
        <button onclick="openModal('userModal')">Modifier</button>
    </p>
    <br>
    <p>Email: <strong id="email"><?= htmlspecialchars($user_mail); ?></strong>
        <button onclick="openModal('emailModal')">Modifier</button>
    </p>
    <br>
    <p>Maison: <strong><?= htmlspecialchars($house); ?></strong>
        <button onclick="openModal('houseModal')">Modifier</button>
    </p>
    <br>
    <p>Email notifications: <strong id="notif"><?= htmlspecialchars($want_email_notif); ?></strong>
        <button id="notif" onclick="openModal('emailNotifM')">Modifier</button>
    </p>
    <br>
    <p>Mot de passe: <strong id="password">********</strong>
        <button onclick="openModal('mdpModal')">Modifier</button>
    </p>
    <br>
</div>


<div id="houseModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier la maison</h2>
        <form action="profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <select name="house" id="modalHouse">
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
</div>

<div id="userModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier l'username</h2>
        <form action="profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="text" name="username" id="modalUser" placeholder="Nouveau nom d'utilisateur">
            <button type="submit" name="update_username">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>

<div id="emailModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier l'email</h2>
        <form action="profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="text" name="email" id="modalEmail" placeholder="Nouvel email">
            <button type="submit" name="update_email">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>

<div id="mdpModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier le mot de passe</h2>
        <form action="profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" autocomplete="username"
                style="display:none;">
            <input type="password" name="password" id="modalMdp" placeholder="Nouveau mot de passe"
                autocomplete="new-password">
            <input type="password" name="confirm_password" id="modalInput" placeholder="Confirmer le mot de passe"
                autocomplete="new-password">
            <button type="submit" name="update_password">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>

<div id="emailNotifM" class="modal" style="display: none;">
    <div class="modal-content">
        <h2 id="modalTitle">Modifier l'envoi de mail en cas de commentaire</h2>
        <form action="profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <select name="want_email_notif" id="modalNotif">
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>
            <button type="submit" name="update_notif">Modifier</button>
        </form>
        <button onclick="closeModal()">Fermer</button>
    </div>
</div>

<div id="infoHouse" class="<?= htmlspecialchars($house) ?>" style="display: none;">
</div>