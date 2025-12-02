<?php
// Подключение к базе данных
$host = 'teybet-az.com';
$db = 'u2790813_default';
$user = 'u2790813_default';
$pass = 'Bt65Tm95USDafNbn';
$charset = 'utf8mb4';

// Формируем DSN для подключения к MySQL
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt = $pdo->query("SELECT name, url FROM mirrors ORDER BY id DESC");
    $mirrors = $stmt->fetchAll();

} catch (\PDOException $e) {
    echo 'Ошибка подключения к базе данных: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash - Redirect</title>
    <style>
        body {
            background-color: #1f1f2e;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .center-content {
            text-align: center;
            margin-top: 10%;
        }

        .logo img {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .content-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .content-subtitle {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .link-list {
            list-style: none;
            padding: 0;
            margin-bottom: 40px;
        }

        .link-list li {
            background-color: #2a2a3c;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .link-list li a {
            color: #00bfff;
            text-decoration: none;
        }

        .link-list li a:hover {
            text-decoration: underline;
        }

        .info-box {
            background-color: #2a2a3c;
            border-radius: 8px;
            padding: 20px;
            color: #cccccc;
        }
        .redirect-button {
          background-color: #00bfff;
          border: none;
          border-radius: 8px;
          padding: 15px 30px;
          color: #ffffff;
          font-size: 1rem;
          cursor: pointer;
          text-decoration: none;
          display: inline-block;
          margin-top: 20px;
      }

      .redirect-button:hover {
          background-color: #008fcc;
      }
    </style>
</head>
<body>
    <div class="center-content">
        <div class="logo">
            <img src="/assets/images/logo.png" alt="Flash Logo">
        </div>
        <h1 class="content-title">{{ __('Чтобы открыть игру, отключите VPN или перейдите на:') }}</h1>

        <?php if (!empty($mirrors)): ?>
            <a target="_blank" href="<?= htmlspecialchars($mirrors[0]['url']) ?>" class="redirect-button">{{ __('Перейти на') }} <?= htmlspecialchars($mirrors[0]['name']) ?></a>
            <ul class="link-list">
                <?php foreach ($mirrors as $mirror): ?>
                    <li>
                        <span><?= htmlspecialchars($mirror['name']) ?></span>
                        <a href="<?= htmlspecialchars($mirror['url']) ?>" target="_blank"><?= htmlspecialchars($mirror['url']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>{{ __('Зеркала не найдены.') }}</p>
        <?php endif; ?>

    </div>
</body>
</html>
