<?php

//DEMARRAGE DE LA SESSION

session_start();

//VERIFICATION COOKIE POUR VOIR SI USER EST DEJA AUTHENTIFIE

require('src/log.php');

//SI SESSION EXISTE ET DONC USER CONNECTE ALORS REDIRECTION

if (isset($_SESSION['connect'])) {
	header('location: http://hostimage/accueil.php');
	exit();
}

//================================================================================
//============== CONNECTION USER ET VERIFICATION DE SES INFO =====================
//================================================================================

if (!empty($_POST['pseudo']) && !empty($_POST['password'])) {

	require('src/connect.php');

	//STOCKAGE DANS UNE VARIABLE

	$pseudo = htmlspecialchars($_POST['pseudo']);
	$password = htmlspecialchars($_POST['password']);

	// VERIFICATION SI USER EXISTE DANS BDD

	$req = $db->prepare('SELECT COUNT(*) AS x FROM user WHERE email = ? OR pseudo = ?');
	$req->execute(array($pseudo, $pseudo));

	while ($user = $req->fetch()) {

		if ($user['x'] != 1) {
			header('location: http://hostimage/index.php?error=true&messageUser=true');
			exit();
		}
	}

	//VERIFICATION CORRESPONDANCE MDP ET EMAIL USER

	$donnees = $db->prepare('SELECT email, pseudo, password, salt, blocked FROM user WHERE email = ? OR pseudo = ?');
	$donnees->execute(array($pseudo, $pseudo));

	while ($user = $donnees->fetch()) {

		$compare = $user['salt'] . sha1($password . "123") . "25";

		if ($compare == $user['password'] && $user['blocked'] == 0) {

			$_SESSION['connect'] = true;
			$_SESSION['email'] = $user['email'];
			$_SESSION['pseudo'] = $user['pseudo'];

			if (isset($_POST['auto'])) {

				setcookie('auth', $user['secret'], time() + 364 * 24 * 3600, "/", null, false, true);
			}

			header('location: http://hostimage/accueil.php?messageSuccess');
			exit();
		} elseif ($user['blocked'] == 1) {

			header('location: http://hostimage/index.php?error=true&messageBlocked=true');
		} else {
			header('location: http://hostimage/index.php?error=true&messagePassword=true');
			exit();
		}
	}
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
            <h1>Se connecter</h1>
            <?php if (isset($_GET['error']) && isset($_GET['messageUser'])) { ?>
            <div class="alert error">
                <p>Utilisateur non reconnu.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messageBlocked'])) { ?>
            <div class="alert error">
                <p>Utilisateur bloqué. Vous n'avez pas accès à ce site.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && isset($_GET['messagePassword'])) { ?>
            <div class="alert error">
                <p>Mot de passe incorrect.</p>
            </div>
            <?php } ?>
            <?php if (isset($_GET['messageLogout'])) { ?>
            <div class="alert success">
                <p>Déconnexion réussie. A bientôt !</p>
            </div>
            <?php } ?>
            <form method="post" action="index.php">
                <input type="text" name="pseudo" placeholder="Email ou pseudo" required />
                <input type="password" name="password" placeholder="Mot de passe" required />
                <button type="submit">S'identifier</button>
                <div id="remember">
                    <label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
                </div>
            </form>
            <p class="grey">Première visite sur Hostimage ? <a id="inscription"
                    href="inscription.php">Inscrivez-vous</a>.</p>
        </div>

    </section>

    <?php include('src/footer.php'); ?>
    <script src="script.js"></script>
</body>

</html>