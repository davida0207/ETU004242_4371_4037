<?php
$title = 'Régions';
ob_start();

$flash = $flash ?? null;
$error = $error ?? null;
$flashMap = [
	'created' => ['ok', 'Région créée.'],
	'updated' => ['ok', 'Région modifiée.'],
	'deleted' => ['ok', 'Région supprimée.'],
	'blocked' => ['warn', 'Suppression impossible: des villes existent déjà.'],
];
?>

<?php if (!empty($error)): ?>
	<div class="flash flash-warn">
		<?= htmlspecialchars((string)$error) ?>
	</div>
<?php endif; ?>

<?php if ($flash && isset($flashMap[$flash])): ?>
	<div class="flash <?= $flashMap[$flash][0] === 'warn' ? 'flash-warn' : '' ?>">
		<?= htmlspecialchars($flashMap[$flash][1]) ?>
	</div>
<?php endif; ?>

<div class="card">
	<p>
		<a class="btn btn-primary" href="/regions/add">+ Ajouter une région</a>
	</p>

	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nom</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (($regions ?? []) as $r): ?>
			<tr>
				<td><?= (int)$r['id'] ?></td>
				<td><?= htmlspecialchars((string)$r['nom']) ?></td>
				<td>
					<a class="btn btn-secondary" href="/regions/<?= (int)$r['id'] ?>/edit">Modifier</a>
					<form method="post" action="/regions/<?= (int)$r['id'] ?>/delete" style="display:inline">
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
