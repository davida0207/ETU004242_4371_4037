<?php
$title = 'Détail don';
ob_start();

$don = $don ?? [];
$allocations = $allocations ?? [];
$quantite = (float)($don['quantite'] ?? 0);
$attribueQ = (float)($don['attribue_quantite'] ?? 0);
$resteQ = max(0.0, $quantite - $attribueQ);
?>

<div class="card">
	<p>
		<a class="btn btn-secondary" href="/dons">← Retour</a>
		<?php if (!empty($canEdit)): ?><a class="btn btn-secondary" href="/dons/<?= (int)$don['id'] ?>/edit">Modifier</a><?php endif; ?>
		<?php if (!empty($canDelete)): ?>
			<form method="post" action="/dons/<?= (int)$don['id'] ?>/delete" class="inline-form">
				<button class="btn btn-danger" type="submit">Supprimer</button>
			</form>
		<?php endif; ?>
	</p>

	<p><strong>Article:</strong> <?= htmlspecialchars((string)$don['libelle']) ?> (<?= htmlspecialchars((string)$don['categorie']) ?>)</p>
	<p><strong>Date:</strong> <?= htmlspecialchars((string)$don['date_don']) ?></p>
	<p><strong>Quantité:</strong> <?= htmlspecialchars((string)$don['quantite']) ?> <?= htmlspecialchars((string)$don['unite']) ?></p>
	<p><strong>Attribué:</strong> <?= htmlspecialchars((string)$attribueQ) ?> / <?= htmlspecialchars((string)$quantite) ?> (reste: <?= htmlspecialchars((string)$resteQ) ?>)</p>
	<?php if (!empty($don['source'])): ?><p><strong>Source:</strong> <?= htmlspecialchars((string)$don['source']) ?></p><?php endif; ?>
	<?php if (!empty($don['note'])): ?><p><strong>Note:</strong> <?= htmlspecialchars((string)$don['note']) ?></p><?php endif; ?>
</div>

<div class="card">
	<h3 class="mb-md">Allocations</h3>
	<table class="table">
		<thead>
			<tr>
				<th>Besoin</th>
				<th>Date besoin</th>
				<th>Région</th>
				<th>Ville</th>
				<th>Quantité</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($allocations as $al): ?>
			<tr>
				<td><a href="/besoins/<?= (int)$al['besoin_id'] ?>">#<?= (int)$al['besoin_id'] ?></a></td>
				<td><?= htmlspecialchars((string)$al['date_besoin']) ?></td>
				<td><?= htmlspecialchars((string)$al['region']) ?></td>
				<td><?= htmlspecialchars((string)$al['ville']) ?></td>
				<td><?= htmlspecialchars((string)$al['quantite']) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
