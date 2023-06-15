<?php
require_once 'Connect.php';
use SYRADEV\Utils\Connect;

$connect = Connect::getInstance();
$connect->startSession();

// Check si la requête est bien une requête Ajax et qu'on est bien sur le bon serveur.
$requestIsAjax = $connect->ajaxCheck() && $connect->domainCheck();

// Check du CSRF token
$validAjax = $connect->validateAjaxRequest();

if ($requestIsAjax && $validAjax) {
    $ajaxRequest = json_decode(file_get_contents('php://input'));
    if (isset($ajaxRequest) && !empty($ajaxRequest)) {
        if (isset($ajaxRequest->type) && $ajaxRequest->type === 'cnx') {
            if (isset($ajaxRequest->action) && $ajaxRequest->action === 'disconnect') {
                session_unset();
                session_destroy();
                echo json_encode([
                    'status' => 200,
                    'action' => 'cnx',
                    'disconnected' => true
                ]);
            }
        }
    }
} else {
    echo json_encode([
        'status' => 200,
        'action' => 'cnx',
        'disconnected' => true
    ]);
}
