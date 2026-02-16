<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier besoin' : 'Ajouter besoin';
ob_start();

$besoin = $besoin ?? [];
$errors = $errors ?? ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''];
$action = $mode === 'edit' ? '/besoins/' . (int)($besoin['id'] ?? 0) . '/edit' : '/besoins/add';
?>

<div class="card">
	<p><a class="btn btn-secondary" href="/besoins">← Retour</a></p>

	<form method="post" action="<?= htmlspecialchars($action) ?>">
		<label>Ville</label>
		<select class="input" name="ville_id">
			<option value="">-- Choisir --</option>
			<?php foreach (($villes ?? []) as $v): ?>
				<option value="<?= (int)$v['id'] ?>" <?= ((string)($besoin['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$v['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (!empty($errors['ville_id'])): ?><div class="error"><?= htmlspecialchars((string)$errors['ville_id']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Article</label>
		<select class="input" name="article_id">
			<option value="">-- Choisir --</option>
			<?php foreach (($articles ?? []) as $a): ?>
				<option value="<?= (int)$a['id'] ?>" <?= ((string)($besoin['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$a['libelle']) ?> (<?= htmlspecialchars((string)$a['categorie']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (!empty($errors['article_id'])): ?><div class="error"><?= htmlspecialchars((string)$errors['article_id']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Quantité</label>
		<input class="input" type="number" step="0.01" name="quantite" value="<?= htmlspecialchars((string)($besoin['quantite'] ?? '')) ?>">
		<?php if (!empty($errors['quantite'])): ?><div class="error"><?= htmlspecialchars((string)$errors['quantite']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Date besoin</label>
		<input class="input" type="date" name="date_besoin" value="<?= htmlspecialchars((string)($besoin['date_besoin'] ?? '')) ?>">
		<?php if (!empty($errors['date_besoin'])): ?><div class="error"><?= htmlspecialchars((string)$errors['date_besoin']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Note</label>
		<input class="input" type="text" name="note" value="<?= htmlspecialchars((string)($besoin['note'] ?? '')) ?>">

		<div class="form-group">
			<button class="btn btn-primary" type="submit">Enregistrer</button>
		</div>
	</form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
