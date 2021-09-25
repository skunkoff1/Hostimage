<?php

//DEMARRAGE DE LA SESSION

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');
require('src/connect.php');

$pseudo = $_SESSION['pseudo'];

//RECUPERATION CURRENT ALBUM

$currentFileName = $_GET['image'];
$currentalbum = $_GET['album'];
$currentImageName = $_GET['name'];


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Host Image</title>
    <link rel="stylesheet" href="http://hostimage/design/album.css">
    <link rel="icon" type="image/png" href="http://hostimage/images/logo-icon-rouge.png">
    <script src="https://kit.fontawesome.com/7b8010a28b.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include('src/header.php'); ?>
    <section id="bienvenue">
        <div id="presentation">
            <p><?php echo $currentImageName ?></p>
        </div>
    </section>

    <section class="img-full">
        <div class="img-full">
            <?php echo '<img class="img-full" src="uploads/' . $pseudo . '/' . $currentFileName . '">'; ?>
        </div>
    </section>
    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>