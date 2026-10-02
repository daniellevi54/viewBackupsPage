<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>TOPTI</h1>
        <h2>Login - Acesso a Backups</h2>
        <hr>
    </header>
    <main>
        <form method="POST" action="/login">
            <label>Usuário</label>
            <input type="text" name="login" required>
            <br><br>
            <label>Senha:</label>
            <input type="password" name="password" placeholder="Digite sua senha" required>
            <br><br>
            <input type="submit" value="Log-in">
        </form>
    </main>
</body>
</html>