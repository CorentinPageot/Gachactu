<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireSuperAdmin(); // Seul le super admin peut gérer les utilisateurs

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$utilisateur = $id ? getAdminById($id) : null;
$pageTitle = $utilisateur ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur';
$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? ROLE_ADMIN);
    $note = trim($_POST['note'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    // Validation
    if (empty($username)) {
        $message = 'Le nom d\'utilisateur est requis.';
        $messageType = 'error';
    } elseif (!in_array($role, [ROLE_ADMIN, ROLE_SUPER_ADMIN])) {
        $message = 'Rôle invalide.';
        $messageType = 'error';
    } elseif (!$id && empty($password)) {
        $message = 'Le mot de passe est requis pour un nouvel utilisateur.';
        $messageType = 'error';
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $message = 'Les mots de passe ne correspondent pas.';
        $messageType = 'error';
    } elseif (!empty($password) && strlen($password) < 6) {
        $message = 'Le mot de passe doit contenir au moins 6 caractères.';
        $messageType = 'error';
    } else {
        // Vérifier l'unicité du username
        $pdo = getDatabase();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id ?: 0]);
        
        if ($stmt->fetchColumn() > 0) {
            $message = 'Ce nom d\'utilisateur est déjà utilisé.';
            $messageType = 'error';
        } else {
            if ($id) {
                // Modification
                if (updateAdmin($id, $username, $role, $note, $password ?: null)) {
                    $message = 'Utilisateur modifié avec succès.';
                    $messageType = 'success';
                    $utilisateur = getAdminById($id); // Recharger les données
                } else {
                    $message = 'Erreur lors de la modification.';
                    $messageType = 'error';
                }
            } else {
                // Création
                if (createAdmin($username, $password, $role)) {
                    $message = 'Utilisateur créé avec succès.';
                    $messageType = 'success';
                    // Rediriger vers la liste
                    header('Location: utilisateurs.php');
                    exit;
                } else {
                    $message = 'Erreur lors de la création.';
                    $messageType = 'error';
                }
            }
        }
    }
}

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-header">
    <h1>
        <i class="fas fa-<?= $id ? 'edit' : 'plus' ?>"></i>
        <?= $pageTitle ?>
    </h1>
    <a href="utilisateurs.php" class="admin-button admin-button-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <div class="admin-form-group">
            <label for="username">
                Nom d'utilisateur <span class="admin-required">*</span>
            </label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                class="admin-input"
                value="<?= htmlspecialchars($utilisateur['username'] ?? '') ?>"
                required
            >
        </div>

        <div class="admin-form-group">
            <label for="role">
                Rôle <span class="admin-required">*</span>
            </label>
            <select id="role" name="role" class="admin-input" required>
                <option value="<?= ROLE_ADMIN ?>" <?= (!$utilisateur || $utilisateur['role'] === ROLE_ADMIN) ? 'selected' : '' ?>>
                    Admin
                </option>
                <option value="<?= ROLE_SUPER_ADMIN ?>" <?= ($utilisateur && $utilisateur['role'] === ROLE_SUPER_ADMIN) ? 'selected' : '' ?>>
                    Super Admin
                </option>
            </select>
            <small class="admin-help-text">
                <strong>Admin :</strong> Accès à toutes les fonctionnalités sauf la gestion des utilisateurs.<br>
                <strong>Super Admin :</strong> Accès complet incluant la création et modification des comptes.
            </small>
        </div>

        <div class="admin-form-group">
            <label for="password">
                <?= $id ? 'Nouveau mot de passe' : 'Mot de passe' ?>
                <?php if (!$id): ?><span class="admin-required">*</span><?php endif; ?>
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="admin-input"
                minlength="6"
                <?= !$id ? 'required' : '' ?>
            >
            <?php if ($id): ?>
                <small class="admin-help-text">Laissez vide pour ne pas modifier le mot de passe</small>
            <?php endif; ?>
        </div>

        <div class="admin-form-group">
            <label for="confirm_password">
                Confirmer le mot de passe
                <?php if (!$id): ?><span class="admin-required">*</span><?php endif; ?>
            </label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="admin-input"
                minlength="6"
                <?= !$id ? 'required' : '' ?>
            >
        </div>

        <div class="admin-form-group">
            <label for="note">Note (visible uniquement par les Super Admins)</label>
            <textarea 
                id="note" 
                name="note" 
                class="admin-input"
                rows="5"
                placeholder="Informations ou remarques sur cet utilisateur..."
            ><?= htmlspecialchars($utilisateur['note'] ?? '') ?></textarea>
            <small class="admin-help-text">
                Utilisez ce champ pour conserver des informations sur cet utilisateur (responsabilités, accès spécifiques, etc.)
            </small>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-success">
                <i class="fas fa-save"></i>
                <?= $id ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' ?>
            </button>
            <a href="utilisateurs.php" class="admin-button admin-button-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>

<?php if ($utilisateur && $utilisateur['last_login']): ?>
<div class="admin-card">
    <h3><i class="fas fa-info-circle"></i> Informations</h3>
    <div class="admin-info-grid">
        <div class="admin-info-item">
            <strong>Dernière connexion :</strong>
            <?= date('d/m/Y à H:i', strtotime($utilisateur['last_login'])) ?>
        </div>
        <div class="admin-info-item">
            <strong>Créé le :</strong>
            <?= date('d/m/Y à H:i', strtotime($utilisateur['created_at'])) ?>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.admin-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.admin-info-item {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.admin-info-item strong {
    display: block;
    margin-bottom: 0.25rem;
    color: #495057;
    font-size: 0.875rem;
}
</style>

<?php include 'includes/admin-footer.php'; ?>
