<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier don' : 'Ajouter don';
ob_start();

$don = $don ?? [];
$errors = $errors ?? ['article_id' => '', 'quantite' => '', 'date_don' => ''];
$action = $mode === 'edit' ? '/dons/' . (int)($don['id'] ?? 0) . '/edit' : '/dons/add';
?>

<div class="card">
	<p><a class="btn btn-secondary" href="/dons">← Retour</a></p>

	<form method="post" action="<?= htmlspecialchars($action) ?>">
		<label>Article</label>
		<select class="input" name="article_id">
			<option value="">-- Choisir --</option>
			<?php foreach (($articles ?? []) as $a): ?>
				<option value="<?= (int)$a['id'] ?>" <?= ((string)($don['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$a['libelle']) ?> (<?= htmlspecialchars((string)$a['categorie']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (!empty($errors['article_id'])): ?><div class="error"><?= htmlspecialchars((string)$errors['article_id']) ?></div><?php endif; ?>

		<p style="margin-top: 12px;"></p>

		<label>Quantité</label>
		<input class="input" type="number" step="0.01" name="quantite" value="<?= htmlspecialchars((string)($don['quantite'] ?? '')) ?>">
		<?php if (!empty($errors['quantite'])): ?><div class="error"><?= htmlspecialchars((string)$errors['quantite']) ?></div><?php endif; ?>

		<p style="margin-top: 12px;"></p>

		<label>Date don</label>
		<input class="input" type="date" name="date_don" value="<?= htmlspecialchars((string)($don['date_don'] ?? '')) ?>">
		<?php if (!empty($errors['date_don'])): ?><div class="error"><?= htmlspecialchars((string)$errors['date_don']) ?></div><?php endif; ?>

		<p style="margin-top: 12px;"></p>

		<label>Source / Donateur (optionnel)</label>
		<input class="input" type="text" name="source" value="<?= htmlspecialchars((string)($don['source'] ?? '')) ?>">

		<p style="margin-top: 12px;"></p>

		<label>Note</label>
		<input class="input" type="text" name="note" value="<?= htmlspecialchars((string)($don['note'] ?? '')) ?>">

		<p style="margin-top: 12px;">
			<button class="btn btn-primary" type="submit">Enregistrer</button>
		</p>
	</form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
