<?php

require_once 'Connect.php';
use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();
$connect->protectPage();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $connect->generateCSRFToken(); ?>">
    <title>Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.min.css" rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<video id="bgvid" autoplay muted loop preload>
    <source src="imgs/clouds.mp4" type="video/mp4">
</video>
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="row">
        <div class="col">
            <img class="dumbo" src="imgs/dumbo.png" alt="PHP Elephant">
        </div>
    </div>
    <footer class="footer mt-auto py-3 text-center">
        <div class="container">
            <span class="text-muted">&copy; Abdul Yves Hakim Fly</span>
        </div>
    </footer>

</div>
<script src="js/docready.min.js"></script>
<script src="js/dashboard.min.js"></script>
</body>
</html>
