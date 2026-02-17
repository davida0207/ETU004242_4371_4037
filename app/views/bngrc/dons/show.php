<?php
$title = 'Détail don';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}
function fmtQty(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}

$don         = $don ?? [];
$allocations = $allocations ?? [];
$canEdit     = $canEdit ?? false;
$canDelete   = $canDelete ?? false;
$quantite    = (float)($don['quantite'] ?? 0);
$attribueQ   = (float)($don['attribue_quantite'] ?? 0);
$resteQ      = max(0.0, $quantite - $attribueQ);
$prix        = (float)($don['prix_unitaire'] ?? 0);
$valeur      = $quantite * $prix;
$attribueVal = $attribueQ * $prix;
$resteVal    = $resteQ * $prix;
$pctAttribue = $quantite > 0 ? round($attribueQ / $quantite * 100) : 0;

$catBadge = match($don['categorie'] ?? '') {
	'nature'   => 'bg-gradient-success',
	'materiau' => 'bg-gradient-info',
	'argent'   => 'bg-gradient-warning',
	default    => 'bg-gradient-secondary',
};
$catLabel = match($don['categorie'] ?? '') {
	'nature'   => 'Nature',
	'materiau' => 'Matériau',
	'argent'   => 'Argent',
	default    => ucfirst($don['categorie'] ?? ''),
};

// Statut
if ($attribueQ <= 0) {
	$statut      = 'Non traité';
	$statutBadge = 'bg-gradient-secondary';
	$statutIcon  = 'fas fa-clock';
} elseif ($attribueQ < $quantite) {
	$statut      = 'Partiel';
	$statutBadge = 'bg-gradient-warning';
	$statutIcon  = 'fas fa-adjust';
} else {
	$statut      = 'Traité';
	$statutBadge = 'bg-gradient-success';
	$statutIcon  = 'fas fa-check-circle';
}

$progressColor = $pctAttribue >= 100 ? 'bg-success' : ($pctAttribue >= 50 ? 'bg-info' : ($pctAttribue > 0 ? 'bg-warning' : 'bg-secondary'));
?>

<!-- En-tête -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h5 class="text-white mb-1"><i class="ni ni-basket me-2"></i>Don #<?= (int)$don['id'] ?></h5>
		<p class="text-white text-sm opacity-8 mb-0"><?= htmlspecialchars((string)$don['libelle']) ?> — <span class="badge badge-sm <?= $statutBadge ?>"><i class="<?= $statutIcon ?> me-1"></i><?= $statut ?></span></p>
	</div>
	<a class="btn btn-sm btn-white mb-0" href="/dons"><i class="fas fa-arrow-left me-1"></i> Retour à la liste</a>
</div>

<div class="row">
	<!-- Infos don -->
	<div class="col-lg-8">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-info-circle text-success me-2"></i>Informations du don</h6>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Article</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$don['libelle']) ?> <span class="badge badge-sm <?= $catBadge ?>"><?= htmlspecialchars($catLabel) ?></span></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Date du don</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$don['date_don']) ?></p>
						</div>
						<?php if (!empty($don['source'])): ?>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Source / Donateur</span>
							<p class="text-sm mb-0"><?= htmlspecialchars((string)$don['source']) ?></p>
						</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Quantité</span>
							<p class="text-sm mb-0"><?= fmtQty($quantite) ?> <?= htmlspecialchars((string)$don['unite']) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Prix unitaire</span>
							<p class="text-sm mb-0"><?= moneyAr($prix) ?></p>
						</div>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Valeur totale</span>
							<p class="text-sm font-weight-bold mb-0"><?= moneyAr($valeur) ?></p>
						</div>
						<?php if (!empty($don['note'])): ?>
						<div class="mb-3">
							<span class="text-xs text-uppercase text-secondary font-weight-bold">Note</span>
							<p class="text-sm mb-0"><?= nl2br(htmlspecialchars((string)$don['note'])) ?></p>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<hr class="horizontal dark">
				<div class="d-flex gap-2">
					<?php if ($canEdit): ?>
						<a class="btn btn-sm bg-gradient-dark" href="/dons/<?= (int)$don['id'] ?>/edit"><i class="fas fa-pencil-alt me-1"></i> Modifier</a>
					<?php endif; ?>
					<?php if ($canDelete): ?>
						<form method="post" action="/dons/<?= (int)$don['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
							<button class="btn btn-sm btn-outline-danger mb-0" type="submit"><i class="fas fa-trash me-1"></i> Supprimer</button>
						</form>
					<?php endif; ?>
					<?php if (!$canEdit && !$canDelete): ?>
						<span class="badge bg-gradient-secondary"><i class="fas fa-lock me-1"></i> Don dispatché — non modifiable</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Résumé distribution -->
	<div class="col-lg-4">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-chart-pie text-info me-2"></i>Distribution</h6>
			</div>
			<div class="card-body text-center">
				<div class="mb-3">
					<h2 class="font-weight-bolder mb-0"><?= $pctAttribue ?>%</h2>
					<p class="text-sm text-secondary mb-0">attribué</p>
				</div>
				<div class="progress mb-3" style="height:10px;">
					<div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= min($pctAttribue, 100) ?>%;" aria-valuenow="<?= $pctAttribue ?>" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<div class="row text-center">
					<div class="col-6">
						<span class="text-xs text-uppercase text-secondary font-weight-bold">Attribué</span>
						<h6 class="text-success mb-0"><?= fmtQty($attribueQ) ?> <?= htmlspecialchars((string)$don['unite']) ?></h6>
						<p class="text-xs text-secondary"><?= moneyAr($attribueVal) ?></p>
					</div>
					<div class="col-6">
						<span class="text-xs text-uppercase text-secondary font-weight-bold">Restant</span>
						<h6 class="text-warning mb-0"><?= fmtQty($resteQ) ?> <?= htmlspecialchars((string)$don['unite']) ?></h6>
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
			<h6 class="mb-0"><i class="fas fa-link text-primary me-2"></i>Répartition vers les besoins</h6>
			<span class="badge bg-gradient-dark"><?= count($allocations) ?> allocation(s)</span>
		</div>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<?php if (empty($allocations)): ?>
			<div class="text-center py-4">
				<i class="ni ni-bullet-list-67 text-secondary" style="font-size:2rem;"></i>
				<p class="text-sm text-secondary mt-2 mb-0">Ce don n'a pas encore été attribué.</p>
			</div>
		<?php else: ?>
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ville</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Besoin</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité attribuée</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date besoin</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dispatch run</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($allocations as $al): ?>
					<tr>
						<td>
							<div class="d-flex px-2 py-1">
								<div class="icon icon-shape icon-sm me-2 bg-gradient-dark shadow text-center rounded-circle">
									<i class="ni ni-building text-white text-xs" aria-hidden="true"></i>
								</div>
								<div class="d-flex flex-column justify-content-center">
									<h6 class="mb-0 text-sm"><?= htmlspecialchars((string)$al['ville']) ?></h6>
									<p class="text-xs text-secondary mb-0"><?= htmlspecialchars((string)$al['region']) ?></p>
								</div>
							</div>
						</td>
						<td><a class="text-sm font-weight-bold" href="/besoins/<?= (int)$al['besoin_id'] ?>">Besoin #<?= (int)$al['besoin_id'] ?></a></td>
						<td><span class="text-xs font-weight-bold"><?= htmlspecialchars((string)$al['quantite']) ?></span></td>
						<td><span class="text-xs text-secondary"><?= htmlspecialchars((string)$al['date_besoin']) ?></span></td>
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
