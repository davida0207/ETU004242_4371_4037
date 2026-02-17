<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier ville' : 'Ajouter ville';
ob_start();

$ville = $ville ?? ['nom' => '', 'region_id' => ''];
$errors = $errors ?? ['nom' => '', 'region_id' => ''];
$action = $mode === 'edit' ? '/villes/' . (int)($ville['id'] ?? 0) . '/edit' : '/villes/add';
?>

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex align-items-center justify-content-between">
					<h6><?= $title ?></h6>
					<a class="btn btn-outline-secondary btn-sm" href="/villes">← Retour</a>
				</div>
			</div>
			<div class="card-body">
				<form method="post" action="<?= htmlspecialchars($action) ?>">
					<div class="form-group">
						<label class="form-control-label">Région</label>
						<select class="form-select" name="region_id">
							<option value="">-- Choisir --</option>
							<?php foreach (($regions ?? []) as $r): ?>
								<option value="<?= (int)$r['id'] ?>" <?= ((string)($ville['region_id'] ?? '') === (string)$r['id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars((string)$r['nom']) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (!empty($errors['region_id'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['region_id']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Nom</label>
						<input class="form-control" type="text" name="nom" value="<?= htmlspecialchars((string)($ville['nom'] ?? '')) ?>">
						<?php if (!empty($errors['nom'])): ?><small class="text-danger"><?= htmlspecialchars((string)$errors['nom']) ?></small><?php endif; ?>
					</div>

					<div class="form-group">
						<button class="btn bg-gradient-primary" type="submit">Enregistrer</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
