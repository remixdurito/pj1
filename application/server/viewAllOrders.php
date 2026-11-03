<?php
require_once "functions.php";

// Lee todas las comandas del fichero orders.json
$orders = readJsonFile(__DIR__ . "/../../orders/orders.json");

if ($orders === null) {
    // El fichero no existe o no se puede abrir: 404 + página de error
    sendNotFound("The orders file was not found or could not be opened.");
    exit;
}

// Todo correcto: 200 OK + página con la tabla
http_response_code(200);
renderOrdersPage($orders);