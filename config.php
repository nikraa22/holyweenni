<?php

$host = 'localhost';
$dbName = 'students_db';
$user = 'root';
$password = '';

dsn="mysql:host=dsn = "mysql:host=dsn="mysql:host=host;dbname=$dbName;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    pdo=newPDO(pdo = new PDO(pdo=newPDO(dsn, user,user,user,password, $options);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
