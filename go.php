<?php
include 'db.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $stmt = $pdo->prepare("SELECT * FROM shortened_urls WHERE short_code = ?");
    $stmt->execute([$code]);
    $url = $stmt->fetch();

    if ($url) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $stmt = $pdo->prepare("INSERT INTO url_clicks (shortened_url_id, ip_address) VALUES (?, ?)");
        $stmt->execute([$url['id'], $ipAddress]);

        header("Location: " . $url['original_url']);
        exit;
    } else {
        echo "Ссылка не найдена.";
    }
} else {
    echo "Отсутствует параметр code.";
}