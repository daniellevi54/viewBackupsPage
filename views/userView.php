<?php
/** @var array $laundrys */
/** @var array $backups */
/** @var string $currentPath */
/** @var string $parentPath */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualização de Backups - TOPTI</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>TOPTI</h1>
        <h2>Backups <?= strtoupper($_SESSION['login']) ?></h2>
        <hr>
    </header>
    <main>
        <!--Botão de logout-->
        <a href="/logout">
            <button style="margin-bottom: 20px;">Logout</button>
        </a>
        <br>
        <?php if (isset($_SESSION['id']) && $currentPath !== ''): ?>
            <a href="?path=<?= urlencode($parentPath) ?>">⬆️📁</a>
            <br><br>
        <?php endif; ?>
        <?php foreach($backups['folders'] as $folder): ?>
            <a href="?path=<?= urlencode($folder['Prefix']) ?>">
                📁 <?= htmlspecialchars($folder['name']) ?>
            </a>
            <br>
        <?php endforeach ?>
        <?php foreach($backups['files'] as $file): ?>
            <a href="/download?Key=<?= urlencode($file['Key']) ?>">
                📄<?= htmlspecialchars($file['name']) ?>
            </a>
            <br>
        <?php endforeach ?>
    </main>
</body>
</html>