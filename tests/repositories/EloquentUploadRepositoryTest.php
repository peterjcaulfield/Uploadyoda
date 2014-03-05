<?php

use Mockery as m,
    Quasimodal\Uploadyoda\repositories\EloquentUploadRepository,
    Quasimodal\Uploadyoda\models\Upload;



class EloquentUploadRepositoryTest extends \Orchestra\Testbench\TestCase
{
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
            'Filter' => 'Quasimodal\Uploadyoda\Facades\Uploadyoda'
        );
    }

    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make('artisan');
        // create the tables
        $artisan->call('migrate', [
            '--database' => 'uploadyoda',
            '--path' => '../src/migrations'      
            ]);
    }

    public function tearDown()
    {
        m::close();
    }

    public function makeRepo()
    {
        return App::make('Quasimodal\Uploadyoda\repositories\UploadRepositoryInterface');
    }

    public function seedDb()
    {
        $artisan = $this->app->make('artisan');
        // seed the db's
        $artisan->call('db:seed', [
            '--class' => 'Quasimodal\Uploadyoda\seeds\PackageSeeder'
            ]); 
    }
  
     /**
     * Test running migration.
     *
     * @test
     */
    public function testRunningMigration()
    {
        $this->seedDb();
        $this->assertEquals(Upload::count(), 28);
    } 
        
    public function testRepositoryCreateMethod()
    {
        $uploadStub = [
            'name' => 'test.jpeg',
            'mime_type' => 'image/jpeg',
            'path' => 'path/to/file',
            'size' => '100kB'
        ];
        $repo = $this->makeRepo();
        $repo->create($uploadStub);
        $this->assertEquals(Upload::count(), 1);
    }

    public function testRepositoryDestroyMethod()
    {
        $uploadStub = [
            'name' => 'test.jpeg',
            'mime_type' => 'image/jpeg',
            'path' => 'path/to/file',
            'size' => '100kB'
        ];
        $repo = $this->makeRepo();
        $repo->create($uploadStub);
        $this->assertEquals(Upload::count(), 1);
        $repo->destroy(1);
        $this->assertEquals(Upload::count(), 0);
    }

    public function testRepositoryGetAllUploadsMethod()
    {
        $this->seedDb();
        $repo = $this->makeRepo();
        $repo->setPaginate(false);
        $uploads = $repo->getAllUploads();
        $this->assertEquals($uploads->count(), 28);
    } 

    public function testRepositoryGetAllUploadsWithSearchFilter()
    {
        $filter = [
            'search' => 'foo',
            'date' => false,
            'type' => false 
            ];

        $this->seedDb();
        $repo = $this->makeRepo();
        $repo->setPaginate(false);
        $repo->setFilter($filter);
        $this->assertEquals($repo->getAllUploads()->count(), 6); 
    }

    public function testRepositoryGetAllUploadsWithTypeFilter()
    {
        $filter = [
            'search' => false,
            'date' => false,
            'type' => 'image' 
            ];

        $this->seedDb();
        $repo = $this->makeRepo();
        $repo->setPaginate(false);
        $repo->setFilter($filter);
        $this->assertEquals($repo->getAllUploads()->count(), 12); 
    }
}
