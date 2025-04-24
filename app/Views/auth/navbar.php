<div id="navbar-toggle">
    <div></div>
    <div></div>
    <div></div>
</div>
<div id="navbar">

    <div id="navbar-content">
        <ul>
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Liens visibles lorsque l'utilisateur est connecté -->
                <li><a href="index.php">Accueil</a></li>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a href="gallery.php">Galerie</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
                <li><a href="publicWall.php">Mur public</a></li>
            <?php else: ?>
                <!-- Liens visibles lorsque l'utilisateur n'est pas connecté -->
                <li><a href="index.php">Accueil</a></li>
                <li><a href="login.php">Se connecter</a></li>
                <li><a href="register.php">S'inscrire</a></li>
                <li><a href="publicWall.php">Mur public</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>