<?php
$title = 'Dons';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}
function fmtQty(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}

$nonce      = \Flight::get('csp_nonce');
$flash      = $flash ?? '';
$flashMap   = [
	'created' => ['success', 'Don enregistré avec succès.', 'ni ni-check-bold'],
	'deleted' => ['success', 'Don supprimé.', 'ni ni-check-bold'],
	'blocked' => ['warning', 'Modification/suppression impossible : des allocations existent pour ce don.', 'ni ni-bell-55'],
];
$filters    = $filters ?? [];
$rows       = $rows ?? [];
$articles   = $articles ?? [];
$categories = $categories ?? [];

/* Mini-stats */
$totalDons     = 0;
$totalAttribue = 0;
foreach ($rows as $d) {
	$totalDons     += (float)$d['quantite'] * (float)$d['prix_unitaire'];
	$totalAttribue += (float)$d['attribue_quantite'] * (float)$d['prix_unitaire'];
}
$totalReste = $totalDons - $totalAttribue;
?>

<!-- Flash -->
<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-<?= $flashMap[$flash][0] ?> alert-dismissible fade show text-white" role="alert">
		<span class="alert-icon"><i class="<?= $flashMap[$flash][2] ?>"></i></span>
		<span class="alert-text"><strong><?= $flashMap[$flash][0] === 'warning' ? 'Attention !' : 'Succès !' ?></strong> <?= htmlspecialchars($flashMap[$flash][1]) ?></span>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<?php endif; ?>

<!-- Cartes stats -->
<div class="row mb-4">
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Dons reçus</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalDons) ?></h5>
							<p class="mb-0"><span class="text-success text-sm font-weight-bolder"><?= count($rows) ?></span> <span class="text-sm">don(s)</span></p>
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
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Attribué</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalAttribue) ?></h5>
							<p class="mb-0"><span class="text-info text-sm font-weight-bolder"><?= $totalDons > 0 ? round($totalAttribue / $totalDons * 100) : 0 ?>%</span> <span class="text-sm">distribué</span></p>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
							<i class="ni ni-send text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Disponible</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalReste) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
							<i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Filtres -->
