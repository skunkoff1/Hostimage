<?php

//DEMARRAGE DE LA SESSION

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');
require('src/connect.php');

$pseudo = $_SESSION['pseudo'];

$album = array();
$images = array();

$reqAlbum = $db->prepare('SELECT album_name FROM album WHERE album_user = ?');
$reqAlbum->execute(array($pseudo));

while ($data = $reqAlbum->fetch()) {
    $album[] = $data['album_name'];
}

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
            <p>Bienvenue dans votre espace albums !</p>
        </div>
    </section>
    <section>
        <div class="album-container">
            <?php foreach ($album as $value) {

                $reqImgFromAlbum = $db->prepare('SELECT file_name FROM images WHERE album = ? AND album_user = ? LIMIT 1');
                $reqImgFromAlbum->execute(array($value, $pseudo));

                while ($data = $reqImgFromAlbum->fetch()) {
                    $images[] = $data['file_name'];
                }

                echo '
                <div class="album-card">';
                foreach ($images as $elmt) {
                    echo '<img class="album-front-image" src="uploads/' . $pseudo . '/' . $elmt . '">';
                }

                $images = array();

                echo '
                <h3>' . $value . '</h3>
                <div><hr></div>
                <h4><a class="album-card-link" href="http://hostimage/currentalbum.php?album=' . $value . '">ouvrir l\'album</a></h4>
                </div>';
            }
            ?>
        </div>
    </section>
    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>