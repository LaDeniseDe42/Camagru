<div class="container">
    <form action="forgot_password.php" method="post">
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Envoyer</button>
    </form>

    <?php if (!empty($successMessage)): ?>
        <div class="error-container">
            <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
        </div>
    <?php endif; ?>

    <!-- Message d'erreur -->
    <?php if (!empty($errorMessage)): ?>
        <div class="error-container">
            <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
        </div>
    <?php endif; ?>

</div>