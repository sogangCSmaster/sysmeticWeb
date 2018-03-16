<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
ini_set("memory_limit", -1);

require_once dirname(__FILE__).'/vendor/autoload.php';
require_once dirname(__FILE__).'/app/lib/DBConnect.php';
require_once dirname(__FILE__).'/app/lib/Util.php';
require_once dirname(__FILE__).'/app/lib/PasswordHash.php';
date_default_timezone_set('Asia/Seoul');

use API\Application;

$_ENV['SLIM_MODE'] = (getenv('SLIM_MODE')) ? getenv('SLIM_MODE') : 'development';

$config = array();

require_once dirname(__FILE__).'/app/config/default.php';

$configFile = dirname(__FILE__).'/app/config/'.$_ENV['SLIM_MODE'].'.php';

if(is_readable($configFile)){
	require_once $configFile;
}

session_start();

$app = new Slim\Slim($config['app']);
// 미들웨어 추가(캐시, 콘텐트타입, API횟수제한, JSON, 인증 등)
// $app->add(new API\Middleware\JSON('/api/v1'));

$log = $app->getLog();

/*
$langFile = dirname(__FILE__).'/share/lang/default.php';
if(is_readable($langFile)){
	require_once $langFile;
}

if(!empty($_GET['lang'])) {
	$_SESSION['lang'] = $_GET['lang'];
	$localLangFile = dirname(__FILE__).'/share/lang/'.$_SESSION['lang'].'.php';
	if(is_readable($localLangFile)){
		require_once $localLangFile;
	}
}
*/

$app->container->singleton('db', function() use ($app) {
	$dbConfig = $app->config('db');

	// 디비연결
	$db = new DBConnect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['name']);
	$db->connect();

	return $db;
});
/*
// memcached
$app->container->singleton('m', function() use ($app) {
	$memcached_server_hosts = $app->config('memcached');

	$M = new Memcached();

	foreach($memcached_server_hosts as $host){
		$M->addServer($host, 11211);
	}

	return $M;
});
*/
// JSON friendly errors
// NOTE: debug must be false
// or default error template will be printed
$app->error(function (\Exception $e) use ($app, $log) {

    $mediaType = $app->request->getMediaType();

    $isAPI = (bool) preg_match('|^/api/.*$|', $app->request->getPath());

    // Standard exception data
    $error = array(
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    );

    // Graceful error data for production mode
    if (!in_array(
        get_class($e),
        array('API\\Exception', 'API\\Exception\ValidationException')
    )
        && 'production' === $app->config('mode')) {
        $error['message'] = 'There was an internal error';
        unset($error['file'], $error['line']);
    }

    // Custom error data (e.g. Validations)
    if (method_exists($e, 'getData')) {
        $errors = $e->getData();
    }

    if (!empty($errors)) {
        $error['errors'] = $errors;
    }

    $log->error($e->getMessage());
    if ('application/json' === $mediaType || true === $isAPI) {
        $app->response->headers->set(
            'Content-Type',
            'application/json'
        );
        echo json_encode($error);
    } else {
        echo '<html>
        <head><title>Error</title></head>
        <body><h1>Error: ' . $error['code'] . '</h1><p>'
        . $error['message']
        .'</p></body></html>';
    }

});

/// Custom 404 error
$app->notFound(function () use ($app) {

    $mediaType = $app->request->getMediaType();

    $isAPI = (bool) preg_match('|^/api/.*$|', $app->request->getPath());

    if ('application/json' === $mediaType || true === $isAPI) {

        $app->response->headers->set(
            'Content-Type',
            'application/json'
        );

        echo json_encode(
            array(
                'code' => 404,
                'message' => 'Not found'
            )
        );

    } else {
        echo '<html>
        <head><title>404 Page Not Found</title></head>
        <body><h1>404 Page Not Found</h1><p>The page you are
        looking for could not be found.</p></body></html>';
    }
});
