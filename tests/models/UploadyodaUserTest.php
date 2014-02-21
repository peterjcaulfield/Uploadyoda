<?php namespace Quasimodal\Uploadyoda; 

use Hash,
    Mockery as m;

class UploadyodaUserTest extends \Orchestra\Testbench\TestCase 
{
    public function setUp()
    {
        parent::setUp();
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

    public function testUserPasswordIsHashedOnCreate()
    {
        $user = array('firstname' => 'optimus', 'lastname' => 'prime', 'password' => 'mysupersecurepassword');
        $hashedUser = array('firstname' => 'optimus', 'lastname' => 'prime', 'password' => Hash::make('mysupersecurepassword'));

        $uploadyodaUserMock = m::mock('Quasimodal\Uploadyoda\UploadyodaUser');
        $uploadyodaUser = new EloquentUploadyodaUserRepository($uploadyodaUserMock);

        Hash::shouldReceive('make')
                ->once()
                ->andReturn('hashed');

        $uploadyodaUserMock->shouldReceive('create')
                ->once()
                ->with($hashedUser);

        $uploadyodaUser->create($user);    
    }
}
