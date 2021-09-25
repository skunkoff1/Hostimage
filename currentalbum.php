<?php

//DEMARRAGE DE LA SESSION

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');
require('src/connect.php');

$pseudo = $_SESSION['pseudo'];

//RECUPERATION CURRENT ALBUM

$currentalbum = $_GET['album'];



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
            <p><?php echo $currentalbum ?></p>
        </div>
    </section>

    <section>
        <div class="img-container">
            <?php $reqImgFromCurrentAlbum = $db->prepare('SELECT img_name,file_name FROM images WHERE album_user = ? AND album = ?');
            $reqImgFromCurrentAlbum->execute(array($pseudo, $currentalbum));

            while ($image = $reqImgFromCurrentAlbum->fetch()) {
                echo '
                        <div class="image-card">
                        <a href="http://hostimage/currentimage.php?name=' . $image['img_name'] . '&album=' . $currentalbum . '&image=' . $image['file_name'] . '">
                        <img class="image-inside-card"  src="uploads/' . $pseudo . '/' . $image['file_name'] . '"></a>                        
                        <hr>
                        <p><h3>' . $image['img_name'] . '</h3></p>
                        <p><a href="http://hostimage/currentimage.php?name=' . $image['img_name'] . '&album=' . $currentalbum . '&image=' . $image['file_name'] . '">Voir l\'image</a></p>
                        </div>';
            }
            ?>
        </div>
    </section>
    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>