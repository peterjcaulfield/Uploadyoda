<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Config\Repository;
use Input;


class Uploadyoda {

    protected static $mimes = array(
        'image' => array('image/jpeg', 'image/png'),
        'video' => array('video/mp4')
    );

    protected $config;

    protected $upload;

    protected $php_upload_errors = array(
        'File exceeds max server filesize',
        "File exceeds the HTML form's maximum filesize",
        'File was only partially uploaded',
        'No file was uploaded',
        'Server missing tmp folder',
        'Failed to write to disk',
        'PHP extension stopped the file upload'
    );

    public static function getMimes()
    {
        return self::$mimes;
    }

    public function __construct( Repository $config, Upload $upload )
    {
        $this->config = $config;

        $this->upload = $upload;
    }

    public function createUniqueFilename( $filename, $ext ) 
    {
        // first we replace any spaces with hyphens
        $filename = preg_replace('/\s+/', '-', $filename);

        if ( $this->upload->where( 'name', '=', $filename . '.' . $ext )->count() )
        {   // if filename ends in hyphen(s) or hyphen(s) with number(s) remove these and add a single hyphen at the end 
            if ( preg_match( '/-+(\d+)?$/', $filename, $match ) )
                $filename = substr($filename, 0, -(strlen($match[0]))) . '-';
            else
              $filename = $filename . '-';  
            
            // check if there is a versioned filename in the database already and retrieve the highest versioned filename 
            $existing_versioned = $this->upload->select('name')
                ->where( 'name', 'like', $filename . '_%.%' ) // wildcards appended to query for one or more characters after the hyphen
                ->orderBy('name', 'desc')
                ->take(1)
                ->get();
            // check for 
            if ( count( $existing_versioned ) && preg_match('/\d+$/', pathinfo( $existing_versioned[0]->name, PATHINFO_FILENAME ) ) )
            {
                $last_versioned = pathinfo( $existing_versioned[0]->name, PATHINFO_FILENAME );
                preg_match('/\d+$/', $last_versioned, $match );
                if ( count( $match ) ){
                    $version_number = ++$match[0];

                    return $filename . $version_number;
                }
            }
            else // no versioned filenames exist yet so we can create the first
                return $filename . 1;
        }// no record exists with current filename to return it for db insertion
       return $filename; 
    }

    public function upload( $key, $allowedMimeTypes=null, $maxFileSize=null, $path=null, $name=null )
    {
        if ( empty($_FILES) )
        {
            /**
             * If files array is empty there are a number of possible causes
             * we return an error string that communicates a generic description
             * that will cover all these causes
             */
            throw new UploadyodaException( 'server error', 1 );
        }
        // check for server errors that would be recorded in the $_FILES array
        if ( !empty($_FILES) && isset($_FILES['file']['error']) )
        {
            if ( $_FILES['file']['error'] != 0 )
                throw new UploadyodaException( $this->php_upload_errors[$_FILES['file']['error'] - 1], 1 );
        }

        if ( Input::hasFile( $key  ) )
        {
            $response  = array();
            $uploadedFile = Input::file( $key );

            // check Mime Type is valid 
            $allowedMimeTypes = isset( $allowedMimeTypes ) ? $allowedMimeTypes : $this->config->get('uploadyoda::allowed_mime_types');
            $fileExt = pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION);
            if ( !in_array( $fileExt, $allowedMimeTypes ) )
                throw new UploadyodaException( 'Invalid file type: ' . $fileExt, 1 );

            // check file size is valid
            $maxFileSize = isset( $maxFileSize ) ? $maxFileSize : $this->config->get('uploadyoda::max_file_size');
            $fileSize = $uploadedFile->getSize();
            if ( $fileSize > $maxFileSize )
                throw new UploadyodaException( "File size exceeds the application's maximum filesize", 1 );

            // create unique filename
            $name = isset( $name ) ? $name : pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $name = $this->createUniqueFilename( $name, $fileExt );

            // set the upload directory path
            $path = isset( $path ) ? $path : $this->config->get('uploadyoda::uploads_directory');

            // format the response
            $response['name'] = $name . '.' . $fileExt;
            $response['path'] = $path;
            $response['mime_type'] = $uploadedFile->getMimeType();
            $response['size'] = $this::formatFilesize($fileSize);

            // upload the file
            $uploadedFile->move( public_path() .'/' . $path, $name . '.' . $fileExt );

            return $response;
        }
    }

    static function formatFilesize($fileSize)
    {
        if ( $fileSize < 1000000 )
            return ( ceil( ( $fileSize / 1000 ) * 100 ) / 100 ) . ' kB';
        else
            return ( ceil( ( $fileSize / 1000000 ) * 100 ) / 100 ) . ' MB';
    }

    static function generateThumbnail($filename, $mime)
    {
        // check if image
        if (preg_match('/image/', $mime))
            return '<div class="thumb" style="background-image:url(/packages/quasimodal/uploadyoda/uploads/' . $filename . '); background-size: contain"/></div>';
        // check if pdf
        if (preg_match('/pdf/', $mime))
            return '<div class="preview-container"/><i class="fa fa-5x fa-file-text"></i></div>';
    }
}
