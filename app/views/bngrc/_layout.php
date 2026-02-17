<?php
/** Gabarit principal — BNGRC (Argon Dashboard) */
$title = $title ?? 'BNGRC';
$nonce = \Flight::get('csp_nonce');

/* Détection de la page active pour la sidebar */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$currentPage = trim($uri, '/');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" type="image/png" href="/assets/images/favicon.png">
	<title><?= htmlspecialchars((string)$title) ?> — BNGRC</title>

	<!-- Polices -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

	<!-- Icônes Nucleo -->
	<link href="/assets/argon/css/nucleo-icons.css" rel="stylesheet" />
	<link href="/assets/argon/css/nucleo-svg.css" rel="stylesheet" />
	<!-- Font Awesome 6 (CDN public) -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Argon Dashboard CSS -->
	<link rel="stylesheet" href="/assets/argon/css/argon-dashboard.min.css?v=2.1.0">

	<!-- Utilitaires BNGRC (inline-form, filters-form, font-mono) -->
	<link rel="stylesheet" href="/assets/bngrc-override.css">
</head>

<body class="g-sidenav-show bg-gray-100">
	<!-- Bandeau sombre haut de page (design Argon) -->
	<div class="min-height-300 bg-dark position-absolute w-100"></div>

	<!-- ===================== SIDEBAR ===================== -->
	<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
		<!-- En-tête sidebar -->
		<div class="sidenav-header">
			<i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
			<a class="navbar-brand m-0" href="/bngrc/dashboard">
				<span class="ms-1 font-weight-bold text-dark" style="font-size:1.1rem;">🇲🇬 BNGRC</span>
			</a>
		</div>
		<hr class="horizontal dark mt-0">

		<!-- Menu de navigation -->
		<div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
			<ul class="navbar-nav">
				<!-- Titre section -->
				<li class="nav-item mt-2">
					<h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Tableau de bord</h6>
				</li>
				<li class="nav-item">
				<a class="nav-link <?= ($currentPage === 'bngrc/dashboard' || $currentPage === '') ? 'active' : '' ?>" href="/bngrc/dashboard">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Dashboard</span>
					</a>
				</li>

				<!-- Section gestion -->
				<li class="nav-item mt-3">
					<h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestion</h6>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'besoins') ? 'active' : '' ?>" href="/besoins">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-bullet-list-67 text-danger text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Besoins</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'dons') ? 'active' : '' ?>" href="/dons">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-basket text-success text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Dons</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'dispatch') ? 'active' : '' ?>" href="/dispatch">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fas fa-random text-primary text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Dispatch</span>
					</a>
				</li>

				<!-- Section référentiel -->
				<li class="nav-item mt-3">
					<h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Référentiel</h6>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'regions') ? 'active' : '' ?>" href="/regions">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-map-big text-info text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Régions</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'villes') ? 'active' : '' ?>" href="/villes">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-building text-warning text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Villes</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?= str_starts_with($currentPage, 'articles') ? 'active' : '' ?>" href="/articles">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="ni ni-box-2 text-dark text-sm opacity-10"></i>
						</div>
						<span class="nav-link-text ms-1">Articles</span>
					</a>
				</li>
			</ul>
		</div>

		<!-- Pied de sidebar -->
		<div class="sidenav-footer mx-3 mt-3">
			<div class="card card-plain shadow-none" id="sidenavCard">
				<div class="card-body text-center p-3 w-100 pt-0">
					<div class="docs-info">
						<h6 class="mb-0 text-sm">BNGRC</h6>
						<p class="text-xs font-weight-bold mb-0">Suivi Collectes &amp; Distributions</p>
					</div>
				</div>
			</div>
		</div>
	</aside>
	<!-- ===================== FIN SIDEBAR ===================== -->

	<!-- ===================== CONTENU PRINCIPAL ===================== -->
	<main class="main-content position-relative border-radius-lg">
		<!-- Barre de navigation supérieure -->
		<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
			<div class="container-fluid py-1 px-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
						<li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="/bngrc/dashboard">BNGRC</a></li>
						<li class="breadcrumb-item text-sm text-white active" aria-current="page"><?= htmlspecialchars((string)$title) ?></li>
					</ol>
					<h6 class="font-weight-bolder text-white mb-0"><?= htmlspecialchars((string)$title) ?></h6>
				</nav>

				<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
					<div class="ms-md-auto pe-md-3 d-flex align-items-center">
					</div>
					<ul class="navbar-nav justify-content-end">
						<li class="nav-item d-xl-none ps-3 d-flex align-items-center">
							<a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
								<div class="sidenav-toggler-inner">
									<i class="sidenav-toggler-line bg-white"></i>
									<i class="sidenav-toggler-line bg-white"></i>
									<i class="sidenav-toggler-line bg-white"></i>
								</div>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<!-- Fin navbar supérieure -->

		<!-- Zone de contenu dynamique -->
		<div class="container-fluid py-4">
			<?= $content ?? '' ?>

			<!-- Pied de page -->
			<footer class="footer pt-3">
				<div class="container-fluid">
					<div class="row align-items-center justify-content-lg-between">
						<div class="col-lg-6 mb-lg-0 mb-4">
							<div class="copyright text-center text-sm text-muted text-lg-start">
								© <?= date('Y') ?> BNGRC — Système de Gestion des Collectes et Distributions
							</div>
						</div>
						<div class="col-lg-6">
							<ul class="nav nav-footer justify-content-center justify-content-lg-end">
								<li class="nav-item"><span class="nav-link text-muted text-sm">ETU004242 · ETU4371 · ETU4037</span></li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</main>
	<!-- ===================== FIN CONTENU PRINCIPAL ===================== -->

	<!-- Core JS Argon Dashboard -->
	<script src="/assets/argon/js/core/popper.min.js"></script>
	<script src="/assets/argon/js/core/bootstrap.min.js"></script>
	<script src="/assets/argon/js/plugins/perfect-scrollbar.min.js"></script>
	<script src="/assets/argon/js/plugins/smooth-scrollbar.min.js"></script>
	<script src="/assets/argon/js/plugins/chartjs.min.js"></script>
	<!-- Argon Dashboard JS -->
	<script src="/assets/argon/js/argon-dashboard.min.js?v=2.1.0"></script>
	<?= $pageScripts ?? '' ?>
	<script nonce="<?= htmlspecialchars($nonce) ?>">
		var win = navigator.platform.indexOf('Win') > -1;
		if (win && document.querySelector('#sidenav-scrollbar')) {
			var options = { damping: '0.5' };
			Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
		}
	</script>
</body>
</html>