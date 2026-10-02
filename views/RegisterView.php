<?php
/** @var array $laundrys */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Novas Lavanderias</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>TOPTI</h1>
        <h2>Cadastrar Nova Lavanderia</h2>
        <hr>
    </header>
    <main>
        <form method="post" action="/admin/newRegister">
            <label>Nome da Lavanderia:</label>
            <input type="text" name="descriptive" required>
            <label>ID:</label>
            <input type="number" min="1" max="99" name="id" required>
            <br><br>
            <label>Senha:</label>
            <input type="password" placeholder="Digite a senha:" name="password" required>
            <br><br>
            <input type="submit" name="register" value="cadastrar">
        </form>
        <hr>
        <h3>Lavanderias Cadastradas:</h3>
        <table border="1">
            <tr>
                <th>Lavanderia</th>
                <th>ID</th>
            </tr>
            <?php foreach ($laundrys as $lvd_descritivo => $lvd_codigo): ?>
            <tr>
                <td><?= htmlspecialchars($lvd_descritivo) ?></td>
                <td><?= sprintf('%03d', (int)$lvd_codigo) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>