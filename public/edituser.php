<?php

require_once 'Connect.php';
use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();
$connect->protectPage();
$csrfToken = $connect->generateCSRFToken();
$_GP = array_merge($_GET,$_POST);
$usersFile = __DIR__ . '/../../users.json';
$oldUsersFile = __DIR__ . '/../../users.json.bak';
$users = json_decode(file_get_contents($usersFile));
$formErrors = [];
$firstnameValidationClass = $lastnameValidationClass = $emailValidationClass = '';
$mandatory = '(<span class="text-danger">*</span>)';
$key = 0;
if(!isset($_GP['userid'])) {
    header('Location:listusers.php');
    exit();
} else {
    $key = $_GP['userid'];
}

if(isset($_GP['pwhash']) && isset($_GP['login'])) {

    if(!$connect->validatePostRequest() || $key === 0) {
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
        if(empty($formErrors)) {
            // Mise à jour des données en mémoire
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
    <title>Modifier un utilisateur</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.min.css" rel="stylesheet">
    <link href="css/animbg.min.css" rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="row d-flex justify-content-center">
        <div class="col-md-5 mx-3 my-3">
            <h3>Edition d'un utilisateur</h3>
            <div class="small">Les champs précédés d'une astérisque <?= $mandatory; ?> sont <span
                        class="text-danger">obligatoires</span>.</div>
        </div>
    </div>
    <div class="row d-flex justify-content-center border rounded-3">
        <div class="col-md-5 mx-3 my-3">
            <form action="edituser.php" id="moduserform" method="post" autocomplete="off">
                <?php
                $firstname = $_GP['firstname'] ?? $users->users->{$key}->firstname;
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
                $lastname = $_GP['lastname'] ?? $users->users->{$key}->lastname;
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
                $email = (isset($_GP['login']) && !empty($_GP['login'])) ? $connect->aesDecrypt(base64_decode($_GP['login']), $csrfToken) : $users->users->{$key}->username;
                if (isset($formErrors) && in_array('email', $formErrors)) {
                    $emailValidationClass = ' is-invalid';
                }
                ?>
                <div class="form-group has-validation mb-3">
                    <label for="email" class="form-label">Adresse email <?= $mandatory;?></label>
                    <input type="text" id="email" class="form-control<?= $emailValidationClass;?>" value="<?= $email; ?>">
                    <input type="hidden" id="login" name="login"  value="">
                    <input type="hidden" name="userid" value="<?= $key ?>">
                    <input type="hidden" name="csrf-token" value="<?= $csrfToken; ?>">
                    <div class="invalid-feedback">Veuillez saisir une adresse mail valide !</div>
                </div>
                <?php
                $password = (isset($_GP['pwhash']) && !empty($_GP['pwhash'])) ? $connect->aesDecrypt(base64_decode($_GP['pwhash']), $csrfToken) : '';
                ?>
                <div class="form-group mb-1">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" class="form-control" value="<?= $password; ?>" autocomplete="new-password">
                    <input type="hidden" id="pwhash" name="pwhash" value="">
                </div>
                <div class="form-group mb-5">
                    <div class="small">Saisissez un mot de passe seulement si vous souhaitez le changer.</div>
                </div>
                <div class="form-group mb-3">
                    <a href="listusers.php" class="btn btn-secondary">Annuler</a>
                    <button id="moduser" type="button" class="btn btn-primary float-end">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<br><br>
<script src="js/docready.min.js"></script>
<script src="js/aesjson.min.js"></script>
<script src="js/crypto-js.min.js"></script>
<script src="js/dashboard.min.js"></script>
</body>
</html>
