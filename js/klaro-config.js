// Configuration Klaro pour Gach'Actu
// Documentation: https://kiprotect.com/docs/klaro

var klaroConfig = {
    // Version de la configuration (incrémentez pour forcer un nouveau consentement)
    version: 1,

    // Élément où Klaro va s'insérer (null = body)
    elementID: 'klaro',

    // Nom de votre site
    htmlTexts: true,
    
    // Clé de stockage dans localStorage
    storageName: 'klaroConsent',

    // Nom du cookie
    cookieName: 'klaroConsent',

    // Durée du cookie en jours
    cookieExpiresAfterDays: 365,

    // opt-out = les services sont activés par défaut
    default: true,

    // Langue par défaut
    lang: 'fr',

    // Position de la modal : 'top-left', 'top-right', 'bottom-left', 'bottom-right', 'center'
    // Pour changer la position, modifiez cette valeur
    noticeAsModal: true, // false = bandeau en bas/haut, true = modal au centre

    // Afficher directement la vue de personnalisation (pas le simple bandeau)
    embedded: false, // false = affiche la modal complète avec tous les détails
    mustConsent: true, // L'utilisateur doit faire un choix explicite

    // Traductions françaises
    translations: {
        zz: {
            privacyPolicyUrl: '/mentions-legales'
        },
        fr: {
            consentModal: {
                title: 'Cookies et vie privée',
                description: 'Nous utilisons des cookies pour améliorer votre expérience de navigation et analyser le trafic du site. Vous pouvez personnaliser vos préférences ci-dessous.',
            },
            consentNotice: {
                changeDescription: 'Des modifications ont été apportées depuis votre dernière visite, veuillez mettre à jour vos préférences.',
                description: 'Nous utilisons des cookies pour améliorer votre expérience. {purposes}.',
                learnMore: 'Personnaliser',
                testing: 'Mode test !',
            },
            googleAnalytics: {
                description: 'Google Analytics nous aide à comprendre comment les visiteurs utilisent notre site afin d\'améliorer leur expérience.'
            },
            purposes: {
                analytics: 'Mesure d\'audience',
                functional: 'Fonctionnalités essentielles'
            },
            ok: 'Tout accepter',
            save: 'Enregistrer',
            decline: 'Tout refuser',
            close: 'Fermer',
            acceptAll: 'Tout accepter',
            acceptSelected: 'Accepter la sélection',
            service: {
                disableAll: {
                    title: 'Activer/désactiver tous les services',
                    description: 'Utilisez ce bouton pour activer/désactiver tous les services.'
                },
                optOut: {
                    title: '(opt-out)',
                    description: 'Ce service est activé par défaut (mais vous pouvez le désactiver)'
                },
                required: {
                    title: '(toujours requis)',
                    description: 'Ce service est toujours requis'
                },
                purposes: 'Finalités',
                purpose: 'Finalité'
            },
            poweredBy: 'Propulsé par Klaro !'
        }
    },

    // Services à gérer
    services: [
        {
            // Google Analytics 4
            name: 'googleAnalytics',
            title: 'Google Analytics',
            purposes: ['analytics'],
            cookies: [
                // Cookies GA4
                [/^_ga/, '/', 'gachactu.com'],
                [/^_gid/, '/', 'gachactu.com'],
                [/^_gat/, '/', 'gachactu.com'],
            ],
            required: false,
            optOut: true, // Service activé par défaut (opt-out)
            default: true, // État par défaut
            onlyOnce: true, // Le code ne sera chargé qu'une seule fois

            // Fonction appelée quand l'utilisateur accepte
            callback: function(consent, service) {
                if (consent === true) {
                    // Activer GA4
                    if (typeof window.gtag === 'function') {
                        window.gtag('consent', 'update', {
                            'analytics_storage': 'granted'
                        });
                    }
                } else {
                    // Désactiver GA4
                    if (typeof window.gtag === 'function') {
                        window.gtag('consent', 'update', {
                            'analytics_storage': 'denied'
                        });
                    }
                }
            }
        }
    ]
};
