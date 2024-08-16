<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header('Location: index.php');
        } else {
            echo "Invalid password";
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $password]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header('Location: index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula - Регистрация/Логин</title>
    <link rel="stylesheet" href="/css/reglog-style.css?v=3">

    <style>
        .site-logo {
            margin-bottom: 0;
        }
        .subheader {
            margin-top: 0;
        }
    </style>

</head>
<body>
<h1 class="site-logo">Formula</h1>
<p class="subheader">Успеха</p>
<form method="POST" action="">
    <input type="email" name="email" placeholder="Почта" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Регистрация/Вход</button>
</form>

</body>
</html>