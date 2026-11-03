<?php
require_once "functions.php";

// Recoge el código de la comanda enviado por GET
$id = $_GET["id"] ?? "";

// Lee todas las comandas del fichero orders.json
$orders = readJsonFile(__DIR__ . "/../../orders/orders.json");

if ($orders === null) {
    // El fichero no existe o no se puede abrir
    sendNotFound("The orders file was not found or could not be opened.");
    exit;
}

// Busca la comanda por su código
$order = findOrderById($orders, $id);

if ($order === null) {
    // No existe una comanda con ese código
    sendNotFound("Order not found: there is no order with code " . $id . ".");
    exit;
}

// Todo correcto: 200 OK + tabla con una sola comanda
http_response_code(200);
renderOrdersPage([$order]);