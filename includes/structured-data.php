<!-- Données structurées Schema.org pour le SEO et GA4 -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Gach'Actu",
  "url": "https://gachactu.com",
  "description": "Actualités, guides et tier lists pour vos jeux gacha préférés",
  "publisher": {
    "@type": "Organization",
    "name": "Gach'Actu",
    "logo": {
      "@type": "ImageObject",
      "url": "https://gachactu.com/images/logo_gachactu.png"
    }
  },
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://gachactu.com/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  },
  "sameAs": [
    "https://discord.gg/4YU5MAawqZ",
    "https://x.com/GachActu",
    "https://www.youtube.com/@limulutv",
    "https://www.twitch.tv/limulutv",
    "https://www.tiktok.com/@limugacha"
  ]
}
</script>

<?php if (isset($structuredData) && $structuredData === 'article' && isset($actualite)): ?>
<!-- Données structurées pour Article -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "<?= htmlspecialchars($actualite['titre']) ?>",
  "description": "<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : '' ?>",
  "image": "<?= isset($ogImage) ? $ogImage : 'https://gachactu.com/images/logo_gachactu.png' ?>",
  "datePublished": "<?= isset($actualite['date_publication']) ? date('c', strtotime($actualite['date_publication'])) : '' ?>",
  "dateModified": "<?= isset($actualite['date_modification']) ? date('c', strtotime($actualite['date_modification'])) : (isset($actualite['date_publication']) ? date('c', strtotime($actualite['date_publication'])) : '') ?>",
  "author": {
    "@type": "Person",
    "name": "Gach'Actu"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Gach'Actu",
    "logo": {
      "@type": "ImageObject",
      "url": "https://gachactu.com/images/logo_gachactu.png"
    }
  }
}
</script>
<?php endif; ?>

<?php if (isset($structuredData) && $structuredData === 'game' && isset($jeu)): ?>
<!-- Données structurées pour Jeu -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "VideoGame",
  "name": "<?= htmlspecialchars($jeu['titre']) ?>",
  "description": "<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : '' ?>",
  "image": "<?= isset($ogImage) ? $ogImage : 'https://gachactu.com/images/logo_gachactu.png' ?>",
  "genre": "Gacha Game",
  "applicationCategory": "Game"
}
</script>
<?php endif; ?>
