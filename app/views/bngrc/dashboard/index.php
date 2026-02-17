<?php
$title = 'Dashboard';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$stats  = $stats ?? [];
$rows   = $rows ?? [];
$search = $search ?? '';
$sort   = $sort ?? '';

/* Agrégation par région pour le graphique */
$regionData = [];
foreach ($rows as $r) {
	$region = $r['region'] ?? 'Inconnu';
	if (!isset($regionData[$region])) {
		$regionData[$region] = ['besoins' => 0.0, 'attribue' => 0.0];
	}
	$regionData[$region]['besoins']  += (float)($r['besoins_valeur'] ?? 0);
	$regionData[$region]['attribue'] += (float)($r['attribue_valeur'] ?? 0);
}
$chartLabels   = array_values(array_keys($regionData));
$chartBesoins  = array_values(array_column($regionData, 'besoins'));
$chartAttribue = array_values(array_column($regionData, 'attribue'));

/* JSON pré-encodé pour le script Chart.js (utilisé dans le heredoc en bas) */
$rawLabels   = json_encode($chartLabels, JSON_UNESCAPED_UNICODE);
$rawBesoins  = json_encode($chartBesoins);
$rawAttribue = json_encode($chartAttribue);
?>

<!-- ============ 4 CARDS STATISTIQUES ============ -->
<div class="row">
	<!-- Card 1 : Besoins totaux (valeur + quantité) -->
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins Totaux</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)($stats['besoins_total'] ?? 0))) ?></h5>
							<p class="mb-0">
								<span class="text-primary text-sm font-weight-bolder"><?= number_format((float)($stats['besoins_quantite'] ?? 0), 0, ',', ' ') ?></span>
								unités demandées
							</p>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
							<i class="bi bi-cash-coin text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Card 2 : Dons reçus (total) -->
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Dons Reçus</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)($stats['dons_total_valeur'] ?? 0))) ?></h5>
							<p class="mb-0">
								<span class="text-info text-sm font-weight-bolder"><?= (int)($stats['dons_total_count'] ?? 0) ?></span>
								don(s) enregistré(s)
							</p>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
							<i class="bi bi-basket3 text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Card 3 : Dons attribués -->
	<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Dons Attribués</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)($stats['dons_attribue_valeur'] ?? 0))) ?></h5>
							<p class="mb-0">
								<?php
									$donsTot = (float)($stats['dons_total_valeur'] ?? 0);
									$donsAtt = (float)($stats['dons_attribue_valeur'] ?? 0);
									$donsTaux = $donsTot > 0 ? round(($donsAtt / $donsTot) * 100) : 0;
								?>
								<span class="text-success text-sm font-weight-bolder"><?= (int)$donsTaux ?>%</span>
								des dons distribués
							</p>
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

	<!-- Card 4 : Villes assistées -->
	<div class="col-xl-3 col-sm-6">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Villes Assistées</p>
							<h5 class="font-weight-bolder"><?= (int)($stats['villes_assistees'] ?? 0) ?> / <?= (int)($stats['villes_total'] ?? 0) ?></h5>
							<p class="mb-0">
								<span class="text-warning text-sm font-weight-bolder"><?= count($chartLabels) ?></span> régions couvertes
							</p>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
							<i class="bi bi-geo-alt-fill text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ============ GRAPHIQUE + RÉPARTITION ============ -->
