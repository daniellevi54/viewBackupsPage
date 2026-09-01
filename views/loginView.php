<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>TOPTI</h1>
    <hr>
    <h2>Login - Acesso a Backups</h2>
    <form method="POST" action="/login">
        <label>Usuário</label>
        <input type="text" name="login" required>
        <br><br>
        <label>ID:</label>
        <input type="password" name="password" placeholder="Digite sua senha" required>
        <br><br> 
        <input type="submit" value="Log-in">
    </form>
</body>
</html>