<?php
$title = 'Régions';
ob_start();

$flash = $flash ?? null;
$error = $error ?? null;
$flashMap = [
	'created' => ['success', 'Région créée avec succès.'],
	'updated' => ['success', 'Région modifiée avec succès.'],
	'deleted' => ['success', 'Région supprimée.'],
	'blocked' => ['warning', 'Suppression impossible : des villes existent déjà.'],
];
?>

<?php if (!empty($error)): ?>
	<div class="flash flash-warn">
		<?= htmlspecialchars((string)$error) ?>
	</div>
<?php endif; ?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-<?= $flashMap[$flash][0] ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="fas fa-<?= $flashMap[$flash][0] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i></span>
		<span class="alert-text"><?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
	</div>
<?php endif; ?>

<div class="row">
	<div class="col-12">
		<div class="card mb-4">
			<div class="card-header pb-0 d-flex justify-content-between align-items-center">
				<h6>Liste des régions</h6>
				<a class="btn btn-sm bg-gradient-primary text-white" href="/regions/add">
					<i class="fas fa-plus me-1"></i> Ajouter une région
				</a>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<div class="table-responsive p-0">
					<table class="table align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach (($regions ?? []) as $r): ?>
							<tr>
								<td>
									<div class="d-flex px-2 py-1">
										<span class="text-xs font-weight-bold">#<?= (int)$r['id'] ?></span>
									</div>
								</td>
								<td>
									<p class="text-sm font-weight-bold mb-0"><?= htmlspecialchars((string)$r['nom']) ?></p>
								</td>
								<td class="align-middle text-center">
									<div class="actions-cell justify-content-center">
										<a href="/regions/<?= (int)$r['id'] ?>/edit" class="btn btn-sm btn-outline-secondary mb-0">
											<i class="fas fa-pencil-alt text-xs"></i> Modifier
										</a>
										<form method="post" action="/regions/<?= (int)$r['id'] ?>/delete" class="inline-form">
											<button class="btn btn-sm btn-outline-danger mb-0" type="submit">
												<i class="fas fa-trash text-xs"></i> Supprimer
											</button>
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
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
