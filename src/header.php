<?php



require('src/log.php');
require('src/connect.php');



?>

<header>
    <nav id="nav-menu">
        <img id="img-logo" src="images/logo-ombre-blanc.png" alt="logo host image">
        <ul id="nav-list">
            <li><a href="#about"></a></li>
            <li><a href="#skills"></a></li>
            <?php if (isset($_SESSION['connect'])) { ?>
            <li><?php echo 'Bonjour ' . $_SESSION['pseudo'] . ' !' ?></li>
            <li><a href="http://hostimage/accueil.php">Ajouter une image</a></li>
            <li><a href="http://hostimage/album.php">Gérer mes albums</a></li>
            <li><a href="http://hostimage/src/logout.php">Déconnexion</a></li>
            <?php } else { ?>
            <li><a href="http://hostimage/inscription.php">Inscription</a></li>
            <li><a href="http://hostimage/index.php">Connexion</a></li>
            <?php } ?>
        </ul>
        <button class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </button>
    </nav>
</header>