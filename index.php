<?php
session_start();
include 'db.php'; // Подключение к БД

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_url'])) {
    $originalUrl = $_POST['original_url'];
    if (filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        $shortCode = generateShortCode(); // Генерация уникального кода
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $stmt = $pdo->prepare("INSERT INTO shortened_urls (user_id, original_url, short_code) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $originalUrl, $shortCode]);

        $_SESSION['shortened_url'] = $shortCode;
    } else {
        echo "Неверный формат URL";
    }
}

function generateShortCode() {
    $code = substr(md5(uniqid()), 0, 6); // Пример генерации короткого кода
    return $code;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сократить ссылку</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Сократить ссылку</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="original_url" class="form-label">Адрес страницы:</label>
                <input type="text" class="form-control" id="original_url" name="original_url" required>
            </div>
            <button type="submit" class="btn btn-primary">Сократить</button>
        </form>

        <?php if (isset($_SESSION['shortened_url'])): ?>
            <div class="mt-3">
                <p>Ваша сокращенная ссылка:</p>
                <input type="text" id="shortened_url" value="<?php echo "http://$_SERVER[HTTP_HOST]/go/" . $_SESSION['shortened_url']; ?>" readonly class="form-control">
                <button onclick="copyToClipboard()" class="btn btn-success mt-2">Скопировать</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyToClipboard() {
            const copyText = document.getElementById("shortened_url");
            copyText.select();
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("Ссылка скопирована!");
            });
        }
    </script>
</body>
</html>