<?php
$title = 'Dashboard — ' . htmlspecialchars($ville['nom'] ?? '?');
ob_start();

$nonce       = \Flight::get('csp_nonce');
$ville       = (array)($ville ?? []);
$region      = (array)($region ?? []);
$besoins     = (array)($besoins ?? []);
$allocations = (array)($allocations ?? []);
$summary     = (array)($summary ?? []);

function fmtQtyVd(float $v): string {
	return ($v == (int)$v) ? number_format($v, 0, ',', ' ') : number_format($v, 2, ',', ' ');
}
function moneyArVd(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

/* Calculs */
$totalBesoin   = 0;
$totalAttribue = 0;
$nbArticles    = 0;
$articlesVus   = [];
foreach ($besoins as $b) {
	$totalBesoin   += (float)$b['quantite'] * (float)$b['prix_unitaire'];
	$totalAttribue += (float)$b['attribue_quantite'] * (float)$b['prix_unitaire'];
	if (!in_array($b['article_id'], $articlesVus)) {
		$articlesVus[] = $b['article_id'];
	}
}
$nbArticles     = count($articlesVus);
$totalReste     = $totalBesoin - $totalAttribue;
$pctCouverture  = $totalBesoin > 0 ? round($totalAttribue / $totalBesoin * 100) : 0;

/* Badge couleur */
$pctClass = $pctCouverture >= 75 ? 'bg-gradient-success' : ($pctCouverture >= 40 ? 'bg-gradient-warning' : 'bg-gradient-danger');
$barColor = $pctCouverture >= 75 ? 'bg-success' : ($pctCouverture >= 40 ? 'bg-warning' : 'bg-danger');
?>

<!-- Header -->
<div class="row mb-4">
	<div class="col-12 d-flex justify-content-between align-items-center">
		<a href="/villes" class="btn btn-sm btn-outline-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Retour liste</a>
	</div>
</div>

<!-- Cartes statistiques -->
<div class="row mb-4">
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins</p>
							<h5 class="font-weight-bolder mb-0"><?= count($besoins) ?></h5>
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
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Valeur besoins</p>
							<h5 class="font-weight-bolder mb-0"><?= moneyArVd($totalBesoin) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
							<i class="fas fa-money-bill-wave text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Couverture</p>
							<h5 class="font-weight-bolder mb-0"><?= $pctCouverture ?> %</h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape <?= $pctClass ?> shadow text-center rounded-circle">
							<i class="fas fa-chart-line text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Articles</p>
							<h5 class="font-weight-bolder mb-0"><?= $nbArticles ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
							<i class="bi bi-box-seam text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-4">
	<!-- Infos ville -->
	<div class="col-lg-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="bi bi-building me-2 text-warning"></i> Ville</h6>
			</div>
			<div class="card-body">
				<p class="text-sm mb-2"><strong>Nom :</strong> <?= htmlspecialchars($ville['nom']) ?></p>
				<p class="text-sm mb-2"><strong>Région :</strong> <?= htmlspecialchars($region['nom'] ?? '—') ?></p>
				<hr>
				<p class="text-sm mb-2"><strong>Couverture globale :</strong></p>
				<div class="d-flex align-items-center mb-2">
					<span class="text-sm font-weight-bold me-2"><?= $pctCouverture ?>%</span>
					<div class="progress w-100" style="height:10px;">
						<div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $pctCouverture ?>%;" aria-valuenow="<?= $pctCouverture ?>" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
				<p class="text-xs text-muted">Attribué : <?= moneyArVd($totalAttribue) ?> / <?= moneyArVd($totalBesoin) ?></p>
				<p class="text-xs text-muted mb-0">Reste : <strong class="<?= $totalReste > 0 ? 'text-danger' : 'text-success' ?>"><?= moneyArVd($totalReste) ?></strong></p>
			</div>
		</div>
	</div>

	<!-- Chart couverture par article -->
	<div class="col-lg-8">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i> Couverture par article</h6>
			</div>
			<div class="card-body">
				<canvas id="chartCouverture" height="250"></canvas>
			</div>
		</div>
	</div>
</div>

<!-- Liste des besoins -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<h6 class="mb-0"><i class="bi bi-list-ul me-2 text-danger"></i> Besoins de la ville (<?= count($besoins) ?>)</h6>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<?php if (empty($besoins)): ?>
					<p class="text-sm text-muted text-center py-4">Aucun besoin enregistré pour cette ville.</p>
				<?php else: ?>
					<div class="table-responsive p-0">
						<table class="table align-items-center mb-0">
							<thead>
								<tr>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Article</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Catégorie</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantité</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Attribué</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Couverture</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valeur totale</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
									<th class="text-secondary opacity-7"></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($besoins as $b):
									$qte   = (float)$b['quantite'];
									$att   = (float)$b['attribue_quantite'];
									$pct   = $qte > 0 ? round($att / $qte * 100) : 0;
									$bColor = $pct >= 75 ? 'bg-success' : ($pct >= 40 ? 'bg-warning' : 'bg-danger');
									$val   = $qte * (float)$b['prix_unitaire'];
								?>
								<tr>
									<td>
										<div class="d-flex px-3 py-1">
											<span class="text-xs font-weight-bold"><?= htmlspecialchars($b['libelle']) ?></span>
										</div>
									</td>
									<td>
										<span class="badge bg-gradient-secondary text-xxs"><?= htmlspecialchars($b['categorie']) ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-sm font-weight-bold"><?= fmtQtyVd($qte) ?></span>
										<span class="text-xxs text-muted"><?= htmlspecialchars($b['unite']) ?></span>
									</td>
									<td class="align-middle text-center">
										<span class="text-sm"><?= fmtQtyVd($att) ?></span>
									</td>
									<td class="align-middle text-center" style="min-width:120px;">
										<div class="d-flex align-items-center justify-content-center">
											<span class="me-2 text-xs font-weight-bold"><?= $pct ?>%</span>
											<div>
												<div class="progress" style="width:60px; height:6px;">
													<div class="progress-bar <?= $bColor ?>" role="progressbar" style="width:<?= $pct ?>%"></div>
												</div>
											</div>
										</div>
									</td>
									<td class="align-middle text-center">
										<span class="text-xs"><?= moneyArVd($val) ?></span>
									</td>
									<td>
										<span class="text-xs"><?= date('d/m/Y', strtotime($b['date_besoin'])) ?></span>
									</td>
									<td class="align-middle text-end pe-3">
										<a href="/besoins/<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary mb-0 px-2" title="Détail">
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
/* Données pour le chart */
$chartLabels = [];
$chartBesoin = [];
$chartAttrib = [];
foreach ($besoins as $b) {
	$lab = $b['libelle'];
	if (!in_array($lab, $chartLabels)) {
		$chartLabels[] = $lab;
		$chartBesoin[$lab] = 0;
		$chartAttrib[$lab] = 0;
	}
	$chartBesoin[$lab] += (float)$b['quantite'] * (float)$b['prix_unitaire'];
	$chartAttrib[$lab] += (float)$b['attribue_quantite'] * (float)$b['prix_unitaire'];
}
$jLabels  = json_encode(array_values($chartLabels));
$jBesoin  = json_encode(array_values($chartBesoin));
$jAttrib  = json_encode(array_values($chartAttrib));

ob_start();
?>
<script nonce="<?= htmlspecialchars($nonce) ?>">
document.addEventListener('DOMContentLoaded', function() {
	var ctx = document.getElementById('chartCouverture');
	if (!ctx) return;
	new Chart(ctx, {
		type: 'bar',
		data: {
			labels: <?= $jLabels ?>,
			datasets: [
				{
					label: 'Besoins (Ar)',
					data: <?= $jBesoin ?>,
					backgroundColor: 'rgba(245,54,92,0.7)',
					borderRadius: 4, barPercentage: 0.5
				},
				{
					label: 'Attribué (Ar)',
					data: <?= $jAttrib ?>,
					backgroundColor: 'rgba(45,206,137,0.7)',
					borderRadius: 4, barPercentage: 0.5
				}
			]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: { legend: { display: true, position: 'top' } },
			scales: {
				y: {
					beginAtZero: true,
					grid: { drawBorder: false, color: 'rgba(0,0,0,.05)' },
					ticks: { font: { size: 11 } }
				},
				x: {
					grid: { display: false },
					ticks: { font: { size: 11 } }
				}
			}
		}
	});
});
</script>
<?php
$pageScripts = ob_get_clean();

$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
?>
