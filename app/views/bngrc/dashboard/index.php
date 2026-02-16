<?php
$title = 'Dashboard dynamique';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$stats = $stats ?? [];
$rows = $rows ?? [];
?>

<div class="dashboard-container">
	<div class="page-header">
		<h2 class="page-title">Tableau de bord</h2>
		<p class="page-description">Vue d'ensemble des collectes et distributions en temps réel</p>
	</div>

	<div class="stats-grid">
		<div class="stat-card stat-card--primary">
			<div class="stat-card__icon">
				<i class="bi bi-clipboard-data"></i>
			</div>
			<div class="stat-card__content">
				<div class="stat-card__label">Besoins Totaux</div>
				<div class="stat-card__value"><?= htmlspecialchars(moneyAr((float)($stats['besoins_total'] ?? 0))) ?></div>
				<div class="stat-card__subtext">Tous les besoins recensés</div>
			</div>
		</div>

		<div class="stat-card stat-card--success">
			<div class="stat-card__icon">
				<i class="bi bi-check-circle"></i>
			</div>
			<div class="stat-card__content">
				<div class="stat-card__label">Attribué</div>
				<div class="stat-card__value"><?= htmlspecialchars(moneyAr((float)($stats['attribue_total'] ?? 0))) ?></div>
				<div class="stat-card__subtext">Total attribué via allocations</div>
			</div>
		</div>

		<div class="stat-card stat-card--info">
			<div class="stat-card__icon">
				<i class="bi bi-graph-up-arrow"></i>
			</div>
			<div class="stat-card__content">
				<div class="stat-card__label">Taux de Couverture</div>
				<div class="stat-card__value"><?= (int)round((float)($stats['taux_couverture'] ?? 0)) ?>%</div>
				<div class="stat-card__subtext">Des besoins sont couverts</div>
			</div>
		</div>

		<div class="stat-card stat-card--warning">
			<div class="stat-card__icon">
				<i class="bi bi-geo-alt"></i>
			</div>
			<div class="stat-card__content">
				<div class="stat-card__label">Villes Assistées</div>
				<div class="stat-card__value"><?= (int)($stats['villes_assistees'] ?? 0) ?> / <?= (int)($stats['villes_total'] ?? 0) ?></div>
				<div class="stat-card__subtext">Villes avec attribution</div>
			</div>
		</div>
	</div>

	<div class="data-section">
		<div class="section-header">
			<h3 class="section-title">Détails par région et ville</h3>
			<div class="section-actions">
				<button class="btn btn--secondary btn--sm">
					<i class="bi bi-download"></i>
					<span>Exporter</span>
				</button>
			</div>
		</div>

		<div class="table-container">
			<table class="data-table">
				<thead>
					<tr>
						<th>Région</th>
						<th>Ville</th>
						<th class="text-right">Besoins</th>
						<th class="text-right">Attribué</th>
						<th class="text-right">Reste</th>
						<th class="text-center">Statut</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($rows as $r):
					$besoins = (float)($r['besoins_valeur'] ?? 0);
					$attribue = (float)($r['attribue_valeur'] ?? 0);
					$reste = max(0.0, $besoins - $attribue);
					$taux = $besoins > 0 ? ($attribue / $besoins) * 100 : 0;
					
					if ($taux >= 80) {
						$statusClass = 'status-badge--success';
						$statusText = 'Complet';
					} elseif ($taux >= 50) {
						$statusClass = 'status-badge--warning';
						$statusText = 'Partiel';
					} else {
						$statusClass = 'status-badge--danger';
						$statusText = 'Insuffisant';
					}
				?>
					<tr>
						<td>
							<div class="cell-content">
								<i class="bi bi-map"></i>
								<span><?= htmlspecialchars((string)$r['region']) ?></span>
							</div>
						</td>
						<td>
							<div class="cell-content">
								<i class="bi bi-building"></i>
								<span><?= htmlspecialchars((string)$r['ville']) ?></span>
							</div>
						</td>
						<td class="text-right font-mono"><?= htmlspecialchars(moneyAr($besoins)) ?></td>
						<td class="text-right font-mono"><?= htmlspecialchars(moneyAr($attribue)) ?></td>
						<td class="text-right font-mono"><?= htmlspecialchars(moneyAr($reste)) ?></td>
						<td class="text-center">
							<span class="status-badge <?= $statusClass ?>">
								<?= $statusText ?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';