<?php
session_start();

require_once __DIR__ . '/../../includes/config.php';

// Constantes de rôles
define('ROLE_SUPER_ADMIN', 'SUPER_ADMIN');
define('ROLE_ADMIN', 'ADMIN');

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']) && isset($_SESSION['admin_role']);
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
 * Vérifie si l'utilisateur a un rôle spécifique
 */
function hasRole($role) {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === $role;
}

/**
 * Vérifie si l'utilisateur est super admin
 */
function isSuperAdmin() {
    return hasRole(ROLE_SUPER_ADMIN);
}

/**
 * Exige le rôle super admin, redirige sinon
 */
function requireSuperAdmin() {
    requireAuth();
    if (!isSuperAdmin()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Tente de connecter un utilisateur
 */
function login($username, $password) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        
        // Mettre à jour last_login
        $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$admin['id']]);
        
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
    $stmt = $pdo->prepare("SELECT id, username, role, last_login, note, created_at FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupère tous les admins
 */
function getAllAdmins() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT id, username, role, last_login, created_at FROM admins ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Met à jour un admin
 */
function updateAdmin($id, $username, $role, $note, $newPassword = null) {
    $pdo = getDatabase();
    
    if ($newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, role = ?, note = ?, password = ? WHERE id = ?");
        return $stmt->execute([$username, $role, $note, $hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, role = ?, note = ? WHERE id = ?");
        return $stmt->execute([$username, $role, $note, $id]);
    }
}

/**
 * Supprime un admin
 */
function deleteAdmin($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
    return $stmt->execute([$id]);
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
function createAdmin($username, $password, $role = ROLE_SUPER_ADMIN) {
    $pdo = getDatabase();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $hash, $role]);
}

/**
 * Vérifie si un admin existe déjà
 */
function adminExists() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    return $stmt->fetchColumn() > 0;
}
