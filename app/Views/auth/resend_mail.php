<div class="container">

    <h3>Renvoyer l'email de confirmation</h3>
    <form action="resend_mail.php" method="post">
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">renvoyer</button>
    </form>

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
<br>