<div class="row mt-4">
	<div class="col-lg-7 mb-lg-0 mb-4">
		<div class="card z-index-2 h-100">
			<div class="card-header pb-0 pt-3 bg-transparent">
				<h6 class="text-capitalize">Besoins vs Attribué par région</h6>
				<p class="text-sm mb-0">
					<i class="fa fa-arrow-up text-success"></i>
					<span class="font-weight-bold"><?= (int)round((float)($stats['taux_couverture'] ?? 0)) ?>%</span> de couverture globale
				</p>
			</div>
			<div class="card-body p-3">
				<div class="chart" style="position:relative; height:300px;">
					<canvas id="chart-besoins" class="chart-canvas" height="300"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<div class="card h-100">
			<div class="card-header pb-0 p-3">
				<h6 class="mb-0">Répartition par région</h6>
			</div>
			<div class="card-body p-3">
				<ul class="list-group">
					<?php foreach ($regionData as $regNom => $regVals): 
						$regTaux = $regVals['besoins'] > 0 ? round(($regVals['attribue'] / $regVals['besoins']) * 100) : 0;
					?>
					<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
						<div class="d-flex align-items-center">
							<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
								<i class="bi bi-map text-white opacity-10"></i>
							</div>
							<div class="d-flex flex-column">
								<h6 class="mb-1 text-dark text-sm"><?= htmlspecialchars($regNom) ?></h6>
								<span class="text-xs"><?= htmlspecialchars(moneyAr($regVals['besoins'])) ?> besoins</span>
							</div>
						</div>
						<div class="d-flex align-items-center text-sm font-weight-bold <?= $regTaux >= 50 ? 'text-success' : 'text-danger' ?>">
							<?= (int)$regTaux ?>%
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- ============ RECHERCHE + TRI + TABLEAU DES VILLES ============ -->
<div class="row mt-4">
	<div class="col-12">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<div class="d-flex flex-wrap justify-content-between align-items-center">
					<div>
						<h6>Tableau des villes</h6>
						<p class="text-sm mb-0">Résumé des besoins et attributions par ville</p>
					</div>
				</div>
				<!-- Barre de recherche + Tri -->
				<form method="get" action="/bngrc/dashboard" class="mt-3">
					<div class="row align-items-end g-2">
						<div class="col-md-4 col-sm-6">
							<div class="input-group">
								<span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
								<input type="text" name="q" class="form-control form-control-sm" placeholder="Rechercher une ville ou région…" value="<?= htmlspecialchars($search) ?>">
							</div>
						</div>
						<div class="col-md-3 col-sm-4">
							<select name="sort" class="form-select form-select-sm">
								<option value="">-- Trier par --</option>
								<option value="alpha" <?= $sort === 'alpha' ? 'selected' : '' ?>>Nom (A-Z)</option>
								<option value="besoins" <?= $sort === 'besoins' ? 'selected' : '' ?>>Besoins ↓</option>
								<option value="reste" <?= $sort === 'reste' ? 'selected' : '' ?>>Reste ↓</option>
							</select>
						</div>
						<div class="col-auto">
							<button type="submit" class="btn btn-sm btn-outline-primary mb-0">
								<i class="fas fa-filter me-1"></i> Filtrer
							</button>
						</div>
						<?php if ($search !== '' || $sort !== ''): ?>
						<div class="col-auto">
							<a href="/bngrc/dashboard" class="btn btn-sm btn-outline-secondary mb-0">Réinitialiser</a>
						</div>
						<?php endif; ?>
					</div>
				</form>
			</div>
			<div class="card-body px-0 pt-0 pb-2 mt-3">
				<div class="table-responsive p-0">
					<table class="table align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ville</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Région</th>
								<th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Besoins</th>
								<th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Attribué</th>
								<th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reste</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php if (empty($rows)): ?>
							<tr><td colspan="7" class="text-center text-sm py-4 text-secondary">Aucune ville trouvée.</td></tr>
						<?php else: ?>
						<?php foreach ($rows as $r):
							$besoins  = (float)($r['besoins_valeur'] ?? 0);
							$attribue = (float)($r['attribue_valeur'] ?? 0);
							$reste    = max(0.0, $besoins - $attribue);
							$taux     = $besoins > 0 ? ($attribue / $besoins) * 100 : 0;

							if ($taux >= 80) {
								$badgeClass = 'bg-gradient-success';
								$statusText = 'Complet';
							} elseif ($taux >= 50) {
								$badgeClass = 'bg-gradient-warning';
								$statusText = 'Partiel';
							} else {
								$badgeClass = 'bg-gradient-danger';
								$statusText = 'Insuffisant';
							}
						?>
							<tr>
								<td>
									<div class="d-flex px-2 py-1">
										<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center border-radius-md">
											<i class="bi bi-building text-white opacity-10"></i>
										</div>
										<div class="d-flex flex-column justify-content-center">
											<h6 class="mb-0 text-sm"><?= htmlspecialchars((string)$r['ville']) ?></h6>
										</div>
									</div>
								</td>
								<td>
									<p class="text-sm font-weight-bold mb-0"><?= htmlspecialchars((string)$r['region']) ?></p>
								</td>
								<td class="text-end">
									<span class="text-xs font-weight-bold" style="font-family:monospace;"><?= htmlspecialchars(moneyAr($besoins)) ?></span>
								</td>
								<td class="text-end">
									<span class="text-xs font-weight-bold" style="font-family:monospace;"><?= htmlspecialchars(moneyAr($attribue)) ?></span>
								</td>
								<td class="text-end">
									<span class="text-xs font-weight-bold" style="font-family:monospace;"><?= htmlspecialchars(moneyAr($reste)) ?></span>
								</td>
								<td class="align-middle text-center text-sm">
									<span class="badge badge-sm <?= $badgeClass ?>"><?= $statusText ?></span>
								</td>
								<td class="align-middle text-center">
									<a href="/villes/<?= (int)$r['ville_id'] ?>/dashboard" class="btn btn-sm bg-gradient-primary mb-0" title="Dashboard de cette ville">
										<i class="fas fa-chart-line me-1"></i> Dashboard
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ============ CHART.JS ============ -->

<?php
$content = ob_get_clean();

/* Script du graphique — sera injecté APRÈS le chargement de Chart.js via $pageScripts dans _layout.php */
$nonce = \Flight::get('csp_nonce');
$pageScripts = <<<SCRIPT
<script nonce="{$nonce}">
(function() {
	var el = document.getElementById("chart-besoins");
	if (!el) { console.error('Canvas chart-besoins introuvable'); return; }
	if (typeof Chart === 'undefined') { console.error('Chart.js non chargé'); return; }
	var ctx = el.getContext("2d");

	new Chart(ctx, {
		type: "bar",
		data: {
			labels: {$rawLabels},
			datasets: [
				{
					label: "Besoins (Ar)",
					backgroundColor: "#5e72e4",
					borderColor: "#5e72e4",
					borderWidth: 1,
					borderRadius: 4,
					data: {$rawBesoins},
					maxBarThickness: 30
				},
				{
					label: "Attribué (Ar)",
					backgroundColor: "#2dce89",
					borderColor: "#2dce89",
					borderWidth: 1,
					borderRadius: 4,
					data: {$rawAttribue},
					maxBarThickness: 30
				}
			]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: { display: true, position: 'top', labels: { color: '#344767', font: { size: 12 } } }
			},
			interaction: { intersect: false, mode: 'index' },
			scales: {
				y: {
					beginAtZero: true,
					grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: '#e9ecef' },
					ticks: {
						display: true, padding: 10, color: '#b2b9bf',
						font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
						callback: function(v) {
							if (v >= 1000000) return (v/1000000).toFixed(1) + 'M Ar';
							if (v >= 1000) return (v/1000).toFixed(0) + 'k Ar';
							return v + ' Ar';
						}
					}
				},
				x: {
					grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
					ticks: {
						display: true, color: '#b2b9bf', padding: 10,
						font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 }
					}
				}
			}
		}
	});
})();
</script>
SCRIPT;

include __DIR__ . '/../_layout.php';