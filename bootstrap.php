<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Annotations\AnnotationRegistry;
//use Doctrine\Common\Annotations\AnnotationReader;

$loader = require __DIR__.'/vendor/autoload.php';
$loader->add('Api', __DIR__.'/src');

$config = new Configuration();
$config->setProxyDir('/tmp');
$config->setProxyNamespace('EntityProxy');
$config->setAutoGenerateProxyClasses(true);

AnnotationRegistry::registerFile(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

$driver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
	new Doctrine\Common\Annotations\AnnotationReader(),
	array(__DIR__ . DIRECTORY_SEPARATOR . 'src')
);

$config->setMetadataDriverImpl($driver);

$db = require_once __DIR__ . '/config/db.php';

if (!$db) {
	throw new \Exception("Error Processing Config db", 1);
}

$em = EntityManager::create(
	$db['options'],
	$config
);
