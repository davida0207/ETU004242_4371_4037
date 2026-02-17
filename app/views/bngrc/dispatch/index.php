<?php
$title = 'Dispatch — Simulation';
ob_start();

$nonce = \Flight::get('csp_nonce');
$flash = $flash ?? '';
$stats = $stats ?? ['dons_total' => 0, 'besoins_total' => 0, 'dons_restant' => 0, 'besoins_restant' => 0];
$runs  = $runs ?? [];

$flashMap = [
	'created' => ['success', 'Dispatch exécuté avec succès !', 'bi bi-check-circle-fill'],
	'reset'   => ['info',    'Simulation réinitialisée — toutes les allocations ont été supprimées.', 'bi bi-bell-fill'],
	'empty'   => ['warning', 'Aucune allocation créée : aucun don ne correspond à un besoin ouvert.', 'bi bi-bell-fill'],
	'error'   => ['danger',  'Erreur lors du dispatch. Veuillez réessayer.', 'bi bi-x-circle-fill'],
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
							<i class="bi bi-basket3 text-lg opacity-10" aria-hidden="true"></i>
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
							<i class="bi bi-truck text-lg opacity-10" aria-hidden="true"></i>
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
							<i class="bi bi-list-ul text-lg opacity-10" aria-hidden="true"></i>
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
							<i class="bi bi-bar-chart-fill text-lg opacity-10" aria-hidden="true"></i>
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
				<p class="text-sm text-muted mb-0">Choisissez la méthode de répartition puis lancez le dispatch.</p>
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

				<?php $isDisabled = ($stats['dons_restant'] === 0 || $stats['besoins_restant'] === 0); ?>

				<form method="post" action="/dispatch/run" id="dispatchForm">
					<!-- Note -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Note (optionnelle)</label>
						<input type="text" name="note" class="form-control" placeholder="Ex: Dispatch du jour, Test allocation…" maxlength="255">
					</div>

					<!-- Choix de la méthode — 3 cartes cliquables -->
					<input type="hidden" name="methode" id="methodeInput" value="fifo">

					<label class="form-label text-xs text-uppercase font-weight-bold mb-2">Méthode de répartition</label>
					<div class="row g-3 mb-4">
						<!-- FIFO -->
						<div class="col-md-4">
							<div class="card border border-2 border-primary shadow-sm method-card active" data-method="fifo" style="cursor:pointer;">
								<div class="card-body text-center p-3">
									<div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle mx-auto mb-2" style="width:48px; height:48px;">
										<i class="fas fa-sort-amount-down text-lg text-white" aria-hidden="true"></i>
									</div>
									<h6 class="text-sm font-weight-bolder mb-1">Ancienneté (FIFO)</h6>
									<p class="text-xs text-muted mb-0">Premier arrivé, premier servi. Les besoins les plus anciens reçoivent en premier.</p>
								</div>
							</div>
						</div>
						<!-- Smallest -->
						<div class="col-md-4">
							<div class="card border border-2 border-light shadow-sm method-card" data-method="smallest" style="cursor:pointer;">
								<div class="card-body text-center p-3">
									<div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle mx-auto mb-2" style="width:48px; height:48px;">
										<i class="fas fa-sort-numeric-down text-lg text-white" aria-hidden="true"></i>
									</div>
									<h6 class="text-sm font-weight-bolder mb-1">Plus petit d'abord</h6>
									<p class="text-xs text-muted mb-0">Les besoins les plus petits (en quantité) sont satisfaits en priorité.</p>
								</div>
							</div>
						</div>
						<!-- Proportional -->
						<div class="col-md-4">
							<div class="card border border-2 border-light shadow-sm method-card" data-method="proportional" style="cursor:pointer;">
								<div class="card-body text-center p-3">
									<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle mx-auto mb-2" style="width:48px; height:48px;">
										<i class="fas fa-balance-scale text-lg text-white" aria-hidden="true"></i>
									</div>
									<h6 class="text-sm font-weight-bolder mb-1">Proportionnel</h6>
									<p class="text-xs text-muted mb-0">Chaque besoin reçoit une part proportionnelle à sa quantité restante.</p>
								</div>
							</div>
						</div>
					</div>

					<button type="submit" class="btn bg-gradient-primary w-100"
							<?= $isDisabled ? 'disabled' : '' ?>>
						<i class="fas fa-play me-1"></i> Lancer le dispatch
					</button>
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

				<!-- Légende méthodes -->
				<hr class="horizontal dark my-3">
				<h6 class="text-xs text-uppercase font-weight-bold opacity-6 mb-2">Aide — Méthodes</h6>
				<div class="mb-2">
					<span class="badge bg-gradient-primary me-1">FIFO</span>
					<span class="text-xs">Date demande ↑ — premier arrivé, premier servi</span>
				</div>
				<div class="mb-2">
					<span class="badge bg-gradient-success me-1">Plus petit</span>
					<span class="text-xs">Quantité ↑ — petit besoins servis d'abord</span>
				</div>
				<div class="mb-0">
					<span class="badge bg-gradient-info me-1">Proportionnel</span>
					<span class="text-xs">Répartition au prorata des restes</span>
				</div>
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
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Méthode</th>
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
										<?php
											$mLabel = match($r['methode'] ?? 'fifo') {
												'smallest'      => 'Plus petit',
												'proportional'  => 'Proportionnel',
												default         => 'FIFO',
											};
											$mBadge = match($r['methode'] ?? 'fifo') {
												'smallest'      => 'bg-gradient-success',
												'proportional'  => 'bg-gradient-info',
												default         => 'bg-gradient-primary',
											};
										?>
										<span class="badge <?= $mBadge ?>"><?= $mLabel ?></span>
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

<script nonce="<?= \Flight::get('csp_nonce') ?>">
document.querySelectorAll('.method-card').forEach(card => {
	card.addEventListener('click', () => {
		// Retirer la sélection de toutes les cartes
		document.querySelectorAll('.method-card').forEach(c => {
			c.classList.remove('active', 'border-primary', 'border-success', 'border-info');
			c.classList.add('border-light');
		});
		// Activer la carte cliquée
		const method = card.dataset.method;
		const colorMap = { fifo: 'border-primary', smallest: 'border-success', proportional: 'border-info' };
		card.classList.add('active', colorMap[method]);
		card.classList.remove('border-light');
		// Mettre à jour le champ caché
		document.getElementById('methodeInput').value = method;
	});
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
?>
