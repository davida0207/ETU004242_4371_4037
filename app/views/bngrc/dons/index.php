<?php
$title = 'Dons';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$flash = $flash ?? '';
$flashMap = [
	'created' => ['ok', 'Don créé.'],
	'deleted' => ['ok', 'Don supprimé.'],
	'blocked' => ['warn', 'Modification/suppression impossible: allocations existantes.'],
];
$filters = $filters ?? [];
?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="flash <?= $flashMap[$flash][0] === 'warn' ? 'flash-warn' : '' ?>">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
	</div>
<?php endif; ?>

<div class="filters">
	<form method="get" action="/dons" class="filters-form">
		<select name="categorie" class="input input--sm">
			<?php foreach (($categories ?? []) as $key => $label): ?>
				<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$label) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<select name="article_id" class="input input--md">
			<option value="">Tous articles</option>
			<?php foreach (($articles ?? []) as $a): ?>
				<option value="<?= (int)$a['id'] ?>" <?= ((string)($filters['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$a['libelle']) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<input class="input input--date" type="date" name="start_date" value="<?= htmlspecialchars((string)($filters['start_date'] ?? '')) ?>">
		<input class="input input--date" type="date" name="end_date" value="<?= htmlspecialchars((string)($filters['end_date'] ?? '')) ?>">

		<button class="btn btn-secondary" type="submit">Filtrer</button>
		<a class="btn btn-primary" href="/dons/add">+ Ajouter</a>
	</form>
</div>

<div class="card">
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Date</th>
				<th>Article</th>
				<th>Qté</th>
				<th>Valeur</th>
				<th>Reste</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (($rows ?? []) as $d):
			$quantite = (float)($d['quantite'] ?? 0);
			$attribueQ = (float)($d['attribue_quantite'] ?? 0);
			$resteQ = max(0.0, $quantite - $attribueQ);
			$prix = (float)($d['prix_unitaire'] ?? 0);
			$valeur = $quantite * $prix;
			$resteVal = $resteQ * $prix;
		?>
			<tr id="d<?= (int)$d['id'] ?>">
				<td><?= (int)$d['id'] ?></td>
				<td><?= htmlspecialchars((string)$d['date_don']) ?></td>
				<td><?= htmlspecialchars((string)$d['libelle']) ?></td>
				<td><?= htmlspecialchars((string)$d['quantite']) ?> <?= htmlspecialchars((string)$d['unite']) ?></td>
				<td><?= htmlspecialchars(moneyAr($valeur)) ?></td>
				<td><?= htmlspecialchars(moneyAr($resteVal)) ?></td>
				<td>
					<a class="btn btn-secondary" href="/dons/<?= (int)$d['id'] ?>">Détail</a>
					<a class="btn btn-secondary" href="/dons/<?= (int)$d['id'] ?>/edit">Modifier</a>
					<form method="post" action="/dons/<?= (int)$d['id'] ?>/delete" class="inline-form">
						<button class="btn btn-danger" type="submit">Supprimer</button>
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
