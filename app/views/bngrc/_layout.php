<?php
/** Gabarit principal — BNGRC */
$title = $title ?? 'BNGRC';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars((string)$title) ?></title>

	<!-- Polices Google : Manrope (titres) + Space Mono (chiffres) -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

	<!-- Feuilles de style principales -->
	<link rel="stylesheet" href="/assets/layout.css">
	<link rel="stylesheet" href="/assets/style.css">

	<?php
	/* Chargement automatique du CSS propre à la page courante.
	   Exemple : /besoins → /assets/pages/besoins.css (si le fichier existe). */
	$root = dirname(__DIR__, 3);
	$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
	$name = trim($uri, '/');
	if ($name === '') {
		$name = 'index';
	}
	$name = preg_replace('/[^a-zA-Z0-9\-_\/]+/', '', $name);
	$name = str_replace('/', '-', $name);
	$pageCssFs = $root . '/public/assets/pages/' . $name . '.css';
	if (file_exists($pageCssFs)) {
		echo '<link rel="stylesheet" href="/assets/pages/' . htmlspecialchars($name) . '.css">';
	}
	?>
</head>
<body>
	<div class="app-wrapper">
		<header class="site-header">
			<div class="header-content">
				<div class="brand-section">
					<div class="brand-mark">
						<span class="brand-initial">B</span>
					</div>
					<div class="brand-info">
						<h1 class="brand-title">BNGRC</h1>
						<p class="brand-subtitle">Bureau National de Gestion des Risques et des Catastrophes</p>
					</div>
				</div>
				
				<nav class="main-nav">
					<a href="/bngrc/dashboard" class="nav-link" data-icon="📊">
						<span class="nav-label">Dashboard</span>
					</a>
					<a href="/besoins" class="nav-link" data-icon="📋">
						<span class="nav-label">Besoins</span>
					</a>
					<a href="/dons" class="nav-link" data-icon="🎁">
						<span class="nav-label">Dons</span>
					</a>
					<a href="/regions" class="nav-link" data-icon="🗺️">
						<span class="nav-label">Régions</span>
					</a>
					<a href="/villes" class="nav-link" data-icon="🏙️">
						<span class="nav-label">Villes</span>
					</a>
					<a href="/articles" class="nav-link" data-icon="📦">
						<span class="nav-label">Articles</span>
					</a>
				</nav>
			</div>
		</header>

		<main class="main-content">
			<?= $content ?? '' ?>
		</main>

		<footer class="site-footer">
			<div class="footer-content">
				<p class="footer-text">© <?= date('Y') ?> BNGRC - Système de Gestion des Collectes et Distributions</p>
			</div>
		</footer>
	</div>
</body>
</html>