<?php
$title = 'Détail besoin';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$besoin = $besoin ?? [];
$allocations = $allocations ?? [];
$quantite = (float)($besoin['quantite'] ?? 0);
$attribueQ = (float)($besoin['attribue_quantite'] ?? 0);
$resteQ = max(0.0, $quantite - $attribueQ);
$prix = (float)($besoin['prix_unitaire'] ?? 0);
?>

<div class="card">
	<p>
		<a class="btn btn-secondary" href="/besoins">← Retour</a>
		<a class="btn btn-secondary" href="/besoins/<?= (int)$besoin['id'] ?>/edit">Modifier</a>
		<?php if (!empty($canDelete)): ?>
			<form method="post" action="/besoins/<?= (int)$besoin['id'] ?>/delete" class="inline-form">
				<button class="btn btn-danger" type="submit">Supprimer</button>
			</form>
		<?php endif; ?>
	</p>

	<p><strong>Région:</strong> <?= htmlspecialchars((string)$besoin['region']) ?></p>
	<p><strong>Ville:</strong> <?= htmlspecialchars((string)$besoin['ville']) ?></p>
	<p><strong>Article:</strong> <?= htmlspecialchars((string)$besoin['libelle']) ?> (<?= htmlspecialchars((string)$besoin['categorie']) ?>)</p>
	<p><strong>Date:</strong> <?= htmlspecialchars((string)$besoin['date_besoin']) ?></p>
	<p><strong>Quantité:</strong> <?= htmlspecialchars((string)$besoin['quantite']) ?> <?= htmlspecialchars((string)$besoin['unite']) ?></p>
	<p><strong>Valeur:</strong> <?= htmlspecialchars(moneyAr($quantite * $prix)) ?></p>
	<p><strong>Couvert:</strong> <?= htmlspecialchars((string)$attribueQ) ?> / <?= htmlspecialchars((string)$quantite) ?> (reste: <?= htmlspecialchars((string)$resteQ) ?>)</p>
	<?php if (!empty($besoin['note'])): ?><p><strong>Note:</strong> <?= htmlspecialchars((string)$besoin['note']) ?></p><?php endif; ?>
</div>

<div class="card">
	<h3 class="mb-md">Allocations</h3>
	<table class="table">
		<thead>
			<tr>
				<th>Don</th>
				<th>Date don</th>
				<th>Source</th>
				<th>Quantité</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($allocations as $al): ?>
			<tr>
				<td><a href="/dons/<?= (int)$al['don_id'] ?>">#<?= (int)$al['don_id'] ?></a></td>
				<td><?= htmlspecialchars((string)$al['date_don']) ?></td>
				<td><?= htmlspecialchars((string)($al['source'] ?? '')) ?></td>
				<td><?= htmlspecialchars((string)$al['quantite']) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
