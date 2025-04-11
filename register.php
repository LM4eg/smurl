<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        die("Пароли не совпадают!");
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);
        $_SESSION['registration_success'] = true; // Установка флага успешной регистрации
    } catch (\PDOException $e) {
        echo "Эта почта уже используется.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - SMURL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'counter.php'; ?>
    <link rel="stylesheet" href="assets/css/style.css?v=10" />
</head>
<body>
    <?php include 'header.php'; ?>
<div class="site-container">
    <div class="content container mt-5">
        <h1>Регистрация</h1>

        <!-- Модальное окно для успешной регистрации -->
        <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
            <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Успешная регистрация</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Регистрация успешно завершена! Теперь вы можете войти.
                        </div>
                        <div class="modal-footer">
                            <a href="login.php" class="btn btn-primary">Войти</a>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // Добавляем скрипт для закрытия модального окна
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();

                // Очищаем флаг успешной регистрации после закрытия модального окна
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
                    modal.hide(); // Скрываем модальное окно после загрузки страницы
                    <?php unset($_SESSION['registration_success']); ?> // Очистка флага
                });
            </script>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Подтвердите пароль:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>