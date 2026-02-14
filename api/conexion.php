<?php
$mysqli = new mysqli("localhost", "root", "", "entradas_online");
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}