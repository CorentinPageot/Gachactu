<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

$error = '';
$showSetup = false;

// Vérifier si c'est la première installation (pas d'admin)
try {
    if (!adminExists()) {
        $showSetup = true;

        // Traitement du formulaire de création du premier admin
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Veuillez remplir tous les champs.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } else {
                if (createAdmin($username, $password)) {
                    login($username, $password);
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Erreur lors de la création du compte.';
                }
            }
        }
    }
} catch (Exception $e) {
    // La table n'existe probablement pas encore
    $showSetup = true;
    $error = 'Veuillez d\'abord exécuter le script SQL admin.sql pour créer la table des administrateurs.';
}

// Si déjà connecté, afficher le dashboard
if (isLoggedIn()) {
    $pageTitle = 'Dashboard admin';
    $stats = getStats();
    include 'includes/admin-header.php';
?>
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gamepad"></i></div>
            <div class="stat-info">
                <span class="stat-number"><?= $stats['jeux'] ?></span>
                <span class="stat-label">Jeux</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
            <div class="stat-info">
                <span class="stat-number"><?= $stats['actualites'] ?></span>
                <span class="stat-label">Actualités</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-number"><?= $stats['personnages'] ?></span>
                <span class="stat-label">Personnages</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gift"></i></div>
            <div class="stat-info">
                <span class="stat-number"><?= $stats['codes'] ?></span>
                <span class="stat-label">Codes actifs</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-handshake"></i></div>
            <div class="stat-info">
                <span class="stat-number"><?= $stats['partenaires'] ?></span>
                <span class="stat-label">Partenaires</span>
            </div>
        </div>
    </div>

    <div class="dashboard-actions">
        <h2>Actions rapides</h2>
        <div class="quick-actions">
            <a href="actualite-edit.php" class="quick-action-btn">
                <i class="fas fa-plus"></i> Nouvelle actualité
            </a>
            <a href="jeu-edit.php" class="quick-action-btn">
                <i class="fas fa-plus"></i> Nouveau jeu
            </a>
            <a href="personnage-edit.php" class="quick-action-btn">
                <i class="fas fa-plus"></i> Nouveau personnage
            </a>
            <a href="code-edit.php" class="quick-action-btn">
                <i class="fas fa-plus"></i> Nouveau code
            </a>
        </div>
    </div>
<?php
    include 'includes/admin-footer.php';
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $showSetup ? 'Installation' : 'Connexion' ?> - Admin Gach'Actu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../images/logo_gachactu.ico" type="image/x-icon">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-header">
                <h1><?= $showSetup ? 'Installation' : 'Administration' ?></h1>
            </div>

            <?php if ($error): ?>
            <div class="admin-alert admin-alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($showSetup): ?>
            <!-- Formulaire de création du premier admin -->
            <form method="POST" class="admin-login-form">
                <input type="hidden" name="setup" value="1">
                <p class="admin-setup-info">Créez votre compte administrateur pour commencer.</p>
                <div class="admin-form-group">
                    <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="admin-form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="admin-form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="admin-login-btn">
                    <i class="fas fa-user-plus"></i> Créer le compte
                </button>
            </form>
            <?php else: ?>
            <!-- Formulaire de connexion -->
            <form method="POST" class="admin-login-form">
                <input type="hidden" name="login" value="1">
                <div class="admin-form-group">
                    <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="admin-form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="admin-login-btn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            <?php endif; ?>

            <div class="admin-login-footer">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
