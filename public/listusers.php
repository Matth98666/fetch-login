<?php

require_once 'Connect.php';
require_once 'PdoSQLite.php';

use SYRADEV\Utils\Connect;
use SYRADEV\db\PdoSQLite;

$connect = Connect::getInstance();
$connect->startSession();
$connect->protectPage();


$db = PdoSQLite::getInstance();
$users = $db->requete('SELECT * FROM users');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $connect->generateCSRFToken(); ?>">
    <title>Liste des utilisateurs</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.min.css" rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="row">
        <div class="col mx-3 my-3">
            <table class="table table-striped table-condensed">
                <tr>
                    <td colspan="6">
                        <a href="adduser.php" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                                <path d="M8.256 14a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Z"/>
                            </svg>
                            Ajouter un utilisateur
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <th>Utilisateur</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
                <?php foreach ($users as $user) { 
                    extract($user);
                    ?>
                    <tr>
                        <td><?= $uid; ?></td>
                        <td><?= $username; ?></td>
                        <td><?= $firstname; ?></td>
                        <td><?= $lastname; ?></td>
                        <td>
                            <a href="edituser.php?userid=<?= $uid; ?>" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd"
                                          d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </a>
                        </td>
                        <td>
                            <?php if($uid!==$_SESSION['uid']) { ?>
                            <a href="deleteuser.php?userid=<?= $uid; ?>" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-file-x" viewBox="0 0 16 16">
                                    <path d="M6.146 6.146a.5.5 0 0 1 .708 0L8 7.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 8l1.147 1.146a.5.5 0 0 1-.708.708L8 8.707 6.854 9.854a.5.5 0 0 1-.708-.708L7.293 8 6.146 6.854a.5.5 0 0 1 0-.708z"/>
                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                </svg>
                            </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<script src="js/docready.min.js"></script>
<script src="js/dashboard.min.js"></script>
</body>
</html>
