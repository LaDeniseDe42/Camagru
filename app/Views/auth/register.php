<div class="container">
    <form action="register.php" method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>

    <div class="login-link">
        <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a></p>
    </div>

    <!-- Message de succès -->
    <?php if (!empty($successMessage)) : ?>
        <div class="error-container">
            <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
        </div>
    <?php endif; ?>

    <!-- Message d'erreur -->
    <?php if (!empty($errorMessage)) : ?>
        <div class="error-container">
            <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
        </div>
    <?php endif; ?>
</div>