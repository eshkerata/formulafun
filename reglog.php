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
        $_SESSION['email'] = $email;
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
    <link rel="stylesheet" href="/src/css/index-style.css?v=3">

    <style>
        .subheader {
            padding-bottom: 16px
        }
        body {
            min-width: 100vw;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        form {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 16px;
        }
        form button {
            border: 0;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            height: fit-content;
            background-color: #8231a0;
            color: #fff;
            padding: 15px;
            border-radius: 33px;
            margin: inherit
        }
    </style>

</head>
<body>
<h1 class="site-logo">Formula</h1>
<p class="subheader">Успеха</p>
<form id="reglog" method="POST" action="">
    <input type="email" name="email" placeholder="Почта" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Регистрация/Вход</button>
</form>

</body>
</html>