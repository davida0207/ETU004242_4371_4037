<?php
$title = 'Nouvel achat';
ob_start();

function moneyAr(float $v): string {
	return number_format($v, 0, ',', ' ') . ' Ar';
}

$achat = $achat ?? [];
$besoin = $besoin ?? null;
$besoinsOuverts = $besoins_ouverts ?? [];
$errors = $errors ?? ['ville_id' => '', 'quantite' => '', 'date_achat' => '', 'cash' => ''];
$cashInfo = $cashInfo ?? ['cash_restant' => 0];
$frais = (float)($frais_percent ?? 0);

$prixUnitaire = $besoin ? (float)$besoin['prix_unitaire'] : 0.0;
$quantite = isset($achat['quantite']) && is_numeric($achat['quantite']) ? (float)$achat['quantite'] : 0.0;
$montantBase = $quantite * $prixUnitaire;
$montantTotal = $montantBase * (1.0 + $frais / 100.0);
?>

<div class="row">
	<div class="col-lg-8 mx-auto">
		<div class="card">
			<div class="card-header pb-0">
				<h6>Nouvel achat via dons en argent</h6>
			</div>
			<div class="card-body">
				<form method="post" action="/achats/add">

					<!-- Besoin à couvrir -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Besoin à couvrir</label>
						<select name="besoin_id" class="form-select">
							<option value="">Choisir un besoin</option>
							<?php foreach ($besoinsOuverts as $b):
								$resteB = (float)$b['quantite'] - (float)$b['attribue_quantite'];
								if ($resteB <= 0) continue;
							?>
								<option value="<?= (int)$b['id'] ?>" <?= ((string)($achat['besoin_id'] ?? '') === (string)$b['id']) ? 'selected' : '' ?>>
									#<?= (int)$b['id'] ?> - <?= htmlspecialchars((string)$b['ville']) ?> - <?= htmlspecialchars((string)$b['libelle']) ?> (reste <?= htmlspecialchars((string)$resteB) ?> <?= htmlspecialchars((string)$b['unite']) ?>)
								</option>
							<?php endforeach; ?>
						</select>
						<?php if ($errors['quantite'] && !$besoin): ?><div class="text-danger text-xs mt-1"><?= htmlspecialchars($errors['quantite']) ?></div><?php endif; ?>
					</div>

					<?php if ($besoin): ?>
						<div class="alert alert-info py-2">
							<h6 class="text-sm font-weight-bold mb-1">Besoin ciblé</h6>
							<p class="text-xs mb-0"><strong>Ville :</strong> <?= htmlspecialchars((string)$besoin['ville']) ?></p>
							<p class="text-xs mb-0"><strong>Article :</strong> <?= htmlspecialchars((string)$besoin['libelle']) ?> (<?= htmlspecialchars((string)$besoin['unite']) ?>)</p>
							<p class="text-xs mb-0"><strong>Quantité totale :</strong> <?= htmlspecialchars((string)$besoin['quantite']) ?></p>
							<p class="text-xs mb-0"><strong>Quantité déjà couverte :</strong> <?= htmlspecialchars((string)$besoin['attribue_quantite']) ?></p>
						</div>
					<?php endif; ?>

					<!-- Ville -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Ville</label>
						<select name="ville_id" class="form-select">
							<option value="">Choisir une ville</option>
							<?php foreach (($villes ?? []) as $v): ?>
								<option value="<?= (int)$v['id'] ?>" <?= ((string)($achat['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars((string)$v['nom']) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if ($errors['ville_id']): ?><div class="text-danger text-xs mt-1"><?= htmlspecialchars($errors['ville_id']) ?></div><?php endif; ?>
					</div>

					<!-- Quantité -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Quantité à acheter</label>
						<input type="number" step="0.01" name="quantite" id="achat-quantite" class="form-control" value="<?= htmlspecialchars((string)($achat['quantite'] ?? '')) ?>">
						<?php if ($errors['quantite']): ?><div class="text-danger text-xs mt-1"><?= htmlspecialchars($errors['quantite']) ?></div><?php endif; ?>
					</div>

					<!-- Date -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Date d'achat</label>
						<input type="date" name="date_achat" class="form-control" value="<?= htmlspecialchars((string)($achat['date_achat'] ?? date('Y-m-d'))) ?>">
						<?php if ($errors['date_achat']): ?><div class="text-danger text-xs mt-1"><?= htmlspecialchars($errors['date_achat']) ?></div><?php endif; ?>
					</div>

					<!-- Note -->
					<div class="mb-3">
						<label class="form-label text-xs text-uppercase font-weight-bold">Note</label>
						<textarea name="note" class="form-control" rows="2"><?= htmlspecialchars((string)($achat['note'] ?? '')) ?></textarea>
					</div>

					<!-- Résumé financier -->
					<div class="card card-body bg-gray-100 mb-3">
						<h6 class="text-sm font-weight-bold mb-2">Résumé financier</h6>
						<p class="text-sm mb-1"><strong>Prix unitaire :</strong> <span id="rf-prix-unitaire"><?= htmlspecialchars(moneyAr($prixUnitaire)) ?></span></p>
						<p class="text-sm mb-1"><strong>Montant de base :</strong> <span id="rf-montant-base"><?= htmlspecialchars(moneyAr($montantBase)) ?></span></p>
						<p class="text-sm mb-1"><strong>Frais :</strong> <span id="rf-frais-percent"><?= htmlspecialchars((string)$frais) ?></span>%</p>
						<p class="text-sm mb-1"><strong>Montant total :</strong> <span id="rf-montant-total"><?= htmlspecialchars(moneyAr($montantTotal)) ?></span></p>
						<p class="text-sm mb-0"><strong>Fonds restants :</strong> <?= htmlspecialchars(moneyAr((float)$cashInfo['cash_restant'])) ?></p>
						<?php if ($errors['cash']): ?><div class="text-danger text-xs mt-1"><?= htmlspecialchars($errors['cash']) ?></div><?php endif; ?>
					</div>

					<!-- Actions -->
					<div class="d-flex gap-2">
						<a href="/achats" class="btn btn-outline-secondary">Annuler</a>
						<button type="submit" class="btn btn-primary">Enregistrer l'achat</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$nonce = \Flight::get('csp_nonce');
?>
<script nonce="<?= htmlspecialchars((string)$nonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
	const besoinsMeta = <?php
		$meta = [];
		foreach ($besoinsOuverts as $b) {
			$meta[(int)$b['id']] = [
				'prix_unitaire' => (float)$b['prix_unitaire'],
				'unite' => (string)$b['unite'],
			];
		}
		echo json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	?>;

	const selectBesoin = document.querySelector('select[name="besoin_id"]');
	const inputQuantite = document.getElementById('achat-quantite');
	const spanPrix = document.getElementById('rf-prix-unitaire');
	const spanBase = document.getElementById('rf-montant-base');
	const spanTotal = document.getElementById('rf-montant-total');
	const fraisPercent = parseFloat(document.getElementById('rf-frais-percent').textContent.replace(',', '.')) || 0;

	function formatAr(v) {
		return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(v) + ' Ar';
	}

	function recalc() {
		const besoinId = selectBesoin ? parseInt(selectBesoin.value || '0', 10) : 0;
		const meta = besoinsMeta[besoinId] || { prix_unitaire: 0 };
		const q = parseFloat(inputQuantite.value.replace(',', '.')) || 0;
		const pu = parseFloat(meta.prix_unitaire) || 0;
		const base = q * pu;
		const total = base * (1 + fraisPercent / 100);
		if (spanPrix) spanPrix.textContent = formatAr(pu);
		if (spanBase) spanBase.textContent = formatAr(base);
		if (spanTotal) spanTotal.textContent = formatAr(total);
	}

	if (selectBesoin) {
		selectBesoin.addEventListener('change', recalc);
	}
	if (inputQuantite) {
		inputQuantite.addEventListener('input', recalc);
	}

	recalc();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
