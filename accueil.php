<?php

//DEMARRAGE DE LA SESSION

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');
require('src/connect.php');

$pseudo = $_SESSION['pseudo'];
$step = "";

/*==================================================================================================*/
/*=================================== GESTION FORMULAIRE ENVOI IMAGE ===============================*/
/*==================================================================================================*/



/*======================================================================================*/
/*============= DEFINITION DES CONSTANTES / TABLEAUX ET VARIABLES ======================*/
/*======================================================================================*/

// Constantes

$dir_path = 'uploads/' . $pseudo . '/';
define('TARGET', $dir_path);    // Repertoire cible
define('MAX_SIZE', 100000);    // Taille max en octets du fichier
define('WIDTH_MAX', 4800);    // Largeur max de l'image en pixels
define('HEIGHT_MAX', 4800);    // Hauteur max de l'image en pixels

// Tableaux de donnees
$tabExt = array('jpg', 'gif', 'png', 'jpeg');    // Extensions autorisees
$infosImg = array();

// Variables
$extension = '';
$message = '';
$error = '';
$nomImage = '';
$nomImageUser = "";
$albumName = "";

/*===========================================================================*/
/*================ CREATION REPERTOIRE CIBLE SI INEXISTANT ==================*/
/*===========================================================================*/

if (!is_dir($dir_path)) {
    if (!mkdir($dir_path)) {
        exit('Erreur : le répertoire cible ne peut-être créé ! Vérifiez que vous diposiez des droits suffisants pour le faire ou créez le manuellement !');
    }
}

/*===========================================================================*/
/*========================== GESTION UPLOAD IMAGE ===========================*/
/*===========================================================================*/


if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

    $nomImageUser = $_POST['filename'];

    if ($_FILES['file']['size'] <= 10485760) {

        $infoImage = pathinfo($_FILES['file']['name']);

        // Recuperation de l'extension du fichier
        $extension  = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        $extensionArray = array('png', 'gif', 'jpg', 'jpeg');

        // On recupere les dimensions du fichier
        $infosImg = getimagesize($_FILES['file']['tmp_name']);

        if (in_array($extension, $extensionArray)) {

            // Parcours du tableau d'erreurs
            if (isset($_FILES['file']['error']) && UPLOAD_ERR_OK === $_FILES['file']['error']) {

                $nomImage = time() . rand() . rand() . '.' . $extension;
                move_uploaded_file($_FILES['file']['tmp_name'], $dir_path . $nomImage);
                $error = 0;
                $message = 'envoi réussi sous le nom : ' . $nomImageUser;

                $reqTabImg = $db->prepare('INSERT INTO images(img_name, file_name, album, album_user) VALUES(?, ?, ?, ?)');
                $reqTabImg->execute(array($nomImageUser, $nomImage, $albumName, $pseudo));
                $step = 1;
            } else {
                $error = 1;
                $message = 'erreur lors de l\'upload';
            }
        } else {
            $error = 1;
            $message = 'Vérifier l\'extension de votre image (extension autorisée : .png, .gif, .jpg, .jpeg)';
        }
    } else {
        $error = 1;
        $message = 'Le fichier est trop volumineux (max : 10Mo)';
    }
}

if (isset($_FILES['file']) && $_FILES['file']['error'] != 0) {

    switch ($_FILES['file']['error']) {
        case 1:
            $error = 1;
            $message = 'erreur lors de l\'envoi : fichier trop volumineux pour le serveur.';
            break;

        case 2:
            $error = 1;
            $message = 'erreur lors de l\'envoi : fichier trop volumineux pour le site.';
            break;

        case 3:
            $error = 1;
            $message = 'erreur lors de l\'envoi : fichier partiellement téléchargé<br /> Essayez à nouveau.';
            break;

        case 4:
            $error = 1;
            $message = 'erreur lors de l\'envoi : Aucun fichier détecté.';
            break;

        case 6:
            $error = 1;
            $message = 'erreur lors de l\'envoi : un dossier temporaire est manquant<br />Contactez nous !';
            break;

        case 7:
            $error = 1;
            $message = 'erreur lors de l\'envoi : Echec lors de l\'écriture<br />Essayez à nouveau.';
            break;

        case 8:
            $error = 1;
            $message = 'erreur lors de l\'envoi : erreur interne.';
            break;
    }
}



if (isset($_POST['album']) && ($_POST['album']) == "new") {
    $step = 2;
}



/*===========================================================================*/
/*================ ENREGISTREMENT BDD ALBUM SI NOUVEL ALBUM =================*/
/*===========================================================================*/

if (isset($_POST['album-name-user'])) {

    $albumName = $_POST['album-name-user'];
    $reqTabAlbum = $db->prepare('INSERT INTO album(album_name, album_user) VALUES(?, ?)');
    $reqTabAlbum->execute(array($albumName, $pseudo));

    $reqIdAlbum = $db->prepare('SELECT MAX(id) FROM images WHERE album_user = ?');
    $reqIdAlbum->execute(array($pseudo));
    $lastId = $reqIdAlbum->fetch();

    $reqUpdateImg = $db->prepare('UPDATE images SET album = ? WHERE id = ?');
    $reqUpdateImg->execute(array($albumName, $lastId[0]));

    $message = 'L\'image a bien été ajoutée à l\'album : ' . $albumName;
}

if (isset($_POST['album'])) {
    $albumName = $_POST['album'];

    $reqIdAlbum = $db->prepare('SELECT MAX(id) FROM images WHERE album_user = ?');
    $reqIdAlbum->execute(array($pseudo));
    $lastId = $reqIdAlbum->fetch();

    $reqUpdateImg = $db->prepare('UPDATE images SET album = ? WHERE id = ?');
    $reqUpdateImg->execute(array($albumName, $lastId[0]));

    if (($_POST['album']) != "new") {
        $message = 'L\'image a bien été ajoutée à l\'album ' . $albumName;
    }
}

/*===========================================================================*/
/*==================== AFFICHAGE DES ALBUMS DEJA CREES ======================*/
/*===========================================================================*/

$album = array();
$images = array();
$indexZ = 0;

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
    <link rel="stylesheet" href="http://hostimage/design/accueil.css">
    <link rel="icon" type="image/png" href="http://hostimage/images/logo-icon-rouge.png">
    <script src="https://kit.fontawesome.com/7b8010a28b.js" crossorigin="anonymous"></script>
</head>

<body class="accueil">

    <?php include('src/header.php'); ?>
    <section id="bienvenue">
        <div id="presentation">
            <p>Bienvenue dans votre espace personnel !</p>
        </div>
    </section>
    <section id="gestion">
        <div id="upload">
            <h1 class="h1-home">Enregistrer une image dans un album</h1>
            <?php if ($step == 0) {
                echo '                    
                    <h2 class="step">Première étape :</h2>
                    <h2>Choisissez l\'image à envoyer</h2>
                    <form id="upload-form" action="accueil.php" method="post" enctype="multipart/form-data">
                            <label for="filename">Nommez votre image (60 caractères maximum)</label>
                            <input type="text" name="filename" maxlength="60">
                            <label for="file">Choisir votre fichier (extension autorisée : .png, .gif, .jpg, .jpeg)</label>
                            <input type="file" name="file" required>
                            <button type="submit">Envoyer</button>
                  </form>';
            } ?>

            <?php if ($step == 1) {
                echo '
                      <h2 class="step">Seconde étape :</h2>
                      <h2>Dans quel album souhaitez vous l\'enregistrer ?</h2>
                      <form id="album-form" action="accueil.php" method="post">
                      <label for="album-list">Choisissez un album ou créez en un nouveau</label>
                      <select name="album" id="album-list">                          
                          <option value="new">Creer un nouvel album</option>';
            ?>

            <?php $reqAlbum = $db->prepare('SELECT album_name FROM album WHERE album_user = ?');
                $reqAlbum->execute(array($pseudo));

                while ($album = $reqAlbum->fetch()) {
                    echo '<option value="' . $album['album_name'] . '">' . $album['album_name'] . '</option>';
                }
                ?>
            <?php echo '</select>
                            <button type="submit">Validez le choix de l\'album</button>
                            </form>';
            }

            if ($step == 2) {
                echo '
                    <h2 class="step">Dernière étape :</h2>
                    <h2>Quel sera le nom de votre nouvel album ? (60 caractères maximum)</h2>
                    <form id="album-new" action="accueil.php" method="post">
                    <label for="album-name-user">Choisir le nom de votre nouvel album</label>
                    <input type="text" name="album-name-user" maxlength="60">                
                    <button type="submit">Validez le choix de l\'album</button>
                    </form>';
            } ?>
        </div>
        <?php if ($error == 0 && $message != '') { ?>
        <div id="alert-success">
            <p><?php echo $message ?></p>
        </div>
        <?php } ?>
        <?php if ($error == 1 && $message != '') { ?>
        <div id="alert-error">
            <p><?php echo $message ?></p>
        </div>
        <?php } ?>

    </section>
    <section id="albums">
        <h1 class="h1-home">Mes albums</h1>
        <div class="album-container">
            <?php
            if (!empty($album)) {
                foreach ($album as $value) {

                    $reqImgFromAlbum = $db->prepare('SELECT file_name FROM images WHERE album = ? AND album_user = ? LIMIT 4');
                    $reqImgFromAlbum->execute(array($value, $pseudo));

                    while ($data = $reqImgFromAlbum->fetch()) {
                        $images[] = $data['file_name'];
                    }

                    echo '
                    <div class="album-card">';
                    foreach ($images as $elmt) {
                        echo '
                        
                        <img  class="album-front-image" src="uploads/' . $pseudo . '/' . $elmt . '">
                        ';
                    }

                    $images = array();
                    $indexZ += 1;

                    echo '
                    <h3>' . $value . '</h3>
                    <div><hr></div>
                    <h4><a class="album-card-link" href="http://hostimage/currentalbum.php?album=' . $value . '">ouvrir l\'album</a></h4>
                    </div>';
                }
            }
            ?>
        </div>
    </section>

    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>