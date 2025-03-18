

<div class=<?php echo $sub_house ?>>
    <p> Bienvenue <?php echo htmlspecialchars($username); ?> </p>
    <p> ici, tu peux partager tes photos avec le monde entier ! </p>
    <form action="publicWall.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" required>
        <button type="submit">Uploader</button>
    </form>
</div>


<div class=<?php echo $sub_house ?>>
    <!-- ici afficher une zone scrollable avec les publications les plus recente des differents user -->
    <h2>Publications</h2>
    <div class="publications">
    <!-- sous chaque plublication afficher le nom de l'utilisateur et la date de publication -->
    </div>  
</div>



<!-- <div class="container">
        <form action="publicWall.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit">Uploader</button>
        </form>
</div> -->