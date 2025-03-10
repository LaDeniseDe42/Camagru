<h2>Mon Profil</h2>
    <?php if (isset($_GET['message']) && $_GET['message'] == 'success') echo "<p style='color: green;'>Profil mis à jour !</p>"; ?>
    <?php if (isset($_GET['message']) && $_GET['message'] == 'password_updated') echo "<p style='color: green;'>Mot de passe mis à jour !</p>"; ?>
    
    <form method="POST">
        <label>Nom d'utilisateur:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        
        <label>Maison Harry Potter:</label>
        <select name="house" onchange="changeTheme(this.value)">
            <option value="gryffindor" <?php if ($user['house'] == 'gryffindor') echo 'selected'; ?>>Gryffondor</option>
            <option value="hufflepuff" <?php if ($user['house'] == 'hufflepuff') echo 'selected'; ?>>Poufsouffle</option>
            <option value="ravenclaw" <?php if ($user['house'] == 'ravenclaw') echo 'selected'; ?>>Serdaigle</option>
            <option value="slytherin" <?php if ($user['house'] == 'slytherin') echo 'selected'; ?>>Serpentard</option>
        </select>
        
        <button type="submit">Mettre à jour</button>
    </form>

    <h3>Changer le mot de passe</h3>
    <form method="POST">
        <label>Nouveau mot de passe:</label>
        <input type="password" name="password" required>
        
        <label>Confirmer le mot de passe:</label>
        <input type="password" name="confirm_password" required>
        
        <button type="submit">Changer</button>
    </form>