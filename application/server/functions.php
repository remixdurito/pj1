<?php

// Lee un fichero JSON y lo devuelve como array asociativo.
// Devuelve null si el fichero no existe o no se puede leer.
function readJsonFile($path)
{
    if (!file_exists($path)) {
        return null;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        return null;
    }
    return json_decode($content, true);
}

// Comprueba si el usuario y la contraseña coinciden con algún usuario de la lista.
// La contraseña se compara con el hash guardado usando password_verify.
function authenticateUser($users, $user, $password)
{
    foreach ($users as $u) {
        // CORREGIDO: Se cambia "$u['user']" por "$u['username']" para que coincida con el JSON
        if ($u["username"] === $user && password_verify($password, $u["password"])) {
            return true;
        }
    }
    return false;
}

// Guarda un array en un fichero en formato JSON.
// Devuelve true si se ha podido guardar.
function writeJsonFile($path, $data)
{
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($path, $json, LOCK_EX) !== false;
}

// Genera una página HTML de error con tres botones de vuelta.
function renderErrorPage($message)
{
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Error - Order Management App</title>
  <link rel="stylesheet" href="../client/css/styles.css">
</head>
<body>
  <div class="container center">
    <h1>Error</h1>
    <p class="error">' . htmlspecialchars($message) . '</p>
    <a class="button" href="../client/viewMenu.html">Back to view orders menu</a>
    <a class="button" href="../client/menu.html">Back to operations menu</a>
    <a class="button secondary" href="../client/index.html">Back to start</a>
  </div>
</body>
</html>';
}

// Envía un 404 y la página de error con el mensaje indicado.
// Llama a renderErrorPage, así no se repite código.
function sendNotFound($message)
{
    http_response_code(404);
    renderErrorPage($message);
}

// Convierte los productos recibidos por POST en un array asociativo
// multidimensional, quedándose solo con los productos marcados y con cantidad > 0.
function buildProductList($postProducts)
{
    $products = [];
    foreach ($postProducts as $p) {
        $quantity = (int) ($p["quantity"] ?? 0);
        if (isset($p["selected"]) && $quantity > 0) {
            $products[] = [
                "name" => $p["name"],
                "unitPrice" => (float) $p["price"],
                "quantity" => $quantity
            ];
        }
    }
    return $products;
}

// Calcula el precio total de la comanda con el 21% de IVA.
function calculateTotalWithVat($products)
{
    $total = 0;
    foreach ($products as $p) {
        $total += $p["unitPrice"] * $p["quantity"];
    }
    // CORREGIDO: Se añade el asterisco (*) que faltaba para multiplicar por 1.21
    return round($total * 1.21, 2);
}

// Devuelve la comanda con el código indicado, o null si no existe.
function findOrderById($orders, $id)
{
    foreach ($orders as $order) {
        if ($order["orderId"] === $id) {
            return $order;
        }
    }
    return null;
}

// Genera la tabla HTML con las comandas recibidas.
// Sirve tanto para ver todas las comandas como para ver una sola.
function renderOrdersTable($orders)
{
    echo '<table>
  <tr>
    <th>Order code</th>
    <th>Customer full name</th>
    <th>Customer address</th>
    <th>Customer email</th>
    <th>Customer phone</th>
    <th>Products</th>
    <th>Total price with 21% VAT</th>
  </tr>';

    foreach ($orders as $order) {
        // Une todos los productos de la comanda en un solo texto
        $productsText = [];
        foreach ($order["products"] as $p) {
            $productsText[] = $p["name"] . " x" . $p["quantity"]
                . " (" . number_format($p["unitPrice"], 2, ".", "") . " €)";
        }

        echo '<tr>
    <td>' . htmlspecialchars($order["orderId"]) . '</td>
    <td>' . htmlspecialchars($order["customer"]["fullName"]) . '</td>
    <td>' . htmlspecialchars($order["customer"]["address"]) . '</td>
    <td>' . htmlspecialchars($order["customer"]["email"]) . '</td>
    <td>' . htmlspecialchars($order["customer"]["phone"]) . '</td>
    <td>' . htmlspecialchars(implode(", ", $productsText)) . '</td>
    <td>' . number_format($order["totalWithVat"], 2, ".", "") . ' €</td>
  </tr>';
    }

    echo '</table>';
}

// Genera la página HTML completa con la tabla y los tres botones de vuelta.
// La reutilizan "ver todas" y "ver una", así no se repite código.
function renderOrdersPage($orders)
{
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders - Order Management App</title>
  <link rel="stylesheet" href="../client/css/styles.css">
</head>
<body>
  <div class="container" style="max-width: 1000px;">
    <h1>Orders</h1>';

    renderOrdersTable($orders);

    echo '<div class="center">
      <a class="button" href="../client/viewMenu.html">Back to view orders menu</a>
      <a class="button" href="../client/menu.html">Back to operations menu</a>
      <a class="button secondary" href="../client/index.html">Back to start</a>
    </div>
  </div>
</body>
</html>';
}