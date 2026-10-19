<?php
// Настройки подключения к БД
$host = 'localhost';
$db = 'students_db';
$user = 'root'; // 
$pass = '';     // 
$charset = 'utf8mb4';

// Устанавливаем соединение через PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Ошибка подключения к БД: " . htmlspecialchars($e->getMessage()));
}

// 1. Обработка добавления товара (HTTP POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_title'], $_POST['prod_price'])) {
    $title = trim($_POST['prod_title']);
    $price = trim($_POST['prod_price']);
    
    // Валидация
    $errors = [];
    if (empty($title) || mb_strlen($title) > 100) {
        $errors[] = 'Наименование товара должно быть от 1 до 100 символов.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = 'Цена должна быть положительным числом.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (title, price) VALUES (:title, :price)");
        $stmt->execute([
            ':title' => $title,
            ':price' => $price
        ]);
        // Редирект для предотвращения повторной отправки формы при обновлении страницы
        header("Location: catalog.php");
        exit;
    }
}

// 2. Обработка просмотра карточки товара (HTTP GET)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if ($product) {
        echo "<h1>" . htmlspecialchars($product['title']) . "</h1>";
        echo "<p>Цена: " . number_format($product['price'], 2, '.', ' ') . " руб.</p>";
        echo '<p><a href="catalog.php">Вернуться в каталог</a></p>';
        exit;
    } else {
        http_response_code(404);
        echo "<h1>Товар не найден</h1>";
        echo '<p><a href="catalog.php">Вернуться в каталог</a></p>';
        exit;
    }
}

// 3. Вывод каталога товаров (по умолчанию)
$stmt = $pdo->query("SELECT id, title, price FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Каталог товаров</h1>
    
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <a href="index.html">Добавить новый товар</a>
    
    <?php if (empty($products)): ?>
        <p>Каталог пуст.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Наименование</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($products as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= number_format($item['price'], 2, '.', ' ') ?> руб.</td>
                    <td><a href="catalog.php?id=<?= $item['id'] ?>">Подробнее</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>