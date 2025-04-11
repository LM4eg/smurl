<?php
session_start();
include 'db.php'; // Подключение к БД

// Логика генерации и сохранения сокращенной ссылки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_url'])) {
    $originalUrl = $_POST['original_url'];

    // Проверяем, является ли URL валидным
    if (filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        // Генерируем уникальный короткий код
        $shortCode = generateShortCode();

        // Определяем ID пользователя (если зарегистрирован)
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Сохраняем оригинальную ссылку и её короткий код в базу данных
        $stmt = $pdo->prepare("INSERT INTO shortened_urls (user_id, original_url, short_code) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $originalUrl, $shortCode]);

        // Сохраняем сгенерированный короткий код в сессии для вывода на странице
        $_SESSION['shortened_url'] = $shortCode;
    } else {
        echo "Неверный формат URL";
    }
}

// Функция для генерации уникального короткого кода
function generateShortCode() {
    do {
        $code = substr(md5(uniqid()), 0, 6);
        global $pdo; // Используем глобальную переменную PDO для проверки уникальности кода
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM shortened_urls WHERE short_code = ?");
        $stmt->execute([$code]);
        $exists = $stmt->fetchColumn();
    } while ($exists); // Повторяем, пока не найдём уникальный код

    return $code;
}
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
        <h1>Сократить ссылку</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="original_url" class="form-label">Адрес страницы:</label>
                <input type="text" class="form-control" id="original_url" name="original_url" required>
            </div>
            <button type="submit" class="btn btn-primary">Сократить</button>
        </form>

        <!-- Вывод сокращенной ссылки -->
        <?php if (isset($_SESSION['shortened_url'])): ?>
            <div class="mt-3">
                <p>Ваша сокращенная ссылка:</p>
                <input type="text" id="shortened_url" value="https://smurl.ru/<?php echo $_SESSION['shortened_url']; ?>" readonly class="form-control">

                <!-- Кнопки "Скопировать" и "Сгенерировать QR-код" -->
                <div class="mt-2 d-flex gap-2">
                    <button onclick="copyToClipboard()" class="btn btn-success">Скопировать</button>
                    <button onclick="generateQRCode()" class="btn btn-info">Сгенерировать QR-код</button>
                </div>

                <!-- Отображение QR-кода -->
                <div id="qr-code-container" class="mt-3" style="display:none;">
                    <img id="qr-code-image" src="" alt="QR Code" class="img-fluid">
                </div>
            </div>
            <!-- Очищаем сессию после вывода ссылки -->
            <?php unset($_SESSION['shortened_url']); ?>
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

        function generateQRCode() {
            const shortenedUrl = document.getElementById("shortened_url").value;
            const qrCodeContainer = document.getElementById("qr-code-container");
            const qrCodeImage = document.getElementById("qr-code-image");

            // Скрываем контейнер QR-кода перед генерацией
            qrCodeContainer.style.display = "none";

            // Генерация QR-кода через API
            qrCodeImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(shortenedUrl)}`;

            // Показываем контейнер QR-кода после загрузки изображения
            qrCodeImage.onload = function () {
                qrCodeContainer.style.display = "block";
            };
        }
    </script>
    <?php include 'footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>