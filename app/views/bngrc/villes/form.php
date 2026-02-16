<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier ville' : 'Ajouter ville';
ob_start();

$ville = $ville ?? ['nom' => '', 'region_id' => ''];
$errors = $errors ?? ['nom' => '', 'region_id' => ''];
$action = $mode === 'edit' ? '/villes/' . (int)($ville['id'] ?? 0) . '/edit' : '/villes/add';
?>

<div class="card">
	<p><a class="btn btn-secondary" href="/villes">← Retour</a></p>

	<form method="post" action="<?= htmlspecialchars($action) ?>">
		<label>Région</label>
		<select class="input" name="region_id">
			<option value="">-- Choisir --</option>
			<?php foreach (($regions ?? []) as $r): ?>
				<option value="<?= (int)$r['id'] ?>" <?= ((string)($ville['region_id'] ?? '') === (string)$r['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$r['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (!empty($errors['region_id'])): ?><div class="error"><?= htmlspecialchars((string)$errors['region_id']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Nom</label>
		<input class="input" type="text" name="nom" value="<?= htmlspecialchars((string)($ville['nom'] ?? '')) ?>">
		<?php if (!empty($errors['nom'])): ?><div class="error"><?= htmlspecialchars((string)$errors['nom']) ?></div><?php endif; ?>

		<div class="form-group">
			<button class="btn btn-primary" type="submit">Enregistrer</button>
		</div>
	</form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
