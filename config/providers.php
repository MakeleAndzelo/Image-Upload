<?php 

$app->register(new Silex\Provider\TwigServiceProvider, [
	'twig.path' => __DIR__.'/../views'
]);

$app->register(new Silex\Provider\DoctrineServiceProvider, [
	'db.options' => require __DIR__ . "/database.php"
]);

$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

$app->register(new IU\Providers\UploadcareProvider);