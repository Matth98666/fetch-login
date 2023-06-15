<?php

require_once 'Connect.php';
use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $connect->generateCSRFToken(); ?>">
    <meta http-equiv="refresh" content="1800">
    <title>Fetch login</title>
    <?php include_once('favicon.html'); ?>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.min.css" rel="stylesheet">
</head>
<body class="text-center" oncontextmenu="return false;">
<main class="form-signin w-100 m-auto border border-1 rounded-3">
    <form autocomplete="off">
        <img class="mb-4" src="imgs/syradev.svg" alt="Syradev &copy; <?= date('Y'); ?>">
        <h3 class="mb-3 fw-normal w-full">Connexion</h3>
        <div class="form-floating">
            <input type="email" class="form-control" id="login" placeholder="name@domain.tld">
            <label for="login">Adresse Email</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="password" placeholder="Password">
            <label for="password">Mot de passe</label>
        </div>
        <button id="loginBtn" class="w-100 btn btn-lg btn-primary" type="button">Se connecter</button>
        <p class="mt-5 mb-3 text-body-secondary">Syradev &copy; <?= date('Y'); ?></p>
    </form>
</main>

<script src="js/docready.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/crypto-js.min.js"></script>
<script src="js/aesjson.min.js"></script>
<script src="js/login.min.js"></script>
</body>
</html>