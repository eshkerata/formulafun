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
    if (isset($_POST['toggle_complete'])) {
        $task_id = $_POST['task_id'];
        $completed = $_POST['completed'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->execute([$completed, $task_id]);
        header('Location: index.php');
        exit();
    }
    if (isset($_POST['new_column'])) {
        $title = $_POST['column_title'];
        $stmt = $pdo->prepare("INSERT INTO columns (user_id, title) VALUES (?, ?)");
        $stmt->execute([$user_id, $title]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['new_task'])) {
        $column_id = $_POST['column_id'];
        $task_title = $_POST['task_title'];
        $stmt = $pdo->prepare("INSERT INTO tasks (column_id, title) VALUES (?, ?)");
        $stmt->execute([$column_id, $task_title]);
        header('Location: index.php');
        exit();
    }

    // Handle delete task with confirmation
    if (isset($_POST['delete_task_confirm'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        header('Location: index.php');
        exit();
    }

    // Handle delete column with confirmation
    if (isset($_POST['delete_column_confirm'])) {
        $column_id = $_POST['column_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE column_id = ?");
        $stmt->execute([$column_id]);
        $stmt = $pdo->prepare("DELETE FROM columns WHERE id = ?");
        $stmt->execute([$column_id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['edit_task'])) {
        $task_id = $_POST['task_id'];
        $new_title = $_POST['task_title'];
        $stmt = $pdo->prepare("UPDATE tasks SET title = ? WHERE id = ?");
        $stmt->execute([$new_title, $task_id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['edit_column'])) {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula</title>
    <link rel="stylesheet" href="/css/index-style.css">
</head>
<body>
    <div class="user-panel">
        <h1 class="logo">Formula</h1>
        <p>Вход выполнен как <?php echo htmlspecialchars($email); ?>.</p>
        <form method="POST" action="">
            <input type="text" name="column_title" placeholder="Новый столбец" required>
            <button type="submit" name="new_column">Создать новый столбец</button>
        </form>
        <a href="logout.php"><button>Выйти</button></a>
        <p class="dev-info">
            Билд 14082024, команда "Эщкерята"<br>
            <a href="https://t.me/eshkerata_team">тгк</a> | 
            <a href="https://github.com/eshkerata/formulafun">ГитХаб</a>
        </p>
    </div>

    <div class="board">
        <?php while ($column = $columns->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="column">
                <div class="column-control">
                    <form method="POST" action="">
                        <input type="text" name="column_title" value="<?php echo htmlspecialchars($column['title']); ?>" onblur="this.form.submit()">
                        <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                        <button type="submit" name="edit_column">Сохранить</button>
                    </form>
                    <form method="POST" action="">
                        <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                        <input type="hidden" name="column_title" value="<?php echo htmlspecialchars($column['title']); ?>">
                        <button type="submit" class="delete-btn" name="delete_column_confirm">Удалить столбец</button>
                    </form>
                </div>

                <?php
                $tasks = $pdo->prepare("SELECT * FROM tasks WHERE column_id = ?");
                $tasks->execute([$column['id']]);
                while ($task = $tasks->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <div class="task <?php echo $task['completed'] ? 'completed' : ''; ?>">
                        <form method="POST" action="">
                            <input type="text" name="task_title" value="<?php echo htmlspecialchars($task['title']); ?>" onblur="this.form.submit()">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" name="edit_task">Сохранить</button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="completed" value="<?php echo $task['completed']; ?>">
                            <button type="submit" class="toggle_complete" name="toggle_complete"><?php echo $task['completed'] ? '❌' : '✔️'; ?></button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="task_title" value="<?php echo htmlspecialchars($task['title']); ?>">
                            <button type="submit" class="delete-btn" name="delete_task_confirm" style="font-size: 1.2em;">🗑</button>
                        </form>
                    </div>
                <?php endwhile; ?>

                <form method="POST" action="">
                    <input type="text" name="task_title" placeholder="Добавить задачу" required>
                    <input type="hidden" name="column_id" value="<?php echo $column['id']; ?>">
                    <button type="submit" name="new_task" style="font-size: 1.2em;">+</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
