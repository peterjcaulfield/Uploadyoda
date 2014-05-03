<?php

use Mockery as m,
    Quasimodal\Uploadyoda\models\Upload as Upload;


class UploadYodaTest extends Orchestra\Testbench\TestCase
{

    protected $upload;


    public function setUp()
    {
        parent::setUp();

        $this->upload = new Upload;

        $artisan = $this->app->make('artisan');

        $artisan->call('migrate', [
            '--database' => 'uploadyoda',
            '--path' => '../src/migrations'
        ]);
    }

    public function tearDown()
    {
        $_FILES = array();
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

    /**
     * Test running migration.
     *
     * @test
     */
    public function testRunningMigration()
    {
        $this->upload->name = 'test.jpg';
        $this->upload->path = 'test';
        $this->upload->mime_type = 'jpg';
        $this->upload->size = '100kb';
        $this->upload->save();
        $uploads = Upload::count();
        $this->assertEquals($uploads, 1);
    }

    /**
     * Tests of uploadyoda's uniqueFilename helper function
     */
    public function testFilenameWithHyphenIsValidIfNoCollisionsInDb()
    {
        $formattedFilename = Uploadyoda::createUniqueFilename('test-', 'jpg');
        $this->assertEquals('test-', $formattedFilename);
    }

    public function testFilenameWithHyphenAndNumberIsValidIfNoCollisionsInDb()
    {
        $formattedFilename = Uploadyoda::createUniqueFilename('test-1', 'jpg');
        $this->assertEquals('test-1', $formattedFilename);
    }

    public function testFilenameIsVersionedWhenOneCollisionInDbAndCollisionIsNotVersioned()
    {
        $this->upload->create(array( 'name' => 'test.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));

        $formattedFilename = Uploadyoda::createUniqueFilename('test', 'jpg');
        $this->assertEquals('test-1', $formattedFilename);
    }

    public function testFilenameIsVersionedWhenFilenameContainsVersionAndCollisionInDb()
    {
        $this->upload->create(array( 'name' => 'test-1.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));

        $formattedFilename = Uploadyoda::createUniqueFilename('test-1', 'jpg');
        $this->assertEquals('test-2', $formattedFilename);
    }

    public function testFilenameIsVersionedWhenCollisionInDbAndVersionedFilenameAlreadyInDb()
    {
        $this->upload->create(array( 'name' => 'test.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));
        $this->upload->create(array( 'name' => 'test-1.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));

        $formattedFilename = Uploadyoda::createUniqueFilename('test', 'jpg');
        $this->assertEquals('test-2', $formattedFilename);
    }

    public function testFilenameIsVersionedBasedOnIncrementOfExistingVersionedFilenameWithHighestVersionNumericWhenCollisionInDb()
    {
        $this->upload->create(array( 'name' => 'test.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));
        $this->upload->create(array( 'name' => 'test-7.jpg', 'path' => 'test', 'mime_type' => 'jpg', 'size' => '100kb' ));

        $formattedFilename = Uploadyoda::createUniqueFilename('test', 'jpg');
        $this->assertEquals('test-8', $formattedFilename);
    }

    /**
     * Test uploadyoda's upload helper using passed in params
     */

    public function testUploadCorrectlyAcceptsUserDefinedFilename()
    {
        $fileMock = m::mock('fileMock');

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        $fileMock->shouldReceive('getSize')->andReturn(1);

        $fileMock->shouldReceive('getMimeType')->andReturn('mime');
        $fileMock->shouldReceive('move');

        $response = Uploadyoda::upload($fileMock, null, 'custom_filename' );

        $this->assertEquals('custom_filename.jpg', $response['upload']['name']);
    }

    public function testUploadCorrectlyAcceptsUserDefinedUploadPath()
    {
        $fileMock = m::mock('fileMock');

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        $fileMock->shouldReceive('getSize')->andReturn(1);

        $fileMock->shouldReceive('getMimeType')->andReturn('mime');
        $fileMock->shouldReceive('move');

        $response = Uploadyoda::upload($fileMock, 'my_custom_folder', null );

        $this->assertEquals('my_custom_folder', $response['upload']['path']);
    }

    public function testReturnBytesMethodConvertsAlphanumericByteRepresentationToNumericBytes()
    {
        $this->assertEquals(Uploadyoda::returnBytes('1K'), 1024);
        $this->assertEquals(Uploadyoda::returnBytes('1M'), 1024 * 1024);
        $this->assertEquals(Uploadyoda::returnBytes('1G'), 1024 * 1024 * 1024);
    }

    public function testFormatFilesizeFormatsBytesToAlphanumericRepresentation()
    {
       $this->assertEquals(Uploadyoda::formatFilesize(1000), '1 kB');
       $this->assertEquals(Uploadyoda::formatFilesize(1000 * 1000), '1 MB');
    }
}
