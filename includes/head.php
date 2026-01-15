<?php $site_url = 'https://gachactu.com'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Title & Description -->
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Gach\'Actu' : 'Gach\'Actu' ?></title>
    <meta name="description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Gach\'Actu - Actualités, guides et tier lists pour vos jeux gacha préférés. Découvrez les meilleurs personnages, codes promo et stratégies.' ?>">
    <meta name="keywords" content="gacha, jeux gacha, tier list, guides, personnages, codes promo, actualités gaming">
    
    <!-- SEO & Canonical -->
    <link rel="canonical" href="<?= $site_url ?><?= isset($canonicalUrl) ? $canonicalUrl : $_SERVER['REQUEST_URI'] ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Gach'Actu">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= isset($ogType) ? $ogType : 'website' ?>">
    <meta property="og:site_name" content="Gach'Actu">
    <meta property="og:title" content="<?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Gach\'Actu' : 'Gach\'Actu' ?>">
    <meta property="og:description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Actualités, guides et tier lists pour vos jeux gacha préférés.' ?>">
    <meta property="og:url" content="<?= $site_url ?><?= isset($canonicalUrl) ? $canonicalUrl : $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:image" content="<?= isset($ogImage) ? $ogImage : $site_url . '/images/logo_gachactu.png' ?>">
    <meta property="og:locale" content="fr_FR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@GachActu">
    <meta name="twitter:title" content="<?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Gach\'Actu' : 'Gach\'Actu' ?>">
    <meta name="twitter:description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Actualités, guides et tier lists pour vos jeux gacha préférés.' ?>">
    <meta name="twitter:image" content="<?= isset($ogImage) ? $ogImage : $site_url . '/images/logo_gachactu.png' ?>">
    
    <!-- Favicon & Icons -->
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="images/logo_gachactu.png">
    
    <!-- Styles -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.google-analytics.com">
    
    <!-- Analytics & Tracking -->
    <?php include 'includes/analytics.php'; ?>
    
    <!-- Structured Data (Schema.org) -->
    <?php include 'includes/structured-data.php'; ?>
</head>
