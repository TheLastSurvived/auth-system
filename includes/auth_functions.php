<?php
require_once __DIR__ . '/../config/database.php';

function registerUser($name, $email, $phone, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email или телефон уже зарегистрированы'];
    }
    
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $hashedPassword]);
    
    return ['success' => true, 'message' => 'Регистрация прошла успешно'];
}

function loginUser($login, $password) {
    global $pdo;
    
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Неверные учетные данные'];
    }
    
    // Успешная аутентификация
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    
    return ['success' => true, 'message' => 'Вход выполнен успешно'];
}

function updateProfile($userId, $name, $email, $phone, $password = null) {
    global $pdo;
    
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR phone = ?) AND id != ?");
    $stmt->execute([$email, $phone, $userId]);
    
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email или телефон уже используются другим пользователем'];
    }
    
    
    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $userId]);
    }
    
    
    $_SESSION['user_name'] = $name;
    
    return ['success' => true, 'message' => 'Профиль успешно обновлен'];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserData($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}
?>