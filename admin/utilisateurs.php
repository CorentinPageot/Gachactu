<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireSuperAdmin(); // Seul le super admin peut gérer les utilisateurs

$pageTitle = 'Gestion des Utilisateurs';
$message = '';
$messageType = '';

// Suppression d'un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    
    // Empêcher de supprimer son propre compte
    if ($userId === $_SESSION['admin_id']) {
        $message = 'Vous ne pouvez pas supprimer votre propre compte.';
        $messageType = 'error';
    } else {
        if (deleteAdmin($userId)) {
            $message = 'Utilisateur supprimé avec succès.';
            $messageType = 'success';
        } else {
            $message = 'Erreur lors de la suppression.';
            $messageType = 'error';
        }
    }
}

$utilisateurs = getAllAdmins();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2><i class="fas fa-users"></i> <?= $pageTitle ?> (<?= count($utilisateurs) ?>)</h2>
    <a href="utilisateur-edit.php" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouvel utilisateur
    </a>
</div>

<div class="admin-table-container">
    <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Rôle</th>
                    <th>Dernière connexion</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($utilisateurs)): ?>
                <tr>
                    <td colspan="5" class="admin-empty">Aucun utilisateur trouvé</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                            <?php if ($user['id'] === $_SESSION['admin_id']): ?>
                                <span class="admin-badge admin-badge-info">Vous</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['role'] === ROLE_SUPER_ADMIN): ?>
                                <span class="admin-badge admin-badge-warning">Super Admin</span>
                            <?php else: ?>
                                <span class="admin-badge admin-badge-info">Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['last_login']): ?>
                                <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                            <?php else: ?>
                                <span class="admin-text-muted">Jamais</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td class="admin-actions">
                            <a href="utilisateur-edit.php?id=<?= $user['id'] ?>" class="admin-icon-button" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                                <a href="?delete=<?= $user['id'] ?>" 
                                   class="admin-icon-button admin-icon-button-danger" 
                                   title="Supprimer"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
