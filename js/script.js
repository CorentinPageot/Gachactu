// ===== Mode Jour/Nuit + Voir plus =====
document.addEventListener('DOMContentLoaded', function() {

    /* ===============================
       MODE JOUR / NUIT
    =============================== */
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    if (themeToggle) {
        // Vérifier si un thème est déjà enregistré
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme) {
            body.setAttribute('data-theme', savedTheme);
        } else {
            body.setAttribute('data-theme', 'dark');
        }

        // Changement de thème
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    /* ===============================
       BOUTON "VOIR PLUS"
    =============================== */
    document.querySelectorAll('.btn-see-more').forEach(button => {
        button.addEventListener('click', (event) => {
            // Empêche le clic de déclencher le lien de la card
            event.preventDefault();
            event.stopPropagation();

            const wrapper = button.closest('.game-description-wrapper');
            if (!wrapper) return;

            wrapper.classList.toggle('is-open');

            button.textContent = wrapper.classList.contains('is-open')
                ? 'Voir moins'
                : 'Voir plus';
        });
    });

});