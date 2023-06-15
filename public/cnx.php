<?php

require_once 'Connect.php';

use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();
$csrfToken = $connect->generateCSRFToken();

// Check si la requête est bien une requête Ajax et qu'on est bien sur le bon serveur.
$requestIsAjax = $connect->ajaxCheck() && $connect->domainCheck();

// Check du CSRF token
$validAjax = $connect->validateAjaxRequest();

if ($requestIsAjax && $validAjax) {
    // On prépare le header pour ne rien stocker en cache client
    header("Cache-Control: no-store, no-transform, max-age=0, private");
    // On récupère les données envoyées par le fetch
    $ajaxRequest = json_decode(file_get_contents('php://input'));
    // La requête fetch contient bien des données dans son body
    if (isset($ajaxRequest) && !empty($ajaxRequest)) {
        // Si le type de la requête est une demande de connexion
        if (isset($ajaxRequest->type) && $ajaxRequest->type === 'cnx') {
            if (isset($ajaxRequest->action) && $ajaxRequest->action === 'connect') {
                $connected = false;
                // Si le fetch envoie bien une variable login et une variable pwash
                if (isset($ajaxRequest->login) && isset($ajaxRequest->pwash)) {
                    // Je charge le fichier JSON des utilisateurs
                    $userFile = __DIR__ . '/../../users.json';
                    // $data sera un objet contenant la liste des utilisateurs
                    $data = json_decode(file_get_contents($userFile));
                    // Je parcours tous les utilisateurs
                    foreach ($data->users as $key => $user) {
                        // Si le login décrypté est égal au login stocké dans le fichier users.json
                        if ($connect->aesDecrypt(base64_decode($ajaxRequest->login), $csrfToken) === $user->username) {

                            //$pwclear = $connect->aesDecrypt(base64_decode($ajaxRequest->pwash), $csrfToken);
                            //echo $pwclear . '<hr>';
                            //echo $connect->argon2idHash($pwclear). '<hr>';
                            //exit();

                            // Si le mot de passe décrypté est égal au mot de passe hashé dans le fichier users.json
                            if ($connect->argon2idHashVerify(
                                $connect->aesDecrypt(base64_decode($ajaxRequest->pwash), $csrfToken),
                                $user->password)) {
                                // L'utilisateur est reconnu, on crée la session serveur
                                $connected = true;
                                $_SESSION['username'] = $user->username;
                                $_SESSION['firstname'] = $user->firstname;
                                $_SESSION['lastname'] = $user->lastname;
                                $_SESSION['uid'] = $key;

                                echo json_encode([
                                    'status' => 200,
                                    'action' => 'cnx',
                                    'connected' => true
                                ]);
                                break;
                            }
                        }
                    }
                    if (!$connected) {
                        echo json_encode([
                            'status' => 401,
                            'action' => 'cnx',
                            'connected' => false
                        ]);
                    }
                }
            }
        }
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}


