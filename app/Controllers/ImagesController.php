<?php

namespace IU\Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ImagesController implements ControllerProviderInterface
{
	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$images = $app['db']->prepare("SELECT * FROM images");
			$images->execute();

			$images = $images->fetchAll(\PDO::FETCH_CLASS, \IU\Models\Image::class);
			return $app['twig']->render('home.twig',[
				"images_container" => array_chunk($images, 3),
			]);
		})->bind('home');

		$controllers->get('/', function(Application $app) {
			$images = $app['db']->prepare("SELECT * FROM images");
			$images->execute();

			$images = $images->fetchAll(\PDO::FETCH_CLASS, \IU\Models\Image::class);
			return $app['twig']->render('home.twig',[
				"images_container" => array_chunk($images, 3),
			]);
		})->bind('home');

		$controllers->get('/image/{hash}', function(Request $request, Application $app) {
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

		$controllers->post('/upload', function(Request $request, Application $app) {
			if($request->get('file_id') === '') {
				return $app->redirect($app['url_generator']->generate('home'));
			}

			$file = $app['uploadcare']->getFile($request->get('file_id'));

			$stmt = $app['db']->prepare("
				INSERT INTO images (hash, url)
				VALUES (:hash, :url)
			");	

			$stmt->execute([
				'hash' => bin2hex(random_bytes(20)),
				'url' => $file->getUrl(),
			]);

			return $app->redirect($app['url_generator']->generate('home'));
		})->bind('images.upload');

		return $controllers;
	}
}