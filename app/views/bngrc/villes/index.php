<?php
$title = 'Villes';
ob_start();

$flash = $flash ?? null;
$error = $error ?? null;
$flashMap = [
	'created' => ['ok', 'Ville créée.'],
	'updated' => ['ok', 'Ville modifiée.'],
	'deleted' => ['ok', 'Ville supprimée.'],
	'blocked' => ['warn', 'Suppression impossible: des besoins existent ou contrainte FK.'],
];
$filters = $filters ?? ['region_id' => null];
?>

<?php if (!empty($error)): ?>
	<div class="flash flash-warn">
		<?= htmlspecialchars((string)$error) ?>
	</div>
<?php endif; ?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert <?= $flashMap[$flash][0] === 'warn' ? 'alert-warning' : 'alert-success' ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="fas fa-<?= $flashMap[$flash][0] === 'warn' ? 'exclamation-triangle' : 'check-circle' ?>"></i></span>
		<span class="alert-text"><?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<div class="card mb-4">
	<div class="card-body">
		<form method="get" action="/villes" class="filters-form">
			<select name="region_id" class="form-select form-select-sm">
				<option value="">Toutes les régions</option>
				<?php foreach (($regions ?? []) as $r): ?>
					<option value="<?= (int)$r['id'] ?>" <?= ((int)($filters['region_id'] ?? 0) === (int)$r['id']) ? 'selected' : '' ?>>
						<?= htmlspecialchars((string)$r['nom']) ?>
					</option>
				<?php endforeach; ?>
			</select>
			<button class="btn btn-sm btn-outline-secondary" type="submit">Filtrer</button>
			<a class="btn btn-sm bg-gradient-primary text-white" href="/villes/add">+ Ajouter une ville</a>
		</form>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header pb-0">
		<h6>Liste des villes</h6>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ville</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Région</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach (($villes ?? []) as $v): ?>
					<tr>
						<td><?= (int)$v['id'] ?></td>
						<td><?= htmlspecialchars((string)$v['nom']) ?></td>
						<td><?= htmlspecialchars((string)($v['region_nom'] ?? '')) ?></td>
						<td>
							<div class="actions-cell">
								<a class="btn btn-sm btn-outline-secondary mb-0" href="/villes/<?= (int)$v['id'] ?>/edit">Modifier</a>
								<form method="post" action="/villes/<?= (int)$v['id'] ?>/delete" class="inline-form" style="display:inline">
									<button class="btn btn-sm btn-outline-danger mb-0" type="submit">Supprimer</button>
								</form>
							</div>
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
