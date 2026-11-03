<?php
require_once "functions.php";

$ordersPath = __DIR__ . "/../../orders/orders.json";

// Construye el array asociativo multidimensional de la comanda
$products = buildProductList($_POST["products"] ?? []);
$total = calculateTotalWithVat($products);

$order = [
    "orderId" => $_POST["orderId"] ?? "",
    "customer" => [
        "fullName" => $_POST["fullName"] ?? "",
        "address" => $_POST["address"] ?? "",
        "email" => $_POST["email"] ?? "",
        "phone" => $_POST["phone"] ?? ""
    ],
    "products" => $products,
    "totalWithVat" => $total
];

// Si el fichero no existe, se crea con la primera comanda.
// Si existe pero no se puede leer, se responde con un 404.
if (file_exists($ordersPath)) {
    $orders = readJsonFile($ordersPath);
    if ($orders === null) {
        sendNotFound("The orders file could not be opened.");
        exit;
    }
} else {
    $orders = [];
}

$orders[] = $order;

if (!writeJsonFile($ordersPath, $orders)) {
    sendNotFound("The orders file could not be saved.");
    exit;
}

// Respuesta única: solo el resultado, sin HTML/JS/CSS
http_response_code(200);
echo number_format($total, 2, ".", "");