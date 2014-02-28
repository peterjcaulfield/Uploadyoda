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
        // seed the db's
        $artisan->call('db:seed', [
            '--class' => 'Quasimodal\Uploadyoda\seeds\PackageSeeder'
            ]); 

        $this->upload = new Upload();
    }

    public function tearDown()
    {
        m::close();
    }

    public function makeRepo($modelMock)
    {
        $repo = new EloquentUploadRepository($modelMock);
        return $repo;
    }
  
     /**
     * Test running migration.
     *
     * @test
     */
    public function testRunningMigration()
    {
        /*$this->upload->name = 'test.jpg';
        $this->upload->path = 'test';
        $this->upload->mime_type = 'jpg';
        $this->upload->size = '100kb';
        $this->upload->save();*/
        $uploads = Upload::count();
        $this->assertEquals($uploads, 1);
    } 
        
    public function testRepositoryCreateMethod()
    {
        $uploadStub = array('name' => 'test.jpg');
        $modelMock = m::mock('Quasimodal\Uploadyoda\models\Upload');
        $repo = $this->makeRepo($modelMock);
        $modelMock->shouldReceive('create')
            ->once()
            ->with($uploadStub);
        $repo->create($uploadStub);
    }

    public function testRepositoryDestroyMethod()
    {
        $modelMock = m::mock('Quasimodal\Uploadyoda\models\Upload');
        $repo = $this->makeRepo($modelMock);
        $modelMock->shouldReceive('destroy')
            ->once()
            ->with(1);
        $repo->destroy(1);
    }

    public function testRepositoryGetAllUploadsMethod()
    {
        $modelMock = m::mock('Quasimodal\Uploadyoda\models\Upload');
        $repo = $this->makeRepo($modelMock);
        $modelMock->shouldReceive('orderBy')
            ->with('created_at', 'desc')
            ->andReturn(m::self())
            ->shouldReceive('paginate')
            ->with($repo->paginate);

        $repo->getAllUploads();
    } 

    /*public function testRepositoryGetAllUploadsWithFilterMethod()
    {
        $modelMock = m::mock('Quasimodal\Uploadyoda\models\Upload');
        $repo = $this->makeRepo($modelMock);

        $filters = array(
            'date' => 1,
            'type' => 'image',
            'search' => 'test'
        );

        $searchDatesStub = array('start' => 'start', 'end' => 'end');

        Filter::shouldReceive('getSearchDates')
            ->with($filters['date'])
            ->once()
            ->andReturn($searchDatesStub);

        Filter::shouldReceive('getSearchMimeTypes')
            ->with($filters['type'])
            ->once()
            ->andReturn(array('mimes'));

        $modelMock->shouldReceive('where')
            ->with('name', 'LIKE', '%' . $filters['search'] . '%')

        $repo->getAllUploadsWithFilter($filters);
    }*/
}
