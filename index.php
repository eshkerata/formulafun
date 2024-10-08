<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header('Location: reglog.php');
    exit();
}

require 'config.php';

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

$columns = $pdo->prepare("SELECT * FROM columns WHERE user_id = ?");
$columns->execute([$user_id]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['toggle_task_completion'])) {
        $task_id = $_POST['task_id'];
        $completed = $_POST['completed'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->execute([$completed, $task_id]);
        header('Location: index.php');
        exit();
    }
    if (isset($_POST['add_column'])) {
        $title = $_POST['column_title'];
        $stmt = $pdo->prepare("INSERT INTO columns (user_id, title) VALUES (?, ?)");
        $stmt->execute([$user_id, $title]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['add_task'])) {
        $column_id = $_POST['column_id'];
        $task_title = $_POST['task_title'];
        $stmt = $pdo->prepare("INSERT INTO tasks (column_id, title) VALUES (?, ?)");
        $stmt->execute([$column_id, $task_title]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['confirm_task_deletion'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['confirm_column_deletion'])) {
        $column_id = $_POST['column_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE column_id = ?");
        $stmt->execute([$column_id]);
        $stmt = $pdo->prepare("DELETE FROM columns WHERE id = ?");
        $stmt->execute([$column_id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['edit_task_title'])) {
        $task_id = $_POST['task_id'];
        $new_title = $_POST['task_title'];
        $stmt = $pdo->prepare("UPDATE tasks SET title = ? WHERE id = ?");
        $stmt->execute([$new_title, $task_id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['edit_column_title'])) {
        $column_id = $_POST['column_id'];
        $new_title = $_POST['column_title'];
        $stmt = $pdo->prepare("UPDATE columns SET title = ? WHERE id = ?");
        $stmt->execute([$new_title, $column_id]);
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula</title>
    <link rel="stylesheet" href="/src/css/index-style.css?v=4">

    <meta name="title" content="FormulaFun - Система управления задачами">
    <meta name="description" content="Простая система управления задачами для личного использования">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="Russian">
<!-- Добавим небольшой скрипт для автоматической отправки формы при потере фокуса -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const columnTitleInputs = document.querySelectorAll('.column_title input[type="text"]');
            const taskTitleInputs = document.querySelectorAll('.task-actions input[type="text"]');

            columnTitleInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    this.form.submit();
                });
            });

            taskTitleInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    this.form.submit();
                });
            });
        });

        // Якорь на форму после её отправки
        if (window.location.hash) {
            const element = document.querySelector(window.location.hash);
            if (element) {
                window.scrollTo({
                    top: element.offsetTop,
                    behavior: "smooth"
                });
            }
        }
    </script>
</head>
<body>
    <div class="user-panel">
        <div class="logo_info">
            <h1 class="site-logo">Formula</h1>
            <p>Вход в систему как <?php echo htmlspecialchars($email); ?>.</p>
        </div>
        <form method="POST" action="#column_form">
            <input type="text" class="new_column" name="column_title" placeholder="Новая тема" required>
            <input type="hidden" name="add_column">
            <input type="submit" style="display: none;" />
        </form>
        <a class="logout" href="logout.php"><button>Выйти <img src="/src/img/logout.svg"/></button></a>
        <p class="dev-info">
            Версия osp-200824 &copy; Эщкерята, 2024<br>
            <a href="https://t.me/FormulaFun_Project">Telegram</a> | <a href="https://github.com/eshkerata/formulafun/blob/main/LICENSE.MD">Лицензия</a>
        </p>
    </div>

    <div class="task-board">
        <?php while ($column = $columns->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="task-column" id="column_<?php echo $column['id']; ?>">
                <div class="column-header">
                    <form method="POST" id="column_form" class="column_title" action="#column_<?php echo $column['id']; ?>">
                        <input type="text" class="column_title" name="column_title" placeholder="Тема" value="<?php echo htmlspecialchars($column['title']); ?>">
                        <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                        <input type="hidden" name="edit_column_title">
                    </form>
                    <form method="POST" action="">
                        <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                        <button type="submit" class="delete-button" name="confirm_column_deletion"><img src="/src/img/delete_forever.svg"/></button>
                    </form>
                </div>

                <?php
                $tasks = $pdo->prepare("SELECT * FROM tasks WHERE column_id = ?");
                $tasks->execute([$column['id']]);
                while ($task = $tasks->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <div class="task-item <?php echo $task['completed'] ? 'task-completed' : ''; ?>" id="task_<?php echo $task['id']; ?>">
                        <div class="task-actions">
                            <form method="POST" action="" class="task-toggle-completion-form">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="completed" value="<?php echo $task['completed']; ?>">
                                <button type="submit" class="task-toggle-complete" name="toggle_task_completion"><?php echo $task['completed'] ? '<img src="/src/img/check_box.svg"/>' : '<img src="/src/img/check_box_outline_blank.svg"/>'; ?></button>
                            </form>
                            <form method="POST" action="#task_<?php echo $task['id']; ?>">
                                <input type="text" name="task_title" placeholder="Имя задачи" value="<?php echo htmlspecialchars($task['title']); ?>">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="edit_task_title">
                            </form>
                        </div>
                        
                        <form method="POST" action="" class="task-delete-form">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" class="delete-button" name="confirm_task_deletion"><img src="/src/img/delete_forever.svg"/></button>
                        </form>
                    </div>
                <?php endwhile; ?>

                <form method="POST" class="task_add_form" action="">
                    <input type="text" name="task_title" placeholder="Добавить задачу" required>
                    <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                    <button type="submit" name="add_task" style="font-size: 1.2em;"><img src="/src/img/add_circle.svg"/></button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
