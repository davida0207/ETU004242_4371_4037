<?php
$title = 'Récapitulatif des besoins';
ob_start();

function moneyArRecap(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$stats = $stats ?? ['besoins_total' => 0, 'attribue_total' => 0, 'reste_total' => 0];
?>

<!-- Cartes récapitulatives -->
<div class="row mb-4">
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins totaux</p>
							<h5 class="font-weight-bolder" id="recap-besoins-total"><?= htmlspecialchars(moneyArRecap((float)$stats['besoins_total'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
							<i class="bi bi-clipboard-check text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins satisfaits</p>
							<h5 class="font-weight-bolder" id="recap-attribue-total"><?= htmlspecialchars(moneyArRecap((float)$stats['attribue_total'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
							<i class="bi bi-check-circle text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Besoins restants</p>
							<h5 class="font-weight-bolder" id="recap-reste-total"><?= htmlspecialchars(moneyArRecap((float)$stats['reste_total'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
							<i class="bi bi-exclamation-triangle text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Détail + bouton Ajax -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0 d-flex justify-content-between align-items-center">
				<h6>Récapitulatif global</h6>
				<button type="button" class="btn btn-sm btn-outline-primary mb-0" id="btn-recap-refresh">
					<i class="bi bi-arrow-clockwise me-1"></i> Actualiser
				</button>
			</div>
			<div class="card-body">
				<p class="text-sm text-muted">Besoins totaux, satisfaits et besoins restants (en valeur).</p>
				<div class="table-responsive">
					<table class="table align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Indicateur</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Valeur</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-sm"><i class="bi bi-clipboard-check text-danger me-2"></i>Besoins totaux</td>
								<td class="text-sm text-end font-weight-bold" id="recap-besoins-total-row"><?= htmlspecialchars(moneyArRecap((float)$stats['besoins_total'])) ?></td>
							</tr>
							<tr>
								<td class="text-sm"><i class="bi bi-check-circle text-success me-2"></i>Besoins satisfaits</td>
								<td class="text-sm text-end font-weight-bold" id="recap-attribue-total-row"><?= htmlspecialchars(moneyArRecap((float)$stats['attribue_total'])) ?></td>
							</tr>
							<tr>
								<td class="text-sm"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Besoins restants</td>
								<td class="text-sm text-end font-weight-bold" id="recap-reste-total-row"><?= htmlspecialchars(moneyArRecap((float)$stats['reste_total'])) ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $nonce = \Flight::get('csp_nonce'); ?>
<script nonce="<?= htmlspecialchars((string)$nonce) ?>">
document.addEventListener('DOMContentLoaded', function() {
	const btn = document.getElementById('btn-recap-refresh');
	if (!btn) return;
	btn.addEventListener('click', function() {
		btn.disabled = true;
		fetch('/recap/data')
			.then(resp => resp.json())
			.then(data => {
				const fmt = v => new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(v) + ' Ar';
				// Cartes
				document.getElementById('recap-besoins-total').textContent = fmt(data.besoins_total || 0);
				document.getElementById('recap-attribue-total').textContent = fmt(data.attribue_total || 0);
				document.getElementById('recap-reste-total').textContent = fmt(data.reste_total || 0);
				// Tableau
				document.getElementById('recap-besoins-total-row').textContent = fmt(data.besoins_total || 0);
				document.getElementById('recap-attribue-total-row').textContent = fmt(data.attribue_total || 0);
				document.getElementById('recap-reste-total-row').textContent = fmt(data.reste_total || 0);
			})
			.finally(() => {
				btn.disabled = false;
			});
	});
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
