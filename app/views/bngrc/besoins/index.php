<?php
$title = 'Besoins';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}
function fmtQty(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}

$nonce     = \Flight::get('csp_nonce');
$flash     = $flash ?? '';
$flashMap  = [
	'created' => ['success', 'Besoin enregistré avec succès.', 'bi bi-check-circle-fill'],
	'deleted' => ['success', 'Besoin supprimé.', 'bi bi-check-circle-fill'],
	'blocked' => ['warning', 'Suppression impossible : des allocations existent pour ce besoin.', 'bi bi-bell-fill'],
];
$filters    = $filters ?? [];
$rows       = $rows ?? [];
$regions    = $regions ?? [];
$villes     = $villes ?? [];
$articles   = $articles ?? [];
$categories = $categories ?? [];

/* Mini-stats */
$totalBesoins = 0;
$totalCouvert = 0;
foreach ($rows as $b) {
	$totalBesoins += (float)$b['quantite'] * (float)$b['prix_unitaire'];
	$totalCouvert += (float)$b['attribue_quantite'] * (float)$b['prix_unitaire'];
}
$totalReste = $totalBesoins - $totalCouvert;
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins totaux</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalBesoins) ?></h5>
							<p class="mb-0"><span class="text-success text-sm font-weight-bolder"><?= count($rows) ?></span> <span class="text-sm">besoin(s)</span></p>
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
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Couvert</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalCouvert) ?></h5>
							<p class="mb-0"><span class="text-info text-sm font-weight-bolder"><?= $totalBesoins > 0 ? round($totalCouvert / $totalBesoins * 100) : 0 ?>%</span> <span class="text-sm">couverture</span></p>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
							<i class="bi bi-check-circle-fill text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Reste à couvrir</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyAr($totalReste) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
							<i class="bi bi-x-circle-fill text-lg opacity-10" aria-hidden="true"></i>
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
			<h6 class="mb-0"><i class="bi bi-funnel text-sm me-1"></i> Filtres</h6>
			<a class="btn btn-sm bg-gradient-primary mb-0" href="/besoins/add"><i class="fas fa-plus me-1"></i> Ajouter un besoin</a>
		</div>
	</div>
	<div class="card-body pt-2">
		<form method="get" action="/besoins">
			<div class="row g-2 align-items-end">
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Région</label>
					<select name="region_id" class="form-select form-select-sm">
						<option value="">Toutes</option>
						<?php foreach ($regions as $r): ?>
							<option value="<?= (int)$r['id'] ?>" <?= ((string)($filters['region_id'] ?? '') === (string)$r['id']) ? 'selected' : '' ?>><?= htmlspecialchars((string)$r['nom']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Ville</label>
					<select name="ville_id" class="form-select form-select-sm">
						<option value="">Toutes</option>
						<?php foreach ($villes as $v): ?>
							<option value="<?= (int)$v['id'] ?>" <?= ((string)($filters['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>><?= htmlspecialchars((string)$v['nom']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Catégorie</label>
					<select name="categorie" class="form-select form-select-sm">
						<?php foreach ($categories as $key => $label): ?>
							<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>><?= htmlspecialchars((string)$label) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Article</label>
					<select name="article_id" class="form-select form-select-sm">
						<option value="">Tous</option>
						<?php foreach ($articles as $a): ?>
							<option value="<?= (int)$a['id'] ?>" <?= ((string)($filters['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>><?= htmlspecialchars((string)$a['libelle']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Date début</label>
					<input class="form-control form-control-sm" type="date" name="start_date" value="<?= htmlspecialchars((string)($filters['start_date'] ?? '')) ?>">
				</div>
				<div class="col-lg-2 col-md-4">
					<label class="form-control-label text-xs">Date fin</label>
					<input class="form-control form-control-sm" type="date" name="end_date" value="<?= htmlspecialchars((string)($filters['end_date'] ?? '')) ?>">
				</div>
			</div>
			<div class="mt-3 d-flex gap-2">
				<button class="btn btn-sm btn-outline-primary mb-0" type="submit"><i class="fas fa-filter me-1"></i> Filtrer</button>
				<a class="btn btn-sm btn-outline-secondary mb-0" href="/besoins"><i class="fas fa-times me-1"></i> Réinitialiser</a>
			</div>
		</form>
	</div>
</div>

<!-- Tableau -->
<div class="card mb-4">
	<div class="card-header pb-0">
		<div class="d-flex justify-content-between align-items-center">
			<h6 class="mb-0">Liste des besoins</h6>
			<span class="badge bg-gradient-dark"><?= count($rows) ?> résultat(s)</span>
		</div>
	</div>
	<div class="card-body px-0 pt-0 pb-2">
		<?php if (empty($rows)): ?>
			<div class="text-center py-5">
				<i class="bi bi-list-ul text-secondary" style="font-size:3rem;"></i>
				<p class="text-sm text-secondary mt-3">Aucun besoin trouvé.</p>
				<a href="/besoins/add" class="btn btn-sm bg-gradient-primary">Créer un premier besoin</a>
			</div>
		<?php else: ?>
		<div class="table-responsive p-0">
			<table class="table align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ville</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Article</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Catégorie</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Prix unit.</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Valeur totale</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Couvert</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Reste</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
						<th class="text-secondary opacity-7"></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($rows as $b):
					$quantite   = (float)($b['quantite'] ?? 0);
					$attribueQ  = (float)($b['attribue_quantite'] ?? 0);
					$resteQ     = max(0.0, $quantite - $attribueQ);
					$prix       = (float)($b['prix_unitaire'] ?? 0);
					$valeur     = $quantite * $prix;
					$couvertVal = $attribueQ * $prix;
					$resteVal   = $resteQ * $prix;
					$pctCouvert = $quantite > 0 ? round($attribueQ / $quantite * 100) : 0;

					$catBadge = match($b['categorie'] ?? '') {
						'nature'   => 'bg-gradient-success',
						'materiau' => 'bg-gradient-info',
						'argent'   => 'bg-gradient-warning',
						default    => 'bg-gradient-secondary',
					};
					$catLabel = match($b['categorie'] ?? '') {
						'nature'   => 'Nature',
						'materiau' => 'Matériau',
						'argent'   => 'Argent',
						default    => ucfirst($b['categorie'] ?? ''),
					};

					$progressColor = $pctCouvert >= 100 ? 'bg-success' : ($pctCouvert >= 50 ? 'bg-info' : ($pctCouvert > 0 ? 'bg-warning' : 'bg-secondary'));
				?>
					<tr id="b<?= (int)$b['id'] ?>">
						<td>
							<div class="d-flex px-2 py-1">
								<div class="icon icon-shape icon-sm me-2 bg-gradient-dark shadow text-center rounded-circle">
									<i class="bi bi-building text-white text-xs" aria-hidden="true"></i>
								</div>
								<div class="d-flex flex-column justify-content-center">
									<h6 class="mb-0 text-sm"><?= htmlspecialchars((string)$b['ville']) ?></h6>
									<p class="text-xs text-secondary mb-0"><?= htmlspecialchars((string)$b['region']) ?></p>
								</div>
							</div>
						</td>
						<td><span class="text-xs font-weight-bold"><?= htmlspecialchars((string)$b['libelle']) ?></span></td>
						<td><span class="badge badge-sm <?= $catBadge ?>"><?= htmlspecialchars($catLabel) ?></span></td>
						<td><span class="text-xs font-weight-bold"><?= fmtQty($quantite) ?></span> <span class="text-xs text-secondary"><?= htmlspecialchars((string)$b['unite']) ?></span></td>
						<td><span class="text-xs"><?= moneyAr($prix) ?></span></td>
						<td><span class="text-xs font-weight-bold"><?= moneyAr($valeur) ?></span></td>
						<td>
							<div class="d-flex align-items-center">
								<span class="text-xs font-weight-bold me-2"><?= $pctCouvert ?>%</span>
								<div>
									<div class="progress" style="width:60px; height:6px;">
										<div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= min($pctCouvert, 100) ?>%;" aria-valuenow="<?= $pctCouvert ?>" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
								</div>
							</div>
						</td>
						<td><span class="text-xs font-weight-bold text-danger"><?= moneyAr($resteVal) ?></span></td>
						<td><span class="text-xs text-secondary"><?= htmlspecialchars((string)$b['date_besoin']) ?></span></td>
						<td class="align-middle">
							<a href="/besoins/<?= (int)$b['id'] ?>" class="btn btn-link text-info px-2 mb-0" data-bs-toggle="tooltip" title="Voir détail"><i class="fas fa-eye text-info"></i></a>
							<a href="/besoins/<?= (int)$b['id'] ?>/edit" class="btn btn-link text-dark px-2 mb-0" data-bs-toggle="tooltip" title="Modifier"><i class="fas fa-pencil-alt text-dark"></i></a>
							<form method="post" action="/besoins/<?= (int)$b['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
								<button type="submit" class="btn btn-link text-danger px-2 mb-0" data-bs-toggle="tooltip" title="Supprimer"><i class="fas fa-trash text-danger"></i></button>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
