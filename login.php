<?php
session_start();
require_once __DIR__ . '/includes/auth_functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_captcha = $_POST['captcha'] ?? '';

    // Проверка капчи
    if (empty($_SESSION['captcha']) || strtolower($user_captcha) !== strtolower($_SESSION['captcha'])) {
        $errors[] = 'Неверная капча!';
    }

    // Проверка полей формы
    if (empty($login)) {
        $errors[] = 'Email или телефон обязателен';
    }
    if (empty($password)) {
        $errors[] = 'Пароль обязателен';
    }

    // Если ошибок нет - пробуем авторизовать
    if (empty($errors)) {
        $result = loginUser($login, $password);
        if ($result['success']) {
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2>Авторизация</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="login">Email или телефон</label>
            <input type="text" name="login" id="login" class="form-control" required
                   value="<?= htmlspecialchars($login ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="captcha">Введите капчу:</label>
            <input type="text" name="captcha" id="captcha" class="form-control" required>
            <img src="config/captcha.php?<?= time() ?>" alt="CAPTCHA" class="captcha" id="captcha"
                 
                 onclick="this.src='config/captcha.php?'+Math.random()">
            <small class="text-muted">Нажмите на изображение, чтобы обновить капчу</small>
        </div>

        <button type="submit" class="btn btn-primary">Войти</button>
    </form>

    <p class="mt-3">Ещё не зарегистрированы? <a href="register.php">Зарегистрируйтесь</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>