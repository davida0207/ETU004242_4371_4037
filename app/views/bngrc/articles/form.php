<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier article' : 'Ajouter article';
ob_start();

$article = $article ?? ['categorie' => 'nature', 'libelle' => '', 'unite' => '', 'prix_unitaire' => '0', 'actif' => 1];
$errors = $errors ?? ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''];
$action = $mode === 'edit' ? '/articles/' . (int)($article['id'] ?? 0) . '/edit' : '/articles/add';
?>

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex align-items-center justify-content-between">
					<h6><?= $title ?></h6>
					<a class="btn btn-outline-secondary btn-sm" href="/articles">← Retour</a>
				</div>
			</div>
			<div class="card-body">
				<form method="post" action="<?= htmlspecialchars($action) ?>">
					<div class="form-group">
						<label class="form-control-label">Catégorie</label>
						<select class="form-select" name="categorie">
							<?php foreach (($categories ?? []) as $key => $label): ?>
								<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($article['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
									<?= htmlspecialchars((string)$label) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (!empty($errors['categorie'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['categorie']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Libellé</label>
						<input class="form-control" type="text" name="libelle" value="<?= htmlspecialchars((string)($article['libelle'] ?? '')) ?>">
						<?php if (!empty($errors['libelle'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['libelle']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Unité</label>
						<input class="form-control" type="text" name="unite" value="<?= htmlspecialchars((string)($article['unite'] ?? '')) ?>">
						<?php if (!empty($errors['unite'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['unite']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Prix unitaire</label>
						<input class="form-control" type="number" step="0.01" name="prix_unitaire" value="<?= htmlspecialchars((string)($article['prix_unitaire'] ?? '0')) ?>">
						<?php if (!empty($errors['prix_unitaire'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['prix_unitaire']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Actif</label>
						<select class="form-select" name="actif">
							<option value="1" <?= ((int)($article['actif'] ?? 1) === 1) ? 'selected' : '' ?>>Oui</option>
							<option value="0" <?= ((int)($article['actif'] ?? 1) === 0) ? 'selected' : '' ?>>Non</option>
						</select>
					</div>

					<div class="form-group">
						<button class="btn bg-gradient-primary" type="submit">Enregistrer</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
