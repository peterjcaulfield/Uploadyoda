<?php 

use Mockery as m;
use \Quasimodal\Uploadyoda\Upload as Upload;


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
    * Tests of uploadyoda's upload helper function using packages config
    */

    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage server error
     */ 
    public function testUploadReturnsServerErrorWhenFilesArrayIsEmpty()
    {
        $uploadResponse = Uploadyoda::upload('file');
    }

    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage File exceeds max server filesize
     */ 
    public function testUploadReturnsPHPErrorIfPresent()
    {
        $_FILES['file']['error'] = 1;
        $uploadResponse = Uploadyoda::upload('file');
    }

    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage Invalid file type: xyz
     */ 
    public function testUploadReturnsErrorIfMimeTypeIsInvalidBasedOnConfig()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        Config::set('uploadyoda::allowed_mime_types', array('jpg'));
        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.xyz');
        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file');
    }  
    
    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage File size exceeds the application's maximum filesize
     */ 
    public function testUploadReturnsErrorIfFilesizeExceedsMaxSizeBasedOnConfig()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        Config::set('uploadyoda::max_file_size', 1);
        $fileMock->shouldReceive('getSize')->andReturn(2);
        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file');
    }  
    
    /**
     * Test uploadyoda's upload helper using passed in params
     */    
    
    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage Invalid file type: xyz
     */ 
    public function testUploadReturnsErrorIfMimeTypeIsInvalidBasedOnArgs()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.xyz');
        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file', array('jpg'));
    }  
    
    /**
     * @expectedException Quasimodal\Uploadyoda\UploadyodaException
     * @expectedExceptionMessage File size exceeds the application's maximum filesize
     */ 
    public function testUploadReturnsErrorIfFileSizeExceedsMaxSizeBasedOnArgs()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        $fileMock->shouldReceive('getSize')->andReturn(2);
        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file', null, 1);
    }  

    public function testUploadCorrectlyAcceptsUserDefinedFilename()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        $fileMock->shouldReceive('getSize')->andReturn(1);

        $fileMock->shouldReceive('getMimeType')->andReturn('mime');
        $fileMock->shouldReceive('move');

        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file', null, 1, null, 'custom_filename' );
        
        $this->assertEquals('custom_filename.jpg', $response['name']);
    }
    
    public function testUploadCorrectlyAcceptsUserDefinedUploadPath()
    {
        $fileMock = m::mock('fileMock');        
        $_FILES['file'] = array('file' => $fileMock); 

        $mockRequest = m::mock('\Illuminate\Http\Request');
        $mockRequest->shouldReceive('hasFile')->andReturn(true);
        $mockRequest->shouldReceive('file')->andReturn($fileMock);

        $fileMock->shouldReceive('getClientOriginalName')->andReturn('test.jpg');
        $fileMock->shouldReceive('getSize')->andReturn(1);

        $fileMock->shouldReceive('getMimeType')->andReturn('mime');
        $fileMock->shouldReceive('move');

        Input::swap($mockRequest);

        $response = Uploadyoda::upload('file', null, 1, 'my_custom_folder', null );
        
        $this->assertEquals('my_custom_folder', $response['path']);
    }
}
