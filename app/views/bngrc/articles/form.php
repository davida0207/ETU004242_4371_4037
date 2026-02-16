<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier article' : 'Ajouter article';
ob_start();

$article = $article ?? ['categorie' => 'nature', 'libelle' => '', 'unite' => '', 'prix_unitaire' => '0', 'actif' => 1];
$errors = $errors ?? ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''];
$action = $mode === 'edit' ? '/articles/' . (int)($article['id'] ?? 0) . '/edit' : '/articles/add';
?>

<div class="card">
	<p><a class="btn btn-secondary" href="/articles">← Retour</a></p>

	<form method="post" action="<?= htmlspecialchars($action) ?>">
		<label>Catégorie</label>
		<select class="input" name="categorie">
			<?php foreach (($categories ?? []) as $key => $label): ?>
				<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($article['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$label) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (!empty($errors['categorie'])): ?><div class="error"><?= htmlspecialchars((string)$errors['categorie']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Libellé</label>
		<input class="input" type="text" name="libelle" value="<?= htmlspecialchars((string)($article['libelle'] ?? '')) ?>">
		<?php if (!empty($errors['libelle'])): ?><div class="error"><?= htmlspecialchars((string)$errors['libelle']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Unité</label>
		<input class="input" type="text" name="unite" value="<?= htmlspecialchars((string)($article['unite'] ?? '')) ?>">
		<?php if (!empty($errors['unite'])): ?><div class="error"><?= htmlspecialchars((string)$errors['unite']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Prix unitaire</label>
		<input class="input" type="number" step="0.01" name="prix_unitaire" value="<?= htmlspecialchars((string)($article['prix_unitaire'] ?? '0')) ?>">
		<?php if (!empty($errors['prix_unitaire'])): ?><div class="error"><?= htmlspecialchars((string)$errors['prix_unitaire']) ?></div><?php endif; ?>
	</div>

	<div class="form-group">
		<label>Actif</label>
		<select class="input" name="actif">
			<option value="1" <?= ((int)($article['actif'] ?? 1) === 1) ? 'selected' : '' ?>>Oui</option>
			<option value="0" <?= ((int)($article['actif'] ?? 1) === 0) ? 'selected' : '' ?>>Non</option>
		</select>

		<div class="form-group">
			<button class="btn btn-primary" type="submit">Enregistrer</button>
		</div>
	</form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
