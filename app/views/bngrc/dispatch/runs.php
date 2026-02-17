<?php
$title = 'Historique des dispatch runs';
ob_start();

$nonce = \Flight::get('csp_nonce');
$flash = $flash ?? '';
$runs  = $runs ?? [];

$flashMap = [
	'deleted' => ['success', 'Run et allocations supprimés.', 'ni ni-check-bold'],
	'error'   => ['danger',  'Erreur lors de la suppression.', 'ni ni-fat-remove'],
];

function fmtQtyD(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}
?>

<!-- Flash -->
<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-<?= $flashMap[$flash][0] ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="<?= $flashMap[$flash][2] ?>"></i></span>
		<span class="alert-text"><?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<?php endif; ?>

<!-- Header -->
<div class="row mb-4">
	<div class="col-12 d-flex justify-content-between align-items-center">
		<div>
			<a href="/dispatch" class="btn btn-sm btn-outline-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Retour simulation</a>
		</div>
	</div>
</div>

<!-- Liste des runs -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-history me-2 text-info"></i> Tous les dispatch runs (<?= count($runs) ?>)</h6>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<?php if (empty($runs)): ?>
					<p class="text-sm text-muted text-center py-4">Aucun dispatch run enregistré.</p>
				<?php else: ?>
					<div class="table-responsive p-0">
						<table class="table align-items-center mb-0">
							<thead>
								<tr>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Run #</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date d'exécution</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Allocations</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantité totale</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dons traités</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Besoins couverts</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Note</th>
									<th class="text-secondary opacity-7 text-end pe-3">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($runs as $r): ?>
								<tr>
									<td>
										<div class="d-flex px-3 py-1">
											<span class="badge bg-gradient-info">#<?= $r['id'] ?></span>
										</div>
									</td>
									<td>
										<span class="text-xs font-weight-bold"><?= date('d/m/Y H:i:s', strtotime($r['ran_at'])) ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-sm font-weight-bold"><?= $r['nb_allocations'] ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-xs"><?= fmtQtyD((float)$r['total_quantite']) ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-xs"><?= $r['nb_dons'] ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-xs"><?= $r['nb_besoins'] ?></span>
									</td>
									<td>
										<span class="text-xs text-muted"><?= htmlspecialchars($r['note'] ?? '—') ?></span>
									</td>
									<td class="align-middle text-end pe-3">
										<a href="/dispatch/runs/<?= $r['id'] ?>" class="btn btn-sm btn-outline-info mb-0 px-2" title="Détail">
											<i class="fas fa-eye"></i>
										</a>
										<form method="post" action="/dispatch/runs/<?= $r['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Supprimer ce run et toutes ses allocations ?');">
											<button type="submit" class="btn btn-sm btn-outline-danger mb-0 px-2" title="Supprimer">
												<i class="fas fa-trash-alt"></i>
											</button>
										</form>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
?>
