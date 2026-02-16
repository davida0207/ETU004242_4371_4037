<?php
declare(strict_types=1);

namespace app\middlewares;

use flight\Engine;
use Tracy\Debugger;

class SecurityHeadersMiddleware
{
	protected Engine $app;

	public function __construct(Engine $app)
	{
		$this->app = $app;
	}
	
	public function before(array $params): void
	{
		$nonce = $this->app->get('csp_nonce');
		$isDev = Debugger::$showBar === true;

		// development mode to execute Tracy debug bar CSS
		$tracyCssBypass = "'nonce-{$nonce}'";
		if($isDev) {
			$tracyCssBypass = ' \'unsafe-inline\'';
		}

		// Alpine.js (default build) requires eval()/new Function() for expression parsing.
		// Allow it only in development; for production consider swapping to Alpine CSP build.
		$devEvalBypass = $isDev ? " 'unsafe-eval'" : '';

		$csp = "default-src 'self'; "
			. "base-uri 'self'; "
			. "object-src 'none'; "
			. "script-src 'self' 'nonce-{$nonce}'{$devEvalBypass}; "
			. "style-src 'self'{$tracyCssBypass} https://fonts.googleapis.com https://cdn.jsdelivr.net; "
			. "font-src 'self' data: https://fonts.gstatic.com; "
			. "img-src 'self' data:;";
		$this->app->response()->header('X-Frame-Options', 'SAMEORIGIN');
		$this->app->response()->header("Content-Security-Policy", $csp);
		if ($isDev) {
			// Prevent stale HTML/assets in the browser during development.
			$this->app->response()->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
			$this->app->response()->header('Pragma', 'no-cache');
			$this->app->response()->header('Expires', '0');
		}
		$this->app->response()->header('X-XSS-Protection', '1; mode=block');
		$this->app->response()->header('X-Content-Type-Options', 'nosniff');
		$this->app->response()->header('Referrer-Policy', 'no-referrer-when-downgrade');
		$this->app->response()->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
		$this->app->response()->header('Permissions-Policy', 'geolocation=()');
	}
}