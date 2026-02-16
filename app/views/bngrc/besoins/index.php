<?php
$title = 'Besoins';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$flash = $flash ?? '';
$flashMap = [
	'created' => ['ok', 'Besoin créé.'],
	'deleted' => ['ok', 'Besoin supprimé.'],
	'blocked' => ['warn', 'Suppression impossible: allocations existantes.'],
];
$filters = $filters ?? [];
?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="flash <?= $flashMap[$flash][0] === 'warn' ? 'flash-warn' : '' ?>">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
	</div>
<?php endif; ?>

<div class="filters">
	<form method="get" action="/besoins" style="display:flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%;">
		<select name="region_id" class="input" style="max-width: 260px;">
			<option value="">Toutes régions</option>
			<?php foreach (($regions ?? []) as $r): ?>
				<option value="<?= (int)$r['id'] ?>" <?= ((string)($filters['region_id'] ?? '') === (string)$r['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$r['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<select name="ville_id" class="input" style="max-width: 260px;">
			<option value="">Toutes villes</option>
			<?php foreach (($villes ?? []) as $v): ?>
				<option value="<?= (int)$v['id'] ?>" <?= ((string)($filters['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$v['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<select name="categorie" class="input" style="max-width: 220px;">
			<?php foreach (($categories ?? []) as $key => $label): ?>
				<option value="<?= htmlspecialchars((string)$key) ?>" <?= ((string)($filters['categorie'] ?? '') === (string)$key) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$label) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<select name="article_id" class="input" style="max-width: 260px;">
			<option value="">Tous articles</option>
			<?php foreach (($articles ?? []) as $a): ?>
				<option value="<?= (int)$a['id'] ?>" <?= ((string)($filters['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$a['libelle']) ?>
				</option>
			<?php endforeach; ?>
		</select>

		<input class="input" type="date" name="start_date" value="<?= htmlspecialchars((string)($filters['start_date'] ?? '')) ?>" style="max-width: 200px;">
		<input class="input" type="date" name="end_date" value="<?= htmlspecialchars((string)($filters['end_date'] ?? '')) ?>" style="max-width: 200px;">

		<button class="btn btn-secondary" type="submit">Filtrer</button>
		<a class="btn btn-primary" href="/besoins/add">+ Ajouter</a>
	</form>
</div>

<div class="card">
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Date</th>
				<th>Région</th>
				<th>Ville</th>
				<th>Article</th>
				<th>Qté</th>
				<th>Valeur</th>
				<th>Reste</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (($rows ?? []) as $b):
			$quantite = (float)($b['quantite'] ?? 0);
			$attribueQ = (float)($b['attribue_quantite'] ?? 0);
			$resteQ = max(0.0, $quantite - $attribueQ);
			$prix = (float)($b['prix_unitaire'] ?? 0);
			$valeur = $quantite * $prix;
			$resteVal = $resteQ * $prix;
		?>
			<tr id="b<?= (int)$b['id'] ?>">
				<td><?= (int)$b['id'] ?></td>
				<td><?= htmlspecialchars((string)$b['date_besoin']) ?></td>
				<td><?= htmlspecialchars((string)$b['region']) ?></td>
				<td><?= htmlspecialchars((string)$b['ville']) ?></td>
				<td><?= htmlspecialchars((string)$b['libelle']) ?></td>
				<td><?= htmlspecialchars((string)$b['quantite']) ?> <?= htmlspecialchars((string)$b['unite']) ?></td>
				<td><?= htmlspecialchars(moneyAr($valeur)) ?></td>
				<td><?= htmlspecialchars(moneyAr($resteVal)) ?></td>
				<td>
					<a class="btn btn-secondary" href="/besoins/<?= (int)$b['id'] ?>">Détail</a>
					<a class="btn btn-secondary" href="/besoins/<?= (int)$b['id'] ?>/edit">Modifier</a>
					<form method="post" action="/besoins/<?= (int)$b['id'] ?>/delete" style="display:inline">
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
