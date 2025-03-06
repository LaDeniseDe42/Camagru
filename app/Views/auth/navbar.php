
<div id="navbar">
    <div id="navbar-content">
        <ul>
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Liens visibles lorsque l'utilisateur est connecté -->
                <li><a href="index.php">Accueil</a></li>
                <li><a href="profile.php">Mon Profil A FAIRE</a></li>
                <li><a href="gallery.php">Galerie A FAIRE</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <!-- Liens visibles lorsque l'utilisateur n'est pas connecté -->
                 <li><a href="index.php">Accueil</a></li>
                <li><a href="login.php">Se connecter</a></li>
                <li><a href="register.php">S'inscrire</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>