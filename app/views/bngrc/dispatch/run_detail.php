<?php
$title = 'Dispatch Run #' . ($run['id'] ?? '?');
ob_start();

$nonce = \Flight::get('csp_nonce');
$flash = $flash ?? '';
$run = $run ?? [];
$allocations = $allocations ?? [];

$flashMap = [
	'created' => ['success', 'Dispatch exécuté avec succès ! Voici les allocations créées.', 'bi bi-check-circle-fill'],
];

function fmtQtyRd(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}
function moneyArRd(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

/* Grouper par article */
$grouped = [];
$totalValeur = 0;
foreach ($allocations as $a) {
	$key = $a['article'];
	if (!isset($grouped[$key])) {
		$grouped[$key] = [
			'article'    => $a['article'],
			'categorie'  => $a['categorie'],
			'unite'      => $a['unite'],
			'prix_unitaire' => (float)$a['prix_unitaire'],
			'rows'       => [],
			'total_qty'  => 0,
		];
	}
	$grouped[$key]['rows'][] = $a;
	$grouped[$key]['total_qty'] += (float)$a['quantite'];
	$totalValeur += (float)$a['quantite'] * (float)$a['prix_unitaire'];
}
?>

<!-- Flash -->
<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-<?= $flashMap[$flash][0] ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="<?= $flashMap[$flash][2] ?>"></i></span>
		<span class="alert-text"><strong>Succès !</strong> <?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<?php endif; ?>

<!-- Header + navigation -->
<div class="row mb-4">
	<div class="col-12 d-flex justify-content-between align-items-center">
		<a href="/dispatch/runs" class="btn btn-sm btn-outline-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Historique</a>
		<form method="post" action="/dispatch/runs/<?= $run['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Supprimer ce run et toutes ses allocations ?');">
			<button type="submit" class="btn btn-sm btn-outline-danger mb-0"><i class="fas fa-trash-alt me-1"></i> Supprimer ce run</button>
		</form>
	</div>
</div>

<!-- Infos du run -->
<div class="row mb-4">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h6 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i> Informations du run</h6>
					<span class="badge bg-gradient-info badge-lg">#<?= $run['id'] ?></span>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<p class="text-sm mb-2"><strong>Date d'exécution :</strong><br><?= date('d/m/Y à H:i:s', strtotime($run['ran_at'])) ?></p>
						<p class="text-sm mb-2"><strong>Note :</strong><br><?= htmlspecialchars($run['note'] ?? '—') ?></p>
					</div>
					<div class="col-md-6">
						<p class="text-sm mb-2"><strong>Méthode :</strong><br>
							<?php
								$mLabel = match($run['methode'] ?? 'fifo') {
									'smallest'      => 'Plus petit d\'abord',
									'proportional'  => 'Proportionnel',
									default         => 'Ancienneté (FIFO)',
								};
								$mBadge = match($run['methode'] ?? 'fifo') {
									'smallest'      => 'bg-gradient-success',
									'proportional'  => 'bg-gradient-info',
									default         => 'bg-gradient-primary',
								};
							?>
							<span class="badge <?= $mBadge ?> badge-lg"><?= $mLabel ?></span>
						</p>
						<p class="text-sm mb-2"><strong>Allocations créées :</strong><br><span class="text-lg font-weight-bold"><?= $run['nb_allocations'] ?></span></p>
						<p class="text-sm mb-2"><strong>Dons traités / Besoins couverts :</strong><br><?= $run['nb_dons'] ?> dons → <?= $run['nb_besoins'] ?> besoins</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i> Résumé</h6>
			</div>
			<div class="card-body d-flex flex-column justify-content-center">
				<div class="text-center">
					<h3 class="mb-0 text-success"><?= moneyArRd($totalValeur) ?></h3>
					<p class="text-sm text-muted mb-3">Valeur totale répartie</p>
					<div class="d-flex justify-content-around">
						<div>
							<h5 class="mb-0"><?= count($grouped) ?></h5>
							<p class="text-xs text-muted mb-0">Articles</p>
						</div>
						<div>
							<h5 class="mb-0"><?= fmtQtyRd((float)$run['total_quantite']) ?></h5>
							<p class="text-xs text-muted mb-0">Quantité totale</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Allocations groupées par article -->
<?php foreach ($grouped as $g): ?>
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h6 class="mb-0">
						<i class="fas fa-box me-2 text-dark"></i>
						<?= htmlspecialchars($g['article']) ?>
						<span class="badge bg-gradient-secondary ms-2"><?= htmlspecialchars($g['categorie']) ?></span>
					</h6>
					<span class="text-sm text-muted">
						Total : <strong><?= fmtQtyRd($g['total_qty']) ?> <?= htmlspecialchars($g['unite']) ?></strong>
						—
						<strong><?= moneyArRd($g['total_qty'] * $g['prix_unitaire']) ?></strong>
					</span>
				</div>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<div class="table-responsive p-0">
					<table class="table align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Don</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">→ Besoin</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Attribué</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valeur</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($g['rows'] as $a): ?>
							<tr>
								<td>
									<div class="d-flex flex-column px-3 py-1">
										<a href="/dons/<?= $a['don_id'] ?>" class="text-xs font-weight-bold text-primary">Don #<?= $a['don_id'] ?></a>
										<span class="text-xxs"><?= htmlspecialchars($a['don_source'] ?? '—') ?></span>
										<span class="text-xxs text-muted"><?= fmtQtyRd((float)$a['don_quantite']) ?> <?= htmlspecialchars($a['unite']) ?> · <?= date('d/m/Y', strtotime($a['date_don'])) ?></span>
										<?php if (!empty($a['don_note'])): ?>
											<span class="text-xxs text-muted fst-italic"><?= htmlspecialchars($a['don_note']) ?></span>
										<?php endif; ?>
									</div>
								</td>
								<td>
									<div class="d-flex flex-column py-1">
										<a href="/besoins/<?= $a['besoin_id'] ?>" class="text-xs font-weight-bold text-danger">Besoin #<?= $a['besoin_id'] ?></a>
										<span class="text-xxs font-weight-bold"><?= htmlspecialchars($a['ville']) ?> <span class="text-muted font-weight-normal">(<?= htmlspecialchars($a['region']) ?>)</span></span>
										<span class="text-xxs text-muted"><?= fmtQtyRd((float)$a['besoin_quantite']) ?> <?= htmlspecialchars($a['unite']) ?> · <?= date('d/m/Y', strtotime($a['date_besoin'])) ?></span>
										<?php if (!empty($a['besoin_note'])): ?>
											<span class="text-xxs text-muted fst-italic"><?= htmlspecialchars($a['besoin_note']) ?></span>
										<?php endif; ?>
									</div>
								</td>
								<td class="align-middle text-center">
									<span class="text-sm font-weight-bold"><?= fmtQtyRd((float)$a['quantite']) ?></span>
									<span class="text-xxs text-muted"><?= htmlspecialchars($a['unite']) ?></span>
								</td>
								<td class="align-middle text-center">
									<span class="text-xs"><?= moneyArRd((float)$a['quantite'] * (float)$a['prix_unitaire']) ?></span>
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
<?php endforeach; ?>

<?php if (empty($allocations)): ?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body text-center py-4">
				<p class="text-sm text-muted mb-0">Aucune allocation dans ce run.</p>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
?>
