// ===== Mode Jour/Nuit =====
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    // Vérifier si un thème est déjà enregistré dans le localStorage
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme) {
        body.setAttribute('data-theme', savedTheme);
    } else {
        // Par défaut, utiliser le mode jour
        body.setAttribute('data-theme', 'light');
    }

    // Gérer le clic sur le bouton de changement de thème
    themeToggle.addEventListener('click', function() {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
});
