<?php
session_start();

require_once __DIR__ . '/../../includes/config.php';

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Exige une authentification, redirige vers login sinon
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Tente de connecter un utilisateur
 */
function login($username, $password) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }

    return false;
}

/**
 * Déconnecte l'utilisateur
 */
function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

/**
 * Récupère un admin par son ID
 */
function getAdminById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT id, username, created_at FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Change le mot de passe d'un admin
 */
function changePassword($adminId, $newPassword) {
    $pdo = getDatabase();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
    return $stmt->execute([$hash, $adminId]);
}

/**
 * Crée un compte admin (utilisé pour le setup initial)
 */
function createAdmin($username, $password) {
    $pdo = getDatabase();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    return $stmt->execute([$username, $hash]);
}

/**
 * Vérifie si un admin existe déjà
 */
function adminExists() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    return $stmt->fetchColumn() > 0;
}
