<?php
$title = 'Achats via dons en argent';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$flash = $flash ?? '';
$flashMap = [
	'created' => ['ok', 'Achat enregistré et don créé.'],
];
$filters = $filters ?? [];
$cashInfo = $cashInfo ?? ['total_dons_argent' => 0, 'total_achats' => 0, 'cash_restant' => 0];
?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
	</div>
<?php endif; ?>

<!-- Résumé financier -->
<div class="row mb-4">
	<div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
		<div class="card">
			<div class="card-body p-3">
				<div class="row">
					<div class="col-8">
						<div class="numbers">
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Total dons en argent</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)$cashInfo['total_dons_argent'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
							<i class="bi bi-cash-stack text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Total achats</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)$cashInfo['total_achats'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
							<i class="bi bi-cart-check text-lg opacity-10" aria-hidden="true"></i>
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
							<p class="text-sm mb-0 text-uppercase font-weight-bold">Fonds restants</p>
							<h5 class="font-weight-bolder"><?= htmlspecialchars(moneyAr((float)$cashInfo['cash_restant'])) ?></h5>
						</div>
					</div>
					<div class="col-4 text-end">
						<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
							<i class="bi bi-wallet2 text-lg opacity-10" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Filtres -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card">
			<div class="card-body p-3">
				<form method="get" action="/achats" class="row g-3 align-items-end">
					<div class="col-md-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Ville</label>
						<select name="ville_id" class="form-select form-select-sm">
							<option value="">Toutes villes</option>
							<?php foreach (($villes ?? []) as $v): ?>
								<option value="<?= (int)$v['id'] ?>" <?= ((string)($filters['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars((string)$v['nom']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Date début</label>
						<input class="form-control form-control-sm" type="date" name="start_date" value="<?= htmlspecialchars((string)($filters['start_date'] ?? '')) ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Date fin</label>
						<input class="form-control form-control-sm" type="date" name="end_date" value="<?= htmlspecialchars((string)($filters['end_date'] ?? '')) ?>">
					</div>
					<div class="col-md-3 d-flex gap-2">
						<button class="btn btn-sm btn-outline-primary mb-0" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
						<a href="/achats/add" class="btn btn-sm btn-primary mb-0"><i class="bi bi-plus-lg"></i> Nouvel achat</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Tableau des achats -->
<div class="row">
	<div class="col-12">
		<div class="card mb-4">
			<div class="card-header pb-0">
				<h6>Liste des achats</h6>
			</div>
			<div class="card-body px-0 pt-0 pb-2">
				<div class="table-responsive p-0">
					<table class="table align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Région</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ville</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Article</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qté</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Montant</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Frais</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
							</tr>
						</thead>
						<tbody>
						<?php if (empty($rows ?? [])): ?>
							<tr><td colspan="9" class="text-center text-sm py-4">Aucun achat enregistré.</td></tr>
						<?php else: ?>
							<?php foreach ($rows as $r): ?>
								<tr>
									<td class="text-sm ps-4"><?= (int)$r['id'] ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['date_achat']) ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['region']) ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['ville']) ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['libelle']) ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['quantite']) ?> <?= htmlspecialchars((string)$r['unite']) ?></td>
									<td class="text-sm"><?= htmlspecialchars(moneyAr((float)$r['montant_base'])) ?></td>
									<td class="text-sm"><?= htmlspecialchars((string)$r['frais_percent']) ?>%</td>
									<td class="text-sm font-weight-bold"><?= htmlspecialchars(moneyAr((float)$r['montant_total'])) ?></td>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
