<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    http_response_code(400);
    die('Некорректный ID товара.');
}

$stmt = $pdo->prepare('SELECT id, title, price FROM products WHERE id = :id');
$stmt->execute([':id' => (int)$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    die('Товар не найден.');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['title']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($product['title']) ?></h1>
    <p>Цена: <?= number_format($product['price'], 2, ',', ' ') ?> ₽</p>
    <a href="catalog.php">← Вернуться в каталог</a>
</body>
</html>
