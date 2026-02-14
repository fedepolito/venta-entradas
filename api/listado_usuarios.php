<?php
header('Content-Type: application/json');
require '../conexion.php';
require 'funciones/usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(obtenerUsuarios($mysqli));
}