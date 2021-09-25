<?php

    if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

        //STOCKAGE DANS UNE VARIABLE
        $secret = htmlspecialchars($_COOKIE['auth']);

        //VERIFICATION SI L'ID SECRET DU COOKIE EST LIE A UN COMPTE

        require('src/connect.php');

        $req = $db->prepare('SELECT COUNT(*) AS numberAccount FROM user WHERE secret = ?');
        $req->execute(array($secret));

        while($user = $req->fetch()) {

            if($user['numberAccount'] == 1 && $user['blocked'] == 0) {

                $reqUser = $db->prepare('SELECT * FROM user WHERE secret = ?');
                $reqUser->execute(array($secret));

                while($userAccount = $reqUser->fetch()) {

                    $_SESSION['connect'] = 1;
				    $_SESSION['pseudo'] = $userAccount['pseudo'];

                }

            }   
        }

    }

    if(isset($_SESSION['connect'])) {

        require('src/connect.php');

        $req = $db->prepare('SELECT * FROM user WHERE email = ?');
        $req->execute(array($_SESSION['email']));

        while($user = $req->fetch()) {

            if($user['blocked'] == 1) {
                header('location: http://netflix/src/logout.php');
                exit();
            }

        }

    }