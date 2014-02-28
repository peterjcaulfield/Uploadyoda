<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Support\ServiceProvider,
    Illuminate\Foundation\AliasLoader,
    Quasimodal\Uploadyoda\models\Upload,
    Quasimodal\Uploadyoda\Service\Filter;

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

        /**
         * Register the package aliases
         *
         * this saves the package user from having to manually add the package aliases
         */

        $loader = AliasLoader::getInstance();
        $loader->alias('Uploadyoda', 'Quasimodal\Uploadyoda\Facades\Uploadyoda'); 
        $loader->alias('Helpers', 'Quasimodal\Uploadyoda\Facades\Helpers'); 
        $loader->alias('Filter', 'Quasimodal\Uploadyoda\Facades\Filter'); 
    }


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        /**
         * Facade service provider bindings
         */

	    $this->app['uploadyoda'] = $this->app->share(function($app)
        {
            return new Uploadyoda( $app['config'], new Upload() );
        });

	    $this->app['helpers'] = $this->app->share(function($app)
        {
            return new Helpers();
        });
        
        $this->app['filter'] = $this->app->share(function($app)
        {
            return new Filter();
        });
        
        /**
         * Validation service binding
         */

        $this->app->bind('Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator', function()
        {
            return new \Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator( $this->app['validator'] );    
        });

        /**
         * Repository implementation bindings
         */

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
