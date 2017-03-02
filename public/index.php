<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . "/../vendor/autoload.php";

function dd($var)
{
	die(var_dump($var));
}

$app = new Silex\Application;

$app['debug'] = true;

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

$app->get('/', function() use ($app) {
	$images = $app['db']->prepare("SELECT * FROM images");
	$images->execute();

	$images = $images->fetchAll(\PDO::FETCH_CLASS, \IU\Models\Image::class);
	return $app['twig']->render('home.twig',[
		"images_container" => array_chunk($images, 3),
	]);
})->bind('home');

$app->get('/image/{hash}', function(Request $request) use ($app){
	$stmt = $app['db']->prepare("
		SELECT url FROM images WHERE hash = :hash
	");
	$stmt->execute([
		'hash' => $request->get('hash')
	]);
	$stmt->setFetchMode(\PDO::FETCH_CLASS, \IU\Models\Image::class);
	$image = $stmt->fetch();
	if (!$image) {
		return $app->abort(404, "The image does not exist");
	}

	return $app['twig']->render('image.twig', [
		'image' => $image
	]);
})->bind('images.image');

$app->post('/upload', function(Request $request) use ($app) {
	if($request->get('file_id') === '') {
		return $app->redirect($app['url_generator']->generate('home'));
	}

	$file = $app['uploadcare']->getFile($request->get('file_id'));

	$stmt = $app['db']->prepare("
		INSERT INTO images (hash, url, created_at)
		VALUES (:hash, :url, NOW())
	");	

	$stmt->execute([
		'hash' => bin2hex(random_bytes(20)),
		'url' => $file->getUrl(),
	]);

	return $app->redirect($app['url_generator']->generate('home'));
})->bind('images.upload');

$app->run();