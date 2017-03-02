<?php 

namespace IU\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UploadcareProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$app['uploadcare'] = $app->share(function() use ($app){
			return new \Uploadcare\Api('587d8ba803b1187b33ea', 'b6e6e7ff8569d6502766');
		});
	}

	public function boot(Application $app)
	{
		//
	}
}