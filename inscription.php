<?php

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');

//SI SESSION EXISTE ET DONC USER CONNECTE ALORS REDIRECTION

if (isset($_SESSION['connect'])) {
    header('location: http://hostimage/accueil.php');
    exit();
}

//================================================================================
//============== INSCRIPTION USER ET STOCKAGE DE SES INFO ========================
//================================================================================

//VERIFICATION DE LA RECEPTION DE L'URL  

if (!empty($_POST['email']) && !empty($_POST['pseudo']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {

    //APPEL DE LA BASE DE DONNEES

    require('src/connect.php');

    // STOCKAGE DANS UNE VARIABLE

    $email = htmlspecialchars($_POST['email']);
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $password = htmlspecialchars($_POST['password']);
    $passwordTwo = htmlspecialchars($_POST['password_two']);

    //VERIFICATION DE LA VALIDITE DE L'EMAIL

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: http://netflix/inscription.php?error=true&messageEmail=true.');
        exit();
    }

    //VERIFICATION SI L'EMAIL EXISTE DEJA DANS LA BASE DE DONNEES

    $req = $db->prepare('SELECT COUNT(*) AS x FROM user WHERE email = ?');
    $req->execute(array($email));

    while ($result = $req->fetch()) {

        if ($result['x'] != 0) {

            header('location: http://hostimage/inscription.php?error=true&messageEmailUse=true');
            exit();
        }
    }

    //VERIFICATION SI LE PSEUDO EXISTE DEJA DANS LA BASE DE DONNEES

    $req = $db->prepare('SELECT COUNT(*) AS x FROM user WHERE pseudo = ?');
    $req->execute(array($pseudo));

    while ($result = $req->fetch()) {

        if ($result['x'] != 0) {

            header('location: http://hostimage/inscription.php?error=true&messagePseudo=true');
            exit();
        }
    }

    // VERIFICATION MOT DE PASSSE

    $taille = strlen($password);

    if ($taille < 8) {
        header('location: http://hostimage/inscription.php?error=true&messageTaille=true');
        exit();
    }

    if (strpos($password, $pseudo) !== false) {
        header('location: http://hostimage/inscription.php?error=true&messagePasswordP=true');
        exit();
    }

    if ($password != $passwordTwo) {
        header('location: http://hostimage/inscription.php?error=true&messagePassword=true');
        exit();
    }

    //SI OK, CHIFFRAGE DU MOT DE PASSE

    $salt = time() . sha1($email);
    $password = $salt . sha1($password . "123") . "25";


    // SI OK, CREATION DU SECRET (correspond à l'id du cookie)

    $secret = sha1($email) . time();
    $secret = sha1($secret) . time();

    //SI OK, CREATION DU DOSSIER PERONNEL QUI CONTIENDRA LES SOUS DOSSIERS ALBUMS

    $dir_path = 'uploads/' . $pseudo;

    mkdir($dir_path);

    // SI OK, ENREGISTREMENT DANS LA BASE DE DONNEES        

    $req = $db->prepare('INSERT INTO user(email, pseudo, password, secret, salt) VALUES(?, ?, ?, ?, ?)');
    $req->execute(array($email, $pseudo, $password, $secret, $salt));
    header('location: http://hostimage/inscription.php?messageSuccess=true');
    exit();
}

?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Host Image</title>
    <link rel="stylesheet" href="http://hostimage/design/default.css">
    <link rel="icon" type="image/png" href="http://hostimage/images/logo-icon-rouge.png">
    <script src="https://kit.fontawesome.com/7b8010a28b.js" crossorigin="anonymous"></script>
</head>

<body class="home">

    <?php include('src/header.php'); ?>
    <section>
        <div id="presentation">
            <p>Stockage en ligne de vos photos
                <br />Créer vos albums et partagez les !
            </p>
        </div>
    </section>
    <section>
        <div id="login-body">
            <h1>S'inscrire</h1>
            <?php if (isset($_GET['error']) && isset($_GET['messagePassword'])) { ?>
            <div class="alert error">
                <p>Les mots de passe ne sont pas identiques.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messageEmail'])) { ?>
            <div class="alert error">
                <p>L'adresse mail est invalide.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messageEmailUse'])) { ?>
            <div class="alert error">
                <p>Cet email est déjà utilisé par un utilisateur</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messageTaille'])) { ?>
            <div class="alert error">
                <p>Le mot de passe doit contenir au moins 8 caractères.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messagePseudo'])) { ?>
            <div class="alert error">
                <p>Ce pseudo est déjà utilisé par un utilisateur</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messagePasswordP'])) { ?>
            <div class="alert error">
                <p>Le mot de passe ne peut contenir le pseudo.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['messageSuccess'])) { ?>
            <div class="alert success">
                <p>Vous êtes maintenant inscrit. <a href="http://hostimage/index.php">Connectez vous</a>.</p>
            </div>
            <?php } ?>
            <form method="post" action="inscription.php">
                <input type="email" name="email" placeholder="Votre adressse mail" required />
                <input type="text" name="pseudo" placeholder="Choississez un pseudo" required />
                <input type="password" name="password" placeholder="Mot de passe (8 caractères minimum)" required />
                <input type="password" name="password_two" placeholder="Confirmer le mot de passe" required />
                <button type="submit">S'inscrire</button>
                <div id="remember">
                </div>
            </form>
            <p class="grey">Vous possédez déjà un compte ?<a id="inscription" href="index.php">Connectez vous.</a>.</p>
        </div>

    </section>

    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>