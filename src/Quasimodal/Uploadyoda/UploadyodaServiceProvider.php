<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Support\ServiceProvider;

class UploadyodaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    public function boot()
    {
        $this->package('quasimodal/uploadyoda');

        include __DIR__.'/../../routes.php';
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

        // now we bind our repository interface implementations
        $this->app->bind('Quasimodal\Uploadyoda\UploadRepositoryInterface', 'Quasimodal\Uploadyoda\EloquentUploadRepository');
        $this->app->bind('Quasimodal\Uploadyoda\UploadyodaUserRepositoryInterface', 'Quasimodal\Uploadyoda\EloquentUploadyodaUserRepository');
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
