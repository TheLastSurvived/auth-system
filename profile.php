<?php
session_start();
require_once __DIR__ . '/includes/auth_functions.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$user = getUserData($userId);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if (empty($name)) $errors[] = 'Имя обязательно';
    if (empty($email)) $errors[] = 'Email обязателен';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный формат email';
    if (empty($phone)) $errors[] = 'Телефон обязателен';
    
    // Проверка текущего пароля при изменении пароля
    if (!empty($new_password)) {
        if (empty($password)) {
            $errors[] = 'Для изменения пароля введите текущий пароль';
        } elseif (!password_verify($password, $user['password'])) {
            $errors[] = 'Неверный текущий пароль';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Новые пароли не совпадают';
        }
    }
    
    if (empty($errors)) {
        $updatePassword = !empty($new_password) ? $new_password : null;
        $result = updateProfile($userId, $name, $email, $phone, $updatePassword);
        
        if ($result['success']) {
            $success = $result['message'];
            $user = getUserData($userId); 
        } else {
            $errors[] = $result['message'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2>Профиль пользователя</h2>
    
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
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="tel" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Текущий пароль (только для изменения пароля)</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="new_password">Новый пароль</label>
            <input type="password" name="new_password" id="new_password" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Повторите новый пароль</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>
    
    <p><a href="logout.php">Выйти</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>