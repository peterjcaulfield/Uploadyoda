<?php  

use Mockery as m;


class FiltersTest extends \Orchestra\Testbench\TestCase 
{


    public function setUp()
    {
        parent::setUp();
        $this->app['router']->enableFilters();
        $artisan = $this->app->make('artisan');

        $artisan->call('migrate', [
            '--database' => 'uploadyoda',
            '--path' => '../src/migrations'      
        ]);

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
        $app['path.base'] = __DIR__ . '/../../src';

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

    public function testAuthFilterRedirectsToWelcomeIfUserIsNotLoggedInAndNoUsersInDB()
    {
        $this->call('GET', '/uploadyoda');
        
        $this->assertRedirectedTo('uploadyoda_user/welcome');
    }

    public function testAuthFilterRedirectsToLoginIfUserIsNotLoggedInAndUserDbHasAUser()
    {
        $uploadyodaUserMock = m::mock('Quasimodal\Uploadyoda\repositores\EloquentUploadyodaUserRepository');
        $this->app->instance('Quasimodal\Uploadyoda\repositories\EloquentUploadyodaUserRepository', $uploadyodaUserMock);     

        $uploadyodaUserMock->shouldReceive('count')
            ->once()
            ->andReturn(1);

        $this->call('GET', '/uploadyoda');
        
        $this->assertRedirectedTo('uploadyoda_user/login');
    }

    public function testEmptyFilesFilterReturnsServerErrorWhenFilesArrayIsEmpty()
    {
        $uploadyodaUserMock = m::mock('Quasimodal\Uploadyoda\repositores\EloquentUploadyodaUserRepository');
        $this->app->instance('Quasimodal\Uploadyoda\repositories\EloquentUploadyodaUserRepository', $uploadyodaUserMock);     

        $uploadyodaUserMock->shouldReceive('count')
            ->once()
            ->andReturn(1);

        Auth::shouldReceive('guest')
            ->andReturn(false);

        $reponse = $this->call('POST', '/uploadyoda/store')->original;

        $this->assertEquals($reponse, 'Server error'); 
    }
}