<div class="card mb-4">
	<div class="card-header pb-0">
		<div class="d-flex justify-content-between align-items-center">
			<h6 class="mb-0"><i class="ni ni-zoom-split-in text-sm me-1"></i> Filtres</h6>
			<a class="btn btn-sm bg-gradient-success mb-0" href="/dons/add"><i class="fas fa-plus me-1"></i> Ajouter un don</a>
		</div>
	</div>
	<div class="card-body pt-2">
		<form method="get" action="/dons">
			<div class="row g-2 align-items-end">
				<div class="col-lg-3 col-md-4">
					<label class="form-control-label text-xs">Catégorie</label>
					<select name="categorie" class="form-select form-select-sm">
						<?php foreach ($categories as $key => $label): ?>
							<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>><?= htmlspecialchars((string)$label) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-4">
					<label class="form-control-label text-xs">Article</label>
					<select name="article_id" class="form-select form-select-sm">
						<option value="">Tous</option>
						<?php foreach ($articles as $a): ?>
							<option value="<?= (int)$a['id'] ?>" <?= ((string)($filters['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>><?= htmlspecialchars((string)$a['libelle']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-3 col-md-4">
					<label class="form-control-label text-xs">Date début</label>
					<input class="form-control form-control-sm" type="date" name="start_date" value="<?= htmlspecialchars((string)($filters['start_date'] ?? '')) ?>">
				</div>
				<div class="col-lg-3 col-md-4">
					<label class="form-control-label text-xs">Date fin</label>
					<input class="form-control form-control-sm" type="date" name="end_date" value="<?= htmlspecialchars((string)($filters['end_date'] ?? '')) ?>">
				</div>
			</div>
			<div class="mt-3 d-flex gap-2">
				<button class="btn btn-sm btn-outline-primary mb-0" type="submit"><i class="fas fa-filter me-1"></i> Filtrer</button>
				<a class="btn btn-sm btn-outline-secondary mb-0" href="/dons"><i class="fas fa-times me-1"></i> Réinitialiser</a>
			</div>
		</form>
	</div>
</div>

<!-- Tableau -->
<div class="card mb-4">
	<div class="card-header pb-0">
		<div class="d-flex justify-content-between align-items-center">
			<h6 class="mb-0">Liste des dons</h6>
			<span class="badge bg-gradient-dark"><?= count($rows) ?> résultat(s)</span>
		</div>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<?php if (empty($rows)): ?>
			<div class="text-center py-5">
				<i class="ni ni-basket text-secondary" style="font-size:3rem;"></i>
				<p class="text-sm text-secondary mt-3">Aucun don trouvé.</p>
				<a href="/dons/add" class="btn btn-sm bg-gradient-success">Enregistrer un premier don</a>
			</div>
		<?php else: ?>
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Article</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Catégorie</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date don</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Qté restante</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Statut</th>
						<th class="text-secondary opacity-7"></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($rows as $d):
					$quantite   = (float)($d['quantite'] ?? 0);
					$attribueQ  = (float)($d['attribue_quantite'] ?? 0);
					$resteQ     = max(0.0, $quantite - $attribueQ);
					$prix       = (float)($d['prix_unitaire'] ?? 0);
					$hasAlloc   = $attribueQ > 0;

					// Statut : non traité / partiel / traité
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

					$catBadge = match($d['categorie'] ?? '') {
						'nature'   => 'bg-gradient-success',
						'materiau' => 'bg-gradient-info',
						'argent'   => 'bg-gradient-warning',
						default    => 'bg-gradient-secondary',
					};
					$catLabel = match($d['categorie'] ?? '') {
						'nature'   => 'Nature',
						'materiau' => 'Matériau',
						'argent'   => 'Argent',
						default    => ucfirst($d['categorie'] ?? ''),
					};
				?>
					<tr id="d<?= (int)$d['id'] ?>">
						<td>
							<div class="d-flex px-2 py-1">
								<div class="icon icon-shape icon-sm me-2 bg-gradient-success shadow text-center rounded-circle">
									<i class="ni ni-basket text-white text-xs" aria-hidden="true"></i>
								</div>
								<div class="d-flex flex-column justify-content-center">
									<h6 class="mb-0 text-sm"><?= htmlspecialchars((string)$d['libelle']) ?></h6>
									<?php if (!empty($d['source'])): ?>
										<p class="text-xs text-secondary mb-0"><?= htmlspecialchars((string)$d['source']) ?></p>
									<?php endif; ?>
								</div>
							</div>
						</td>
						<td><span class="badge badge-sm <?= $catBadge ?>"><?= htmlspecialchars($catLabel) ?></span></td>
						<td><span class="text-xs font-weight-bold"><?= fmtQty($quantite) ?></span> <span class="text-xs text-secondary"><?= htmlspecialchars((string)$d['unite']) ?></span></td>
						<td><span class="text-xs text-secondary"><?= htmlspecialchars((string)$d['date_don']) ?></span></td>
						<td><span class="text-xs font-weight-bold"><?= fmtQty($resteQ) ?></span> <span class="text-xs text-secondary"><?= htmlspecialchars((string)$d['unite']) ?></span></td>
						<td>
							<span class="badge badge-sm <?= $statutBadge ?>">
								<i class="<?= $statutIcon ?> me-1"></i><?= $statut ?>
							</span>
						</td>
						<td class="align-middle">
							<a href="/dons/<?= (int)$d['id'] ?>" class="btn btn-link text-info px-2 mb-0" data-bs-toggle="tooltip" title="Voir détail"><i class="fas fa-eye text-info"></i></a>
							<?php if (!$hasAlloc): ?>
								<a href="/dons/<?= (int)$d['id'] ?>/edit" class="btn btn-link text-dark px-2 mb-0" data-bs-toggle="tooltip" title="Modifier"><i class="fas fa-pencil-alt text-dark"></i></a>
								<form method="post" action="/dons/<?= (int)$d['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
									<button type="submit" class="btn btn-link text-danger px-2 mb-0" data-bs-toggle="tooltip" title="Supprimer"><i class="fas fa-trash text-danger"></i></button>
								</form>
							<?php else: ?>
								<span class="btn btn-link text-muted px-2 mb-0" data-bs-toggle="tooltip" title="Don dispatché — non modifiable"><i class="fas fa-lock text-muted"></i></span>
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
