<?php 

use Mockery as m;


class UploadTest extends \Orchestra\Testbench\TestCase 
{

    protected $upload;
    protected $uploadMock;
    

    public function setUp()
    {
        parent::setUp();

        $this->upload = new Quasimodal\Uploadyoda\repositories\EloquentUploadRepository(new Quasimodal\Uploadyoda\models\Upload());
        $this->uploadMock = m::mock('\Quasimodal\Uploadyoda\models\Upload');

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

    /**
     * Test running migration.
     *
     * @test
     */
    public function testRunningMigration()
    {
        $upload = array('name' => 'test.jpg', 'path' => 'test', 'mime_type' => 'image/jpg', 'size' => '100kb');
        $this->upload->create($upload);
        $uploads = $this->upload->count();
        $this->assertEquals(1, $uploads);
    } 

    public function testGetSearchDatesCreatesDateRangeIfNoMonthIsPassed()
    {
        $month = null;
        $dates = Quasimodal\Uploadyoda\models\Upload::getSearchDates($month);
        $this->assertEquals('1970-01-01 00:00:00', $dates['start']);
        $this->assertEquals(\Carbon\Carbon::now()->toDateTimeString(), $dates['end']);
    }

    public function testGetSearchWithMonthSpecifier()
    {
        $month = 1; 
        $dates = Quasimodal\Uploadyoda\models\Upload::getSearchDates($month);
        $this->assertEquals('2014-01-01 00:00:00', $dates['start']);
        $this->assertEquals('2014-02-01 00:00:00', $dates['end']);
    }
}
