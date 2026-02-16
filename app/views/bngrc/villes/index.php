<?php
$title = 'Villes';
ob_start();

$flash = $flash ?? null;
$flashMap = [
	'created' => ['ok', 'Ville créée.'],
	'updated' => ['ok', 'Ville modifiée.'],
	'deleted' => ['ok', 'Ville supprimée.'],
	'blocked' => ['warn', 'Suppression impossible: des besoins existent ou contrainte FK.'],
];
$filters = $filters ?? ['region_id' => null];
?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="flash <?= $flashMap[$flash][0] === 'warn' ? 'flash-warn' : '' ?>">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
	</div>
<?php endif; ?>

<div class="filters">
	<form method="get" action="/villes" style="display:flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%;">
		<select name="region_id" class="input" style="max-width: 340px;">
			<option value="">Toutes les régions</option>
			<?php foreach (($regions ?? []) as $r): ?>
				<option value="<?= (int)$r['id'] ?>" <?= ((int)($filters['region_id'] ?? 0) === (int)$r['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string)$r['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button class="btn btn-secondary" type="submit">Filtrer</button>
		<a class="btn btn-primary" href="/villes/add">+ Ajouter une ville</a>
	</form>
</div>

<div class="card">
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Ville</th>
				<th>Région</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (($villes ?? []) as $v): ?>
			<tr>
				<td><?= (int)$v['id'] ?></td>
				<td><?= htmlspecialchars((string)$v['nom']) ?></td>
				<td><?= htmlspecialchars((string)($v['region_nom'] ?? '')) ?></td>
				<td>
					<a class="btn btn-secondary" href="/villes/<?= (int)$v['id'] ?>/edit">Modifier</a>
					<form method="post" action="/villes/<?= (int)$v['id'] ?>/delete" style="display:inline">
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
