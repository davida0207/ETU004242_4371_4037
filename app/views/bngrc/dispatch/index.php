<?php
$title = 'Dispatch — Simulation';
ob_start();

$nonce = \Flight::get('csp_nonce');
$flash = $flash ?? '';
$stats = $stats ?? ['dons_total' => 0, 'besoins_total' => 0, 'dons_restant' => 0, 'besoins_restant' => 0];
$runs  = $runs ?? [];

$flashMap = [
	'created' => ['success', 'Dispatch exécuté avec succès !', 'ni ni-check-bold'],
	'reset'   => ['info',    'Simulation réinitialisée — toutes les allocations ont été supprimées.', 'ni ni-bell-55'],
	'empty'   => ['warning', 'Aucune allocation créée : aucun don ne correspond à un besoin ouvert.', 'ni ni-bell-55'],
	'error'   => ['danger',  'Erreur lors du dispatch. Veuillez réessayer.', 'ni ni-fat-remove'],
];
?>

<!-- Flash -->
<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-<?= $flashMap[$flash][0] ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="<?= $flashMap[$flash][2] ?>"></i></span>
		<span class="alert-text"><strong><?= $flashMap[$flash][0] === 'danger' ? 'Erreur !' : ($flashMap[$flash][0] === 'warning' ? 'Attention !' : 'OK !') ?></strong> <?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<?php endif; ?>

<!-- Cartes de statistiques -->
<div class="row mb-4">
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Dons total</p>
							<h5 class="font-weight-bolder mb-0"><?= $stats['dons_total'] ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
							<i class="ni ni-basket text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Dons restants</p>
							<h5 class="font-weight-bolder mb-0 <?= $stats['dons_restant'] > 0 ? 'text-warning' : 'text-success' ?>"><?= $stats['dons_restant'] ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
							<i class="ni ni-delivery-fast text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins total</p>
							<h5 class="font-weight-bolder mb-0"><?= $stats['besoins_total'] ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
							<i class="ni ni-bullet-list-67 text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins ouverts</p>
							<h5 class="font-weight-bolder mb-0 <?= $stats['besoins_restant'] > 0 ? 'text-danger' : 'text-success' ?>"><?= $stats['besoins_restant'] ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
							<i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Zone d'action -->
<div class="row mb-4">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<h6><i class="fas fa-random me-2 text-primary"></i> Lancer un dispatch</h6>
				<p class="text-sm text-muted mb-0">Le moteur répartit automatiquement les dons vers les besoins par article, en suivant l'ordre chronologique (date ASC, id ASC).</p>
			</div>
			<div class="card-body">
				<?php if ($stats['dons_restant'] === 0 || $stats['besoins_restant'] === 0): ?>
					<div class="alert alert-secondary text-white text-sm mb-3">
						<i class="fas fa-info-circle me-1"></i>
						<?php if ($stats['dons_restant'] === 0 && $stats['besoins_restant'] === 0): ?>
							Tous les dons sont attribués et tous les besoins sont couverts.
						<?php elseif ($stats['dons_restant'] === 0): ?>
							Aucun don restant à attribuer.
						<?php else: ?>
							Aucun besoin ouvert à satisfaire.
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<form method="post" action="/dispatch/run" class="row g-3 align-items-end">
					<div class="col-md-8">
						<label class="form-label text-xs text-uppercase font-weight-bold">Note (optionnelle)</label>
						<input type="text" name="note" class="form-control" placeholder="Ex: Dispatch du jour, Test allocation…" maxlength="255">
					</div>
					<div class="col-md-4">
						<button type="submit" class="btn bg-gradient-primary w-100"
								<?= ($stats['dons_restant'] === 0 || $stats['besoins_restant'] === 0) ? 'disabled' : '' ?>>
							<i class="fas fa-play me-1"></i> Lancer le dispatch
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6><i class="fas fa-undo me-2 text-danger"></i> Réinitialiser</h6>
			</div>
			<div class="card-body d-flex flex-column justify-content-between">
				<p class="text-sm text-muted">Supprimer <strong>toutes</strong> les allocations et <strong>tous</strong> les dispatch runs. Les dons et besoins ne sont pas affectés.</p>
				<form method="post" action="/dispatch/reset" onsubmit="return confirm('⚠️ Supprimer TOUTES les allocations et TOUS les runs ? Cette action est irréversible.');">
					<button type="submit" class="btn btn-outline-danger w-100">
						<i class="fas fa-trash-alt me-1"></i> Tout réinitialiser
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Historique des derniers runs -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0 d-flex justify-content-between align-items-center">
				<h6 class="mb-0"><i class="fas fa-history me-2 text-info"></i> Derniers dispatch runs</h6>
				<?php if (count($runs) > 0): ?>
					<a href="/dispatch/runs" class="btn btn-sm btn-outline-primary mb-0">Voir tout l'historique</a>
				<?php endif; ?>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<?php if (empty($runs)): ?>
					<p class="text-sm text-muted text-center py-4">Aucun dispatch n'a encore été lancé.</p>
				<?php else: ?>
					<div class="table-responsive p-0">
						<table class="table align-items-center mb-0">
							<thead>
								<tr>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Run #</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Allocations</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dons</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Besoins</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Note</th>
									<th class="text-secondary opacity-7"></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach (array_slice($runs, 0, 5) as $r): ?>
								<tr>
									<td>
										<div class="d-flex px-3 py-1">
											<span class="badge bg-gradient-info">#<?= $r['id'] ?></span>
										</div>
									</td>
									<td>
										<span class="text-xs font-weight-bold"><?= date('d/m/Y H:i', strtotime($r['ran_at'])) ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-sm font-weight-bold"><?= $r['nb_allocations'] ?></span>
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
										<a href="/dispatch/runs/<?= $r['id'] ?>" class="btn btn-sm btn-outline-info mb-0 px-3" title="Détail">
											<i class="fas fa-eye"></i>
										</a>
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
