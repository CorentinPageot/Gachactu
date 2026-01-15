<!-- Klaro Consent Manager - DÉBUT -->
<link rel="stylesheet" href="css/klaro.css">
<link rel="stylesheet" href="css/klaro-custom.css">

<!-- Configuration Klaro (doit être chargé AVANT klaro.js) -->
<script defer type="text/javascript" src="js/klaro-config.js"></script>
<script defer type="text/javascript" src="js/klaro.js"></script>

<!-- Google Analytics 4 avec Consent Mode v2 -->
<script>
    // Initialisation du Consent Mode de Google (avant le chargement de GA4)
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    
    // Configuration par défaut : refus (sera activé par Klaro si consenti)
    gtag('consent', 'default', {
        'analytics_storage': 'denied',
        'ad_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'wait_for_update': 500
    });
    
    // Informations de région pour le Consent Mode
    gtag('set', 'ads_data_redaction', true);
</script>

<!-- Google Analytics 4 - Script de base (ID: G-EMG1MRPRXV) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EMG1MRPRXV"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-EMG1MRPRXV', {
        'anonymize_ip': true, // Anonymisation IP (RGPD)
        'cookie_flags': 'SameSite=None;Secure' // Sécurisation des cookies
    });
</script>
<!-- Klaro Consent Manager - FIN -->
