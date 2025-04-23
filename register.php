<?php
session_start();
require_once __DIR__ . '/includes/auth_functions.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if (empty($name)) $errors[] = 'Имя обязательно';
    if (empty($email)) $errors[] = 'Email обязателен';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный формат email';
    if (empty($phone)) $errors[] = 'Телефон обязателен';
    if (empty($password)) $errors[] = 'Пароль обязателен';
    if ($password !== $confirm_password) $errors[] = 'Пароли не совпадают';
    
    if (empty($errors)) {
        $result = registerUser($name, $email, $phone, $password);
        if ($result['success']) {
            $success = $result['message'];
            header("Refresh: 2; url=login.php");
        } else {
            $errors[] = $result['message'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2>Регистрация</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="tel" name="phone" id="phone" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Повторите пароль</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
    
    <p>Уже зарегистрированы? <a href="login.php">Войдите</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>