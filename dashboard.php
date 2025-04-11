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
    <title>Личный кабинет - SMURL: сервис сокращения ссылок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'counter.php'; ?>
    <link rel="stylesheet" href="assets/css/style.css?v=10" />
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="site-container">
    <div class="content container mt-5">
        <h1>Личный кабинет</h1>
        <a href="logout.php" class="btn btn-danger mb-3">Выход</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Оригинальная ссылка</th>
                    <th>Сокращенная ссылка</th>
                    <th>QR код</th>
                    <th>Количество переходов</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($urls as $url): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($url['original_url']); ?></td>
                        <td><a href="https://smurl.ru/<?php echo $url['short_code']; ?>"><?php echo $url['short_code']; ?></a></td>
                        <td>
                            <!-- Кнопка "Показать QR код" -->
                            <button type="button" class="btn btn-info show-qr-btn" data-short-code="<?php echo $url['short_code']; ?>" data-bs-toggle="modal" data-bs-target="#qrModal">Показать QR код</button>
                        </td>
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
    <!-- Модальное окно для отображения QR-кода -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">QR-код</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center qr-modal">
                    <!-- Здесь будет отображаться QR-код -->
                    <img id="qrCodeImage" src="" alt="QR Code">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Функция для генерации QR-кода
        function generateQRCode(shortCode) {
            const qrCodeImage = document.getElementById("qrCodeImage");
            const shortenedUrl = `https://smurl.ru/${shortCode}`;
            qrCodeImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(shortenedUrl)}`;
        }

        // Обработка кликов по кнопке "Показать QR код"
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.show-qr-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const shortCode = this.getAttribute('data-short-code');
                    generateQRCode(shortCode);
                });
            });
        });
    </script>
</body>
</html>