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
	<div class="alert alert-success alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="ni ni-check-bold"></i></span>
		<span class="alert-text"><?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
<?php endif; ?>

<div class="card mb-4">
	<div class="card-body">
		<form method="get" action="/articles" class="filters-form">
			<select name="categorie" class="form-select form-select-sm">
				<option value="">Toutes les catégories</option>
				<?php foreach (($categories ?? []) as $key => $label): ?>
					<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
						<?= htmlspecialchars((string)$label) ?>
					</option>
				<?php endforeach; ?>
			</select>
			<button class="btn btn-sm btn-outline-secondary" type="submit">Filtrer</button>
			<a class="btn btn-sm bg-gradient-primary text-white" href="/articles/add">+ Ajouter un article</a>
		</form>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header pb-0">
		<h6>Liste des articles</h6>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catégorie</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Libellé</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Unité</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prix unitaire</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actif</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
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
						<td>
							<?php if ((int)$a['actif'] === 1): ?>
								<span class="badge badge-sm bg-gradient-success">Oui</span>
							<?php else: ?>
								<span class="badge badge-sm bg-gradient-secondary">Non</span>
							<?php endif; ?>
						</td>
						<td>
							<a class="btn btn-sm btn-outline-secondary mb-0" href="/articles/<?= (int)$a['id'] ?>/edit">Modifier</a>
							<?php if ((int)$a['actif'] === 1): ?>
								<form method="post" action="/articles/<?= (int)$a['id'] ?>/deactivate" class="inline-form">
									<button class="btn btn-sm btn-outline-danger mb-0" type="submit">Désactiver</button>
								</form>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
