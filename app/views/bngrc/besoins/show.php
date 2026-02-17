<?php
$title = 'Détail besoin';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}
function fmtQty(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}

$besoin      = $besoin ?? [];
$allocations = $allocations ?? [];
$canDelete   = $canDelete ?? false;
$quantite    = (float)($besoin['quantite'] ?? 0);
$attribueQ   = (float)($besoin['attribue_quantite'] ?? 0);
$resteQ      = max(0.0, $quantite - $attribueQ);
$prix        = (float)($besoin['prix_unitaire'] ?? 0);
$valeur      = $quantite * $prix;
$couvertVal  = $attribueQ * $prix;
$resteVal    = $resteQ * $prix;
$pctCouvert  = $quantite > 0 ? round($attribueQ / $quantite * 100) : 0;

$catBadge = match($besoin['categorie'] ?? '') {
	'nature'   => 'bg-gradient-success',
	'materiau' => 'bg-gradient-info',
	'argent'   => 'bg-gradient-warning',
	default    => 'bg-gradient-secondary',
};
$catLabel = match($besoin['categorie'] ?? '') {
	'nature'   => 'Nature',
	'materiau' => 'Matériau',
	'argent'   => 'Argent',
	default    => ucfirst($besoin['categorie'] ?? ''),
};

$progressColor = $pctCouvert >= 100 ? 'bg-success' : ($pctCouvert >= 50 ? 'bg-info' : ($pctCouvert > 0 ? 'bg-warning' : 'bg-secondary'));
?>

<!-- En-tête + retour -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h5 class="text-white mb-1"><i class="ni ni-bullet-list-67 me-2"></i>Besoin #<?= (int)$besoin['id'] ?></h5>
		<p class="text-white text-sm opacity-8 mb-0"><?= htmlspecialchars((string)$besoin['ville']) ?> — <?= htmlspecialchars((string)$besoin['region']) ?></p>
	</div>
	<a class="btn btn-sm btn-white mb-0" href="/besoins"><i class="fas fa-arrow-left me-1"></i> Retour à la liste</a>
</div>

<!-- Informations du besoin -->
<div class="row">
	<div class="col-lg-8">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Informations du besoin</h6>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Région</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$besoin['region']) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Ville</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$besoin['ville']) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Article</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$besoin['libelle']) ?> <span class="badge badge-sm <?= $catBadge ?>"><?= htmlspecialchars($catLabel) ?></span></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Date du besoin</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$besoin['date_besoin']) ?></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Quantité</span>
							<p class="text-sm mb-0"><?= fmtQty($quantite) ?> <?= htmlspecialchars((string)$besoin['unite']) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Prix unitaire</span>
							<p class="text-sm mb-0"><?= moneyAr($prix) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Valeur totale</span>
							<p class="text-sm font-weight-bold mb-0"><?= moneyAr($valeur) ?></p>
						</div>
						<?php if (!empty($besoin['note'])): ?>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Note</span>
							<p class="text-sm mb-0"><?= nl2br(htmlspecialchars((string)$besoin['note'])) ?></p>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<hr class="horizontal dark">
				<div class="d-flex gap-2">
					<a class="btn btn-sm bg-gradient-dark" href="/besoins/<?= (int)$besoin['id'] ?>/edit"><i class="fas fa-pencil-alt me-1"></i> Modifier</a>
					<?php if ($canDelete): ?>
						<form method="post" action="/besoins/<?= (int)$besoin['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
							<button class="btn btn-sm btn-outline-danger mb-0" type="submit"><i class="fas fa-trash me-1"></i> Supprimer</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Résumé couverture -->
	<div class="col-lg-4">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-chart-pie text-info me-2"></i>Couverture</h6>
			</div>
			<div class="card-body text-center">
				<div class="mb-3">
					<h2 class="font-weight-bolder mb-0"><?= $pctCouvert ?>%</h2>
					<p class="text-sm text-secondary mb-0">couvert</p>
				</div>
				<div class="progress mb-3" style="height:10px;">
					<div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= min($pctCouvert, 100) ?>%;" aria-valuenow="<?= $pctCouvert ?>" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<div class="row text-center">
					<div class="col-6">
						<span class="text-xs text-uppercase text-secondary font-weight-bold">Couvert</span>
						<h6 class="text-success mb-0"><?= fmtQty($attribueQ) ?> <?= htmlspecialchars((string)$besoin['unite']) ?></h6>
						<p class="text-xs text-secondary"><?= moneyAr($couvertVal) ?></p>
					</div>
					<div class="col-6">
						<span class="text-xs text-uppercase text-secondary font-weight-bold">Reste</span>
						<h6 class="text-danger mb-0"><?= fmtQty($resteQ) ?> <?= htmlspecialchars((string)$besoin['unite']) ?></h6>
						<p class="text-xs text-secondary"><?= moneyAr($resteVal) ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Allocations -->
<div class="card mb-4">
	<div class="card-header pb-0">
		<div class="d-flex justify-content-between align-items-center">
			<h6 class="mb-0"><i class="fas fa-link text-success me-2"></i>Allocations reçues</h6>
			<span class="badge bg-gradient-dark"><?= count($allocations) ?> allocation(s)</span>
		</div>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<?php if (empty($allocations)): ?>
			<div class="text-center py-4">
				<i class="ni ni-basket text-secondary" style="font-size:2rem;"></i>
				<p class="text-sm text-secondary mt-2 mb-0">Aucune allocation pour ce besoin.</p>
			</div>
		<?php else: ?>
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Don</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date don</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Source</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité attribuée</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dispatch run</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($allocations as $al): ?>
					<tr>
						<td>
							<div class="d-flex px-2 py-1">
								<div class="icon icon-shape icon-sm me-2 bg-gradient-success shadow text-center rounded-circle">
									<i class="ni ni-basket text-white text-xs" aria-hidden="true"></i>
								</div>
								<div class="d-flex flex-column justify-content-center">
									<a class="text-sm font-weight-bold mb-0" href="/dons/<?= (int)$al['don_id'] ?>">Don #<?= (int)$al['don_id'] ?></a>
								</div>
							</div>
						</td>
						<td><span class="text-xs text-secondary"><?= htmlspecialchars((string)$al['date_don']) ?></span></td>
						<td><span class="text-xs"><?= htmlspecialchars((string)($al['source'] ?? '—')) ?></span></td>
						<td><span class="text-xs font-weight-bold"><?= htmlspecialchars((string)$al['quantite']) ?></span></td>
						<td>
							<?php if (!empty($al['dispatch_run_id'])): ?>
								<span class="badge badge-sm bg-gradient-info">
									Run #<?= (int)$al['dispatch_run_id'] ?>
									<span class="text-xxs">(<?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$al['dispatch_ran_at']))) ?>)</span>
								</span>
							<?php else: ?>
								<span class="text-xs text-secondary">—</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
