<?php
require_once "functions.php";

// Recoge los datos enviados por POST desde el cliente
$user = $_POST["user"] ?? "";
$password = $_POST["password"] ?? "";

// Lee la lista de usuarios del fichero users.json
$users = readJsonFile(__DIR__ . "/../../users/users.json");

// Responde solo con un texto (sin HTML), como pide el enunciado
if ($users !== null && authenticateUser($users, $user, $password)) {
    http_response_code(200); // Autenticación correcta
    echo "OK";
} else {
    http_response_code(401); // Autenticación incorrecta
    echo "ERROR";
}