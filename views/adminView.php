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
    <title>Página de Admin - TOPTI</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>TOPTI</h1>
        <h2>Backups Admin</h2>
        <hr>
    </header>

<!--Listagem das lavanderias-->
    <main>
        <form method="POST" action="/admin">
            <label for="lavanderia">
                Lavanderia:
            </label>
            <select id="lavanderia" name="laundry">
                <option value="">
                    Selecione...
                </option>
                <?php foreach ($laundrys as $name => $id): ?>
                    <option value="<?= $id ?>">
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">
                Visualizar Backups
            </button>
        </form>
        <br>
        <a href="/admin/register">
            <button>Cadastrar Lavanderia</button>
        </a>
        <br>
        <br>
        <!--Botão de logout-->
        <a href="/logout">
            <button>Logout</button>
        </a>
        <hr>
    <?php
        if(isset($_SESSION['laundry'])){
            $laundryName = array_search($_SESSION['laundry'], $laundrys);
            if (!empty($backups) && $laundryName !== false) {
                echo "<h2>Backups " . strtoupper($laundryName) . "</h2><br>";
            }
        }
    ?>

    <?php if (isset($_SESSION['laundry']) && $currentPath !== ''): ?>
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
        <a href="/admin/download?Key=<?= urlencode($file['Key']) ?>">
            📄<?= htmlspecialchars($file['name']) ?>
        </a>
        <br>
    <?php endforeach ?>
    </main>
</body>
</html>