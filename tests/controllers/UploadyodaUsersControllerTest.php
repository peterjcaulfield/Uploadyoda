<?php 

use Mockery as m;


class UploadyodaUsersControllerTest extends \Orchestra\Testbench\TestCase 
{

    protected $uploader;
    protected $uploadMock;
    

    public function setUp()
    {
        parent::setUp();

        //$this->uploadMock = m::mock('Quasimodal\Uploadyoda\EloquentUploadyodaUserRepository'); 
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'uploadyoda');
        $app['config']->set('database.connections.uploadyoda', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }    

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return array(
            'Quasimodal\Uploadyoda\UploadyodaServiceProvider'
        );
    }

    /**
     * Get package aliases.  In a normal app environment these would be added to
     * the 'aliases' array in the config/app.php file.  If your package exposes an
     * aliased facade, you should add the alias here, along with aliases for
     * facades upon which your package depends, e.g. Cartalyst/Sentry
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return array(
            'Uploadyoda' => 'Quasimodal\Uploadyoda\Facades\Uploadyoda'
        );
    }

    public function testIndexMethodPassesAllUploadsToViewWhenNoQueryStringPresent()
    {
        //$this->app->instance('Quasimodal\Uploadyoda\UploadyodaUserRepositoryInterface', $this->uploadMock);

        //$response = $this->call('GET', '/uploadyoda');
    }

}
