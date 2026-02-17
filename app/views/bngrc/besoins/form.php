<?php
$mode   = $mode ?? 'add';
$title  = $mode === 'edit' ? 'Modifier besoin' : 'Ajouter un besoin';
ob_start();

$besoin  = $besoin ?? [];
$errors  = $errors ?? ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''];
$villes  = $villes ?? [];
$articles = $articles ?? [];
$action  = $mode === 'edit' ? '/besoins/' . (int)($besoin['id'] ?? 0) . '/edit' : '/besoins/add';
?>

<div class="row justify-content-center">
	<div class="col-lg-8 col-md-10">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="mb-0">
							<i class="fas fa-<?= $mode === 'edit' ? 'pencil-alt' : 'plus-circle' ?> me-2 text-primary"></i>
							<?= $mode === 'edit' ? 'Modifier le besoin #' . (int)($besoin['id'] ?? 0) : 'Nouveau besoin' ?>
						</h6>
						<p class="text-sm text-secondary mb-0">Remplissez les informations ci-dessous. Les champs marqués <span class="text-danger">*</span> sont obligatoires.</p>
					</div>
					<a class="btn btn-outline-secondary btn-sm mb-0" href="/besoins"><i class="fas fa-arrow-left me-1"></i> Retour</a>
				</div>
			</div>
			<div class="card-body">
				<form method="post" action="<?= htmlspecialchars($action) ?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Ville <span class="text-danger">*</span></label>
								<select class="form-select" name="ville_id" required>
									<option value="">-- Sélectionner une ville --</option>
									<?php foreach ($villes as $v): ?>
										<option value="<?= (int)$v['id'] ?>" <?= ((string)($besoin['ville_id'] ?? '') === (string)$v['id']) ? 'selected' : '' ?>>
											<?= htmlspecialchars((string)$v['nom']) ?> — <?= htmlspecialchars((string)($v['region_nom'] ?? '')) ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (!empty($errors['ville_id'])): ?>
									<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars((string)$errors['ville_id']) ?></small>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Article <span class="text-danger">*</span></label>
								<select class="form-select" name="article_id" required>
									<option value="">-- Sélectionner un article --</option>
									<?php foreach ($articles as $a): ?>
										<option value="<?= (int)$a['id'] ?>" <?= ((string)($besoin['article_id'] ?? '') === (string)$a['id']) ? 'selected' : '' ?>>
											<?= htmlspecialchars((string)$a['libelle']) ?> (<?= htmlspecialchars(ucfirst((string)$a['categorie'])) ?>) — <?= htmlspecialchars((string)$a['unite']) ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (!empty($errors['article_id'])): ?>
									<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars((string)$errors['article_id']) ?></small>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Quantité <span class="text-danger">*</span></label>
								<input class="form-control" type="number" step="0.01" min="0.01" name="quantite" value="<?= htmlspecialchars((string)($besoin['quantite'] ?? '')) ?>" placeholder="Ex: 500" required>
								<?php if (!empty($errors['quantite'])): ?>
									<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars((string)$errors['quantite']) ?></small>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label">Date du besoin <span class="text-danger">*</span></label>
								<input class="form-control" type="date" name="date_besoin" value="<?= htmlspecialchars((string)($besoin['date_besoin'] ?? '')) ?>" required>
								<?php if (!empty($errors['date_besoin'])): ?>
									<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars((string)$errors['date_besoin']) ?></small>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="form-control-label">Note / Commentaire</label>
						<textarea class="form-control" name="note" rows="3" placeholder="Détails supplémentaires (optionnel)..."><?= htmlspecialchars((string)($besoin['note'] ?? '')) ?></textarea>
					</div>

					<hr class="horizontal dark">
					<div class="d-flex justify-content-end gap-2">
						<a href="/besoins" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Annuler</a>
						<button class="btn bg-gradient-primary" type="submit"><i class="fas fa-save me-1"></i> Enregistrer</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
