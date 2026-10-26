<?php
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['prod_title'] ?? '');
    $priceRaw = $_POST['prod_price'] ?? '';

    if (empty($title) || strlen($title) > 100) {
        $message = 'Ошибка: наименование должно быть от 1 до 100 символов.';
    } elseif (!is_numeric($priceRaw) || $priceRaw < 0) {
        $message = 'Ошибка: цена должна быть неотрицательным числом.';
    } else {
        $price = (float)$priceRaw;

        $stmt = $pdo->prepare('INSERT INTO products (title, price) VALUES (:title, :price)');
        $stmt->execute([
            ':title' => $title,
            ':price' => $price,
        ]);

        header('Location: catalog.php?msg=success');
        exit;
    }
}

$products = $pdo->query('SELECT id, title, price FROM products')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
</head>
<body>
    <h1>Каталог товаров</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <p style="color: green;">Товар успешно добавлен!</p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="catalog.php">
        <label>Наименование товара:
            <input type="text" name="prod_title" maxlength="100" required>
        </label><br><br>
        <label>Цена товара:
            <input type="number" step="0.01" name="prod_price" min="0" required>
        </label><br><br>
        <button type="submit">Добавить товар</button>
    </form>

    <hr>

    <h2>Список товаров</h2>
    <?php if (count($products) === 0): ?>
        <p>Товаров пока нет.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($products as $p): ?>
                <li>
                    <a href="product.php?id=<?= htmlspecialchars((string)$p['id']) ?>">
                        <?= htmlspecialchars($p['title']) ?> — <?= number_format($p['price'], 2, ',', ' ') ?> ₽
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
