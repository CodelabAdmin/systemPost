<?php

session_start();
$user = json_decode(file_get_contents('php://input'), true)['usuario_logueado'];

$_SESSION['user'] = true;

$_SESSION['data_user'] = [
    'id' => $user['id'],
    'nombre' => $user['nombre'],
    'rol' => $user['rol'],
];

echo json_encode($_SESSION['data_user']);
?>