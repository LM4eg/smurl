<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMURL - Сократить ссылку</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'counter.php'; ?>
    <link rel="stylesheet" href="assets/css/style.css?v=10" />
</head>
<body>
    <?php include 'header.php'; ?>
<div class="site-container">
    <div class="content container mt-5">
    <h1>Контакты (пример внутрянки)</h1>
    <p>Если у вас есть вопросы или предложения, свяжитесь с нами:</p>
    <ul>
        <li>Email: <a href="mailto:mail@example.ru">mail@example.ru</a></li>
    </ul>
</div>

<?php include 'footer.php'; ?>
</div>
</body>
</html>