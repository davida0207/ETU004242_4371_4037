<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier région' : 'Ajouter région';
ob_start();

$region = $region ?? ['nom' => ''];
$errors = $errors ?? ['nom' => ''];
$action = $mode === 'edit' ? '/regions/' . (int)($region['id'] ?? 0) . '/edit' : '/regions/add';
?>

<div class="card">
	<p><a class="btn btn-secondary" href="/regions">← Retour</a></p>

	<form method="post" action="<?= htmlspecialchars($action) ?>">
		<label>Nom</label>
		<input class="input" type="text" name="nom" value="<?= htmlspecialchars((string)($region['nom'] ?? '')) ?>">
		<?php if (!empty($errors['nom'])): ?><div class="error"><?= htmlspecialchars((string)$errors['nom']) ?></div><?php endif; ?>

		<p style="margin-top: 12px;">
			<button class="btn btn-primary" type="submit">Enregistrer</button>
		</p>
	</form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
