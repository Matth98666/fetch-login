<?php

require_once 'Connect.php';
require_once 'PdoSQLite.php';

use SYRADEV\Utils\Connect;
use SYRADEV\db\PdoSQLite;

/*
 * if(preg_match((?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$), $_POST['password']):
 * echo 'matched';
 * else:
 * echo 'not matched';
 * endif;
 */

$connect = Connect::getInstance();
$connect->startSession();
$connect->protectPage();
$csrfToken = $connect->generateCSRFToken();
$_GP = array_merge($_GET,$_POST);

// $db = PdoSQLite::getInstance();

$formErrors = [];
$firstnameValidationClass = $lastnameValidationClass = $emailValidationClass = $passwordValidationClass = '';
$mandatory = '(<span class="text-danger">*</span>)';

if(isset($_GP['pwhash']) && isset($_GP['login'])) {

    if(!$connect->validatePostRequest()) {
        header('HTTP/1.1 401 Unauthorized');
        header('Location:login.php');
        exit();
    } else {
        /*** Validation du formulaire ***/

        // Le champ prénom ne peut être vide
        if(empty($_GP['firstname'])) {
            $formErrors[] = 'firstname';
        }
        // Le champ nom ne peut être vide
        if(empty($_GP['lastname'])) {
            $formErrors[] = 'lastname';
        }
        // Le champ adresse email ne peut être vide
        if(empty($_GP['login'])) {
            $formErrors[] = 'email';
        }

        // Le champ password ne peut être vide
        if(empty($_GP['pwhash'])) {
            $formErrors[] = 'password';
        }

        // Si le formulaire est validé
        if(empty($formErrors)) {
            // Ajout des données en en base de données
            $users->users->{$key} = new StdClass();
            $users->users->{$key}->firstname = $_GP['firstname'];
            $users->users->{$key}->lastname = $_GP['lastname'];
            $users->users->{$key}->username = $connect->aesDecrypt(base64_decode($_GP['login']), $csrfToken);
            $users->users->{$key}->password = $connect->argon2idHash($connect->aesDecrypt(base64_decode($_GP['pwhash']), $csrfToken));

            // Sauvegarde de l'ancien fichier des utilisateurs
            copy($usersFile, $oldUsersFile);

            // Sauvegarde le nouveau fichier des utilisateurs
            try {
                $writeUsers = file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
            if($writeUsers !== false) {
                header('Location:listusers.php');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $csrfToken; ?>">
    <title>Ajouter un utilisateur</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.min.css" rel="stylesheet">
    <link href="css/animbg.min.css" rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<div class="container mb-5">
    <?php require_once 'header.php'; ?>
    <div class="row d-flex justify-content-center">
        <div class="col-md-5 mx-3 my-3">
            <h3>Ajouter un utilisateur</h3>
            <div class="small">Les champs précédés d'une astérisque <?= $mandatory; ?> sont <span
                        class="text-danger">obligatoires</span>.</div>
        </div>
    </div>
    <div class="row d-flex justify-content-center border rounded-3 mb-5">
        <div class="col-md-5 mx-3 my-3">
            <form action="adduser.php" id="adduserform" method="post" autocomplete="off">
                <?php
                $firstname = $_GP['firstname'] ?? '';
                if (isset($formErrors) && in_array('firstname', $formErrors)) {
                    $firstnameValidationClass = ' is-invalid';
                }
                ?>
                <div class="form-group has-validation mb-3">
                    <label for="firstname" class="form-label">Prénom <?= $mandatory;?></label>
                    <input type="text" id="firstname" name="firstname" class="form-control<?= $firstnameValidationClass;?>" value="<?= $firstname; ?>" autofocus>
                    <div class="invalid-feedback">Veuillez saisir un prénom !</div>
                </div>
                <?php
                $lastname = $_GP['lastname'] ?? '';
                if (isset($formErrors) && in_array('lastname', $formErrors)) {
                    $lastnameValidationClass = ' is-invalid';
                }
                ?>
                <div class="form-group has-validation mb-3">
                    <label for="lastname" class="form-label">Nom <?= $mandatory;?></label>
                    <input type="text" id="lastname" name="lastname" class="form-control<?= $lastnameValidationClass;?>" value="<?= $lastname; ?>">
                    <div class="invalid-feedback">Veuillez saisir un nom !</div>
                </div>
                <?php
                $email = (isset($_GP['login']) && !empty($_GP['login'])) ? $connect->aesDecrypt(base64_decode($_GP['login']), $csrfToken) : '';
                if (isset($formErrors) && in_array('email', $formErrors)) {
                    $emailValidationClass = ' is-invalid';
                }
                ?>
                <div class="form-group has-validation mb-3">
                    <label for="email" class="form-label">Adresse email <?= $mandatory;?></label>
                    <input type="text" id="email" class="form-control<?= $emailValidationClass;?>" value="<?= $email; ?>">
                    <input type="hidden" id="login" name="login"  value="">
                    <input type="hidden" name="csrf-token" value="<?= $csrfToken; ?>">
                    <div class="invalid-feedback">Veuillez saisir une adresse mail valide !</div>
                </div>
                <?php
                $password = (isset($_GP['pwhash']) && !empty($_GP['pwhash'])) ? $connect->aesDecrypt(base64_decode($_GP['pwhash']), $csrfToken) : '';
                if (isset($formErrors) && in_array('password', $formErrors)) {
                    $passwordValidationClass = ' is-invalid';
                }
                ?>
                <div class="form-group has-validation mb-3">
                    <label for="password" class="form-label">Mot de passe <?= $mandatory;?></label>
                    <input type="password" id="password" class="form-control<?= $passwordValidationClass; ?>" value="<?= $password; ?>" autocomplete="new-password">
                    <div class="invalid-feedback">Veuillez saisir un mot de passe !</div>
                    <input type="hidden" id="pwhash" name="pwhash" value="">
                </div>

                <div class="form-group mb-3">
                    <a href="listusers.php" class="btn btn-secondary">Annuler</a>
                    <button id="adduser" type="button" class="btn btn-primary float-end">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="js/docready.min.js"></script>
<script src="js/aesjson.min.js"></script>
<script src="js/crypto-js.min.js"></script>
<script src="js/dashboard.min.js"></script>
</body>
</html>
