<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier article' : 'Ajouter article';
ob_start();

$nonce   = \Flight::get('csp_nonce');
$article = $article ?? ['categorie' => 'nature', 'libelle' => '', 'unite' => '', 'prix_unitaire' => '0', 'actif' => 1];
$errors  = $errors ?? ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''];
$action  = $mode === 'edit' ? '/articles/' . (int)($article['id'] ?? 0) . '/edit' : '/articles/add';

/* Règles métier par catégorie :
   - argent   → unité = Ar, prix_unitaire = 1 (fixe)
   - nature   → unité libre, prix_unitaire libre
   - materiau → unité libre, prix_unitaire libre
*/
$catRules = [
	'argent'   => ['unite' => 'Ar',  'prix' => '1', 'lock' => true],
	'nature'   => ['unite' => '',    'prix' => '',   'lock' => false],
	'materiau' => ['unite' => '',    'prix' => '',   'lock' => false],
];
$currentCat   = (string)($article['categorie'] ?? 'nature');
$isLocked     = ($catRules[$currentCat]['lock'] ?? false);
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
				<form method="post" action="<?= htmlspecialchars($action) ?>" id="articleForm">
					<div class="form-group">
						<label class="form-control-label">Catégorie</label>
						<select class="form-select" name="categorie" id="selCategorie">
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
						<input class="form-control" type="text" name="unite" id="inputUnite"
							   value="<?= htmlspecialchars((string)($article['unite'] ?? '')) ?>"
							   <?= $isLocked ? 'readonly' : '' ?>>
						<?php if ($isLocked): ?>
							<small class="text-muted">Fixé automatiquement pour la catégorie « Argent ».</small>
						<?php endif; ?>
						<?php if (!empty($errors['unite'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['unite']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Prix unitaire</label>
						<input class="form-control" type="number" step="0.01" name="prix_unitaire" id="inputPrix"
							   value="<?= htmlspecialchars((string)($article['prix_unitaire'] ?? '0')) ?>"
							   <?= $isLocked ? 'readonly' : '' ?>>
						<?php if ($isLocked): ?>
							<small class="text-muted">Fixé à 1 pour la catégorie « Argent » (1 Ar = 1 Ar).</small>
						<?php endif; ?>
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

<?php ob_start(); ?>
<script nonce="<?= htmlspecialchars($nonce) ?>">
document.addEventListener('DOMContentLoaded', function() {
	var sel   = document.getElementById('selCategorie');
	var unite = document.getElementById('inputUnite');
	var prix  = document.getElementById('inputPrix');

	/* Règles par catégorie */
	var rules = <?= json_encode($catRules) ?>;

	function applyRules() {
		var cat  = sel.value;
		var rule = rules[cat] || { unite: '', prix: '', lock: false };

		if (rule.lock) {
			unite.value    = rule.unite;
			unite.readOnly = true;
			prix.value     = rule.prix;
			prix.readOnly  = true;
		} else {
			/* Si on revient d'Argent vers nature/materiau, on vide les champs verrouillés */
			if (unite.readOnly) {
				unite.value = '';
				prix.value  = '';
			}
			unite.readOnly = false;
			prix.readOnly  = false;
		}
	}

	sel.addEventListener('change', applyRules);
});
</script>
<?php
$pageScripts = ob_get_clean();
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
