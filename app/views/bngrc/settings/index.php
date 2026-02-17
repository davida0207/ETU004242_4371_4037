<?php
$title = 'Paramètres';
ob_start();

$fraisPercent = $frais_percent ?? 10.0;
$flash = $flash ?? '';
$errors = $errors ?? [];
?>

<?php if ($flash === 'saved'): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="bi bi-check-circle me-2"></i> Paramètres enregistrés avec succès.
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
	</div>
<?php endif; ?>

<div class="row">
	<div class="col-lg-8 mx-auto">
		<div class="card">
			<div class="card-header pb-0">
				<h6><i class="bi bi-gear me-2"></i>Paramètres BNGRC</h6>
				<p class="text-sm text-muted mb-0">Modifiez les paramètres de l'application directement depuis cette page.</p>
			</div>
			<div class="card-body">
				<form method="post" action="/settings">

					<!-- Frais d'achat -->
					<div class="mb-4">
						<h6 class="text-xs text-uppercase font-weight-bolder text-primary mb-3">Achats via dons en argent</h6>

						<div class="row align-items-center">
							<div class="col-md-8">
								<label for="purchase_fee_percent" class="form-label text-sm font-weight-bold">
									Pourcentage de frais d'achat
								</label>
								<p class="text-xs text-muted mb-2">
									Ce pourcentage est automatiquement ajouté au montant de base lors d'un achat.<br>
									Exemple : si la valeur est <strong>10</strong>, un achat de 100 000 Ar coûtera 110 000 Ar.
								</p>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<input type="number"
										   step="0.01"
										   min="0"
										   max="100"
										   class="form-control <?= !empty($errors['purchase_fee_percent']) ? 'is-invalid' : '' ?>"
										   id="purchase_fee_percent"
										   name="purchase_fee_percent"
										   value="<?= htmlspecialchars((string)$fraisPercent) ?>">
									<span class="input-group-text">%</span>
									<?php if (!empty($errors['purchase_fee_percent'])): ?>
										<div class="invalid-feedback"><?= htmlspecialchars($errors['purchase_fee_percent']) ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<hr class="horizontal dark">

					<!-- Actions -->
					<div class="d-flex justify-content-end gap-2 mt-3">
						<a href="/bngrc/dashboard" class="btn btn-outline-secondary">Annuler</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-check-lg me-1"></i> Enregistrer
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Info card -->
		<div class="card mt-4">
			<div class="card-body">
				<div class="d-flex align-items-start">
					<div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle me-3">
						<i class="bi bi-info-circle text-lg opacity-10"></i>
					</div>
					<div>
						<h6 class="text-sm font-weight-bold mb-1">À propos de ce paramètre</h6>
						<p class="text-xs text-muted mb-0">
							Le <strong>pourcentage de frais d'achat</strong> est appliqué automatiquement dans le formulaire
							<a href="/achats/add">Nouvel achat</a>. Il permet de comptabiliser les frais logistiques
							(transport, manutention, etc.) associés aux achats effectués via les dons en argent.
							La modification prend effet immédiatement pour tous les nouveaux achats.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
