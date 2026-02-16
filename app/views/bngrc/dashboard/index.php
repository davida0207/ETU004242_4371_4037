<?php
$title = 'Dashboard dynamique';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$stats = $stats ?? [];
$rows = $rows ?? [];
?>

<div class="stats-grid">
	<div class="stat-card">
		<h3>Besoins Totaux</h3>
		<div class="value"><?= htmlspecialchars(moneyAr((float)($stats['besoins_total'] ?? 0))) ?></div>
		<div class="subtext">Tous les besoins recensés</div>
	</div>
	<div class="stat-card">
		<h3>Attribué</h3>
		<div class="value"><?= htmlspecialchars(moneyAr((float)($stats['attribue_total'] ?? 0))) ?></div>
		<div class="subtext">Total attribué via allocations</div>
	</div>
	<div class="stat-card">
		<h3>Taux de Couverture</h3>
		<div class="value"><?= (int)round((float)($stats['taux_couverture'] ?? 0)) ?>%</div>
		<div class="subtext">Des besoins sont couverts</div>
	</div>
	<div class="stat-card">
		<h3>Villes Assistées</h3>
		<div class="value"><?= (int)($stats['villes_assistees'] ?? 0) ?> / <?= (int)($stats['villes_total'] ?? 0) ?></div>
		<div class="subtext">Villes avec attribution</div>
	</div>
</div>

<div class="card">
	<table class="table">
		<thead>
			<tr>
				<th>Région</th>
				<th>Ville</th>
				<th>Besoins (valeur)</th>
				<th>Attribué</th>
				<th>Reste</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($rows as $r):
			$besoins = (float)($r['besoins_valeur'] ?? 0);
			$attribue = (float)($r['attribue_valeur'] ?? 0);
			$reste = max(0.0, $besoins - $attribue);
		?>
			<tr>
				<td><?= htmlspecialchars((string)$r['region']) ?></td>
				<td><?= htmlspecialchars((string)$r['ville']) ?></td>
				<td><?= htmlspecialchars(moneyAr($besoins)) ?></td>
				<td><?= htmlspecialchars(moneyAr($attribue)) ?></td>
				<td><?= htmlspecialchars(moneyAr($reste)) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';

