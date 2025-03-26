<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM shortened_urls WHERE user_id = ?");
$stmt->execute([$user_id]);
$urls = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - SMURL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1>Личный кабинет</h1>
        <a href="logout.php" class="btn btn-danger mb-3">Выход</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Оригинальная ссылка</th>
                    <th>Сокращенная ссылка</th>
                    <th>Количество переходов</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($urls as $url): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($url['original_url']); ?></td>
                        <td><a href="http://smurl.ru/<?php echo $url['short_code']; ?>"><?php echo $url['short_code']; ?></a></td>
                        <td><?php 
                            $clickStmt = $pdo->prepare("SELECT COUNT(*) AS count FROM url_clicks WHERE shortened_url_id = ?");
                            $clickStmt->execute([$url['id']]);
                            echo $clickStmt->fetchColumn();
                        ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>