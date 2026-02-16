<?php
$title = 'Articles';
ob_start();

$flash = $flash ?? null;
$flashMap = [
	'created' => ['ok', 'Article créé.'],
	'updated' => ['ok', 'Article modifié.'],
	'deactivated' => ['ok', 'Article désactivé.'],
];
$filters = $filters ?? ['categorie' => null];
?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="flash">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
	</div>
<?php endif; ?>

<div class="filters">
	<form method="get" action="/articles" class="filters-form">
		<select name="categorie" class="input input--md">
			<option value="">Toutes les catégories</option>
			<?php foreach (($categories ?? []) as $key => $label): ?>
				<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$label) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button class="btn btn-secondary" type="submit">Filtrer</button>
		<a class="btn btn-primary" href="/articles/add">+ Ajouter un article</a>
	</form>
</div>

<div class="card">
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Catégorie</th>
				<th>Libellé</th>
				<th>Unité</th>
				<th>Prix unitaire</th>
				<th>Actif</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (($articles ?? []) as $a): ?>
			<tr>
				<td><?= (int)$a['id'] ?></td>
				<td><?= htmlspecialchars((string)$a['categorie']) ?></td>
				<td><?= htmlspecialchars((string)$a['libelle']) ?></td>
				<td><?= htmlspecialchars((string)$a['unite']) ?></td>
				<td><?= htmlspecialchars((string)$a['prix_unitaire']) ?></td>
				<td><?= (int)$a['actif'] === 1 ? 'Oui' : 'Non' ?></td>
				<td>
					<a class="btn btn-secondary" href="/articles/<?= (int)$a['id'] ?>/edit">Modifier</a>
					<?php if ((int)$a['actif'] === 1): ?>
						<form method="post" action="/articles/<?= (int)$a['id'] ?>/deactivate" class="inline-form">
							<button class="btn btn-danger" type="submit">Désactiver</button>
						</form>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
