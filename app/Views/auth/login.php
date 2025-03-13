<div class="container">
    <form action="login.php" method="post">
        <input type="text" name="email" placeholder="Email or Username" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

    <!-- Lien "Mot de passe oublié" sous le formulaire -->
    <div class="forgot-password">
        <a href="forgot_password.php">Mot de passe oublié ?</a>
    </div>
    <br>

    <?php if (isset($_GET['message'])) : ?>
    <div class="message-container">
        <p class="success-message"><?= htmlspecialchars($_GET['message']); ?></p>
    </div>
    <?php endif; ?>
    <br>


    <div class="register-link">
        <p>Besoin de renvoyer l'email de confirmation ? <a href="resend_mail.php">renvoyer</a></p>
    </div>
    <br>

    <!-- Lien d'inscription sous "Mot de passe oublié" -->
    <div class="register-link">
        <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>
    <!-- Message d'erreur sous le lien d'inscription -->
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