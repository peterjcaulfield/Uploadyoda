<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Support\ServiceProvider;
use Quasimodal\Uploadyoda\models\Upload as Upload;

class UploadyodaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    public function boot()
    {
        // load the app resources views/config etc
        $this->package('quasimodal/uploadyoda', null, __DIR__);

        include 'routes.php';
    }


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	    $this->app['uploadyoda'] = $this->app->share(function($app)
        {
            return new Uploadyoda( $app['config'], new Upload() );
        });    
        
        // bind the validation service
        $this->app->bind('Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator', function()
        {
            return new \Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator( $this->app['validator'] );    
        });

        // now we bind our repository interface implementations
        $this->app->bind('Quasimodal\Uploadyoda\repositories\UploadRepositoryInterface', 'Quasimodal\Uploadyoda\repositories\EloquentUploadRepository');
        $this->app->bind('Quasimodal\Uploadyoda\repositories\UploadyodaUserRepositoryInterface', 'Quasimodal\Uploadyoda\repositories\EloquentUploadyodaUserRepository');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('uploadyoda');
	}

}
