<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

$isProd = false;
$vendors = '';
$envs = '';

if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
    || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false) {
	$vendors =  dirname(__DIR__).'/vendor/autoload.php';
	$envs = dirname(__DIR__).'/.env';
} else {
	$vendors =  dirname(__DIR__).'/../httpd.private/staging/vendor/autoload.php';
	$envs = dirname(__DIR__).'/../httpd.private/staging/.env';
}
require $vendors;

(new Dotenv())->bootEnv($envs);

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
