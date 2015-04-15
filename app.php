<?php
require_once __DIR__.'/bootstrap.php';

use Api\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application(['em' => $em]);

$app->before(function (Request $request) {
	if ($request->getMethod() == 'OPTIONS') {
		return;
	}
	$tokens = require_once __DIR__ . '/config/token.php';
	if (!$tokens) {
		throw new \Exception("Error Processing Token file", 1);
	}
	if(!$request->headers->has('X-Token')) {
		return new Response('Unauthorized', 401);
	}
	if (!in_array($request->headers->get('X-Token'), array_keys($tokens))) {
		return new Response('Unauthorized', 401);
	}
	if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
		$data = json_decode($request->getContent(), true);
		$request->request->replace(is_array($data) ? $data : array());
	}
});

$app->after(function (Request $request, Response $response) {
	$response->headers->set('Access-Control-Allow-Origin', '*');
	$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Token');
});


