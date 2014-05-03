<?php

use Mockery as m;

class UploadValidatorTest extends Orchestra\Testbench\TestCase
{

    public function setUp( )
    {
        parent::setUp();
        $this->validator = $this->app->make('Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator');
    }

    public function tearDown()
    {
        m::close();
        $_FILES = array();
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

    public function testValidationValidDynamicUploadMethodReturnsFalseIfNoFile()
    {
        $passes = $this->validator->with(array())->valid('upload');
        $this->assertEquals(false, $passes);
        $this->assertEquals('server error', $this->validator->errors()->first());
    }

    public function testValidationValidDynamicUploadMethodReturnsFalseIfErrorInFilesArray()
    {
        $_FILES['file'] = array('error' => 0);
        // set the error
        $_FILES['file']['error'] = 1;
        $passes = $this->validator->with(array())->valid('upload');
        $this->assertEquals(false, $passes);
        $this->assertEquals($this->validator->getPHPUploadError(1), $this->validator->errors()->first());
    }

    public function testValidationValidDynamicUploadMethodReturnsFalseIfFilesizeExceedsConfigMaxFilesize()
    {
        $fileMock = m::mock('foo');
        $_FILES = array('file' => array('error' => 0));

        $request = array('file' => $fileMock);
        $fileMock->shouldReceive('getSize')
            ->once()
            ->andReturn(Config::get('uploadyoda::max_file_size') + 1);

        $passes = $this->validator->with($request)->valid('upload');
        $this->assertEquals(false, $passes);
        $this->assertEquals('file size exceeds config max file size', $this->validator->errors()->first());
    }

    public function testValidationReturnsFalseWithInvalidMimeType()
    {
        $fileMock = m::mock('Symfony\Component\HttpFoundation\File\File');
        $_FILES = array('file' => array('error' => 0));
        $request = array('file' => $fileMock);
        $fileMock->shouldReceive('getSize')
            ->once()
            ->andReturn(Config::get('uploadyoda::max_file_size'));

        $fileMock->shouldReceive('getPath')
            ->andReturn('path/to/file');

        $fileMock->shouldReceive('isValid')
            ->andReturn(true);

        $fileMock->shouldReceive('guessExtension')
            ->andReturn('mimeThatDoesntExist');

        $passes = $this->validator->with($request)->valid('upload');

        $this->assertEquals(false, $passes);
        $this->assertEquals('Invalid mime type', $this->validator->errors()->first());
    }
}
