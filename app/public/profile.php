<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
requireLogin();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <script>
        function changeTheme(house) {
            document.body.className = house;
        }
    </script>
</head>
<body class="<?php echo htmlspecialchars($user['house']); ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/profile.php"; ?>
    <footer>
    </footer>
</body>
</html>
