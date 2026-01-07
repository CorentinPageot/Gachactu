<!-- Header -->
<header class="header">
    <div class="header-container">
        <div class="header-left">
            <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                <svg class="sun-icon" id="sunIcon" viewBox="0 0 24 24" width="24" height="24">
                    <circle cx="12" cy="12" r="5" fill="currentColor"/>
                    <line x1="12" y1="1" x2="12" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="12" y1="20" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="4.22" y1="4.22" x2="6.34" y2="6.34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="17.66" y1="17.66" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="1" y1="12" x2="4" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="20" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="4.22" y1="19.78" x2="6.34" y2="17.66" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="17.66" y1="6.34" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <i class="fas fa-moon" id="moonIcon"></i>
            </button>
        </div>
        <div class="header-logo">
            <a href="index" class="logo">
                <img src="images/logo_gachactu.png" alt="Logo Gach'Actu">
            </a>
            <h2 class="section-title">GACH'ACTU</h2>
        </div>
        <nav class="nav-links">
            <a href="index" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : ''; ?>" aria-label="Accueil">
                <i class="fas fa-home"></i>
            </a>
            <a href="jeux" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'jeux.php' ? ' active' : ''; ?>">Jeux à venir</a>
            <a href="actualites" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'actualites.php' ? ' active' : ''; ?>">Actualités</a>
        </nav>
        <div class="header-right">
            <a href="https://discord.gg/4YU5MAawqZ" target="_blank" class="btn btn-discord">
                <i class="fab fa-discord"></i>
                <span>Discord</span>
            </a>
            <a href="https://ko-fi.com/limulutv" target="_blank" class="btn btn-don">
                <i class="fas fa-heart"></i>
                <span>Dons</span>
            </a>
        </div>
    </div>
</header>
