<?php
$title = $title ?? 'BNGRC';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars((string)$title) ?></title>
	<?php include __DIR__ . '/_style.php'; ?>
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>BNGRC - Suivi Collectes / Distributions</h1>
			<p>
				
				
				<a href="/bngrc/dashboard">Dashboard dynamique</a>
				
				<a href="/besoins">Besoins</a>
				
				<a href="/dons">Dons</a>
				
				<a href="/regions">Régions</a>
				
				<a href="/villes">Villes</a>
				
				<a href="/articles">Articles</a>
			</p>
		</div>

		<?= $content ?? '' ?>
	</div>
</body>
</html>
