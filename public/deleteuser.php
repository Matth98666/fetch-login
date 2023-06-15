<?php

require_once 'Connect.php';

use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();
$connect->protectPage();
$csrfToken = $connect->generateCSRFToken();
$_GP = array_merge($_GET, $_POST);
$usersFile = __DIR__ . '/../../users.json';
$oldUsersFile = __DIR__ . '/../../users.json.bak';
$users = json_decode(file_get_contents($usersFile));
$key = 0;
if(!isset($_GP['userid']) || $_GP['userid'] === $_SESSION['uid']) {
    header('Location:listusers.php');
    exit();
} else {
    $key = $_GP['userid'];
}
if(isset($_GP['deleteuser']) && $_GP['deleteuser']=== '1' && $connect->validatePostRequest()) {

    // Supprime l'utilisateur
    unset($users->users->$key);

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $csrfToken; ?>">
    <title>Supprimer un utilisateur</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.min.css" rel="stylesheet">
    <link href="css/animbg.min.css" rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="row d-flex justify-content-center">
        <div class="col-md-5 mx-3 my-3">
            <h3>Supprimer un utilisateur</h3>
        </div>
    </div>
    <div class="row d-flex justify-content-center border rounded-3 mb-5">
        <div class="col-md-5 mx-3 my-3">
            <form action="deleteuser.php" id="deleteserform" method="post" autocomplete="off">
                <p>Souhaitez-vous réeelement supprimer l'utilisateur :<p>
                <h3 class="text-center"><?= $users->users->{$key}->firstname . ' ' . $users->users->{$key}->lastname ;?> ?</h3>
                <a href="listusers.php" class="btn btn-secondary">NON</a>
                <input type="hidden" name="userid" value="<?= $key; ?>">
                <input type="hidden" name="csrf-token" value="<?= $csrfToken; ?>">
                <button class="btn btn-primary float-end" name="deleteuser" id="deleteuser" value="1">OUI</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
