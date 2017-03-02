<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . "/../vendor/autoload.php";

$app = new Silex\Application;

$app->register(new Silex\Provider\TwigServiceProvider, [
	'twig.path' => __DIR__.'/../views'
]);

$app->register(new Silex\Provider\DoctrineServiceProvider, [
	'db.options' => [
		'driver' => 'pdo_mysql',
		'dbname' => 'image_upload',
		'user' => 'root',
		'password' => 'kdRPyFMp',
		'host' => 'localhost',
	]
]);

$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

$app->register(new IU\Providers\UploadcareProvider);

$app->mount('/', new \IU\Controllers\ImagesController);

$app->run();