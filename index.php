<?php
session_start();
require_once __DIR__ . '/includes/auth_functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1>Добро пожаловать</h1>
    
    <?php if (isLoggedIn()): ?>
        <p>Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>! Вы вошли в систему.</p>
        <p><a href="profile.php" class="btn btn-primary">Перейти в профиль</a></p>
    <?php else: ?>
        <p>Пожалуйста, <a href="login.php">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>