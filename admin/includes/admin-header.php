<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Admin Gach'Actu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../images/logo_gachactu.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script src="js/langs/fr_FR.js"></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header header-logo">
                <a href="index.php" class="admin-logo">
                    <img src="../images/logo_gachactu.png" alt="Logo">
                    <h2>GACHACTU</h2>
                </a>
            </div>
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="actualites.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'actualites.php' || basename($_SERVER['PHP_SELF']) == 'actualite-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i> Actualités
                </a>
                <a href="jeux.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'jeux.php' || basename($_SERVER['PHP_SELF']) == 'jeu-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-gamepad"></i> Jeux
                </a>
                <a href="personnages.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'personnages.php' || basename($_SERVER['PHP_SELF']) == 'personnage-edit.php' || basename($_SERVER['PHP_SELF']) == 'guide-personnage-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Personnages
                </a>
                <a href="rubriques-personnages.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'rubriques-personnages.php' ? 'active' : '' ?>">
                    <i class="fas fa-list-alt"></i> Rubriques perso
                </a>
                <a href="codes.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'codes.php' || basename($_SERVER['PHP_SELF']) == 'code-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-gift"></i> Codes cadeaux
                </a>
                <a href="guides.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'guides.php' || basename($_SERVER['PHP_SELF']) == 'guide-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-book"></i> Guides
                </a>
                <a href="partenaires.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'partenaires.php' || basename($_SERVER['PHP_SELF']) == 'partenaire-edit.php' ? 'active' : '' ?>">
                    <i class="fas fa-handshake"></i> Partenaires
                </a>
                <a href="categories.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Catégories
                </a>
                <a href="plateformes.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) == 'plateformes.php' ? 'active' : '' ?>">
                    <i class="fas fa-desktop"></i> Plateformes
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="../index.php" class="admin-nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Voir le site
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 class="admin-page-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Administration' ?></h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                    <a href="logout.php" class="admin-logout-btn" title="Déconnexion">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>
            <div class="admin-content">
