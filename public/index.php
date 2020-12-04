<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

$vendors = '';
if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
    || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false) {
	$vendors =  dirname(__DIR__).'/vendor/autoload.php';
	echo "DEV\n";
} else {
	$vendors =  dirname(__DIR__).'/vendor/autoload.php';
	echo "PROD!\n";
}
echo $_SERVER['SERVER_NAME'];

require $vendors;

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
dd($vendors);


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
