<?php
$mode = $mode ?? 'add';
$title = $mode === 'edit' ? 'Modifier région' : 'Ajouter région';
ob_start();

$region = $region ?? ['nom' => ''];
$errors = $errors ?? ['nom' => ''];
$action = $mode === 'edit' ? '/regions/' . (int)($region['id'] ?? 0) . '/edit' : '/regions/add';
?>

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex align-items-center">
					<h6 class="mb-0"><?= htmlspecialchars($title) ?></h6>
					<a class="btn btn-outline-secondary btn-sm ms-auto" href="/regions">← Retour</a>
				</div>
			</div>
			<div class="card-body">
				<form method="post" action="<?= htmlspecialchars($action) ?>">
					<div class="form-group">
						<label for="nom" class="form-control-label">Nom</label>
						<input class="form-control" type="text" id="nom" name="nom" value="<?= htmlspecialchars((string)($region['nom'] ?? '')) ?>">
						<?php if (!empty($errors['nom'])): ?>
							<small class="text-danger"><?= htmlspecialchars((string)$errors['nom']) ?></small>
						<?php endif; ?>
					</div>

					<div class="d-flex justify-content-end mt-4">
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
