<?php namespace Quasimodal\Uploadyoda;
/**
 * TODO
 *
 * change validation to use laravels validation helper
 * make uploads complete via ajax
 */

use Illuminate\Config\Repository;
use Input;


class Uploadyoda {

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

    public function __construct( Repository $config, Upload $upload )
    {
        $this->config = $config;

        $this->upload = $upload;
    }

    public function createUniqueFilename( $filename, $ext ) 
    {
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
            if ( count( $existing_versioned ) )
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
        // first check for conditions which cause $_FILES to be empty 
       // $file_max = ini_get('upload_max_filesize');

        if ( empty($_FILES) )
        {
            /**
             * If files array is empty there are a number of possible causes
             * we return an error string that communicates a generic description
             * that will cover all these causes
             */

            return 'Server error';
        }
        // check for server errors that would be recorded in the $_FILES array
        if ( !empty($_FILES) && isset($_FILES['file']['error']) )
        {
            if ( $_FILES['file']['error'] != 0 )
                return $php_upload_errors[$_FILES['file']['error'] - 1];
        }

        if ( Input::hasFile( $key  ) )
        {
            $response  = array();
            
            // check Mime Type is valid 
            if ( !$allowedMimeTypes )
                $allowedMimeTypes = $this->config->get('uploadyoda::allowed_mime_types');

            $fileExt = pathinfo(Input::file( $key )->getClientOriginalName(), PATHINFO_EXTENSION);

            if ( !in_array( $fileExt, $allowedMimeTypes ) )
                return 'Invalid file type: ' . $fileExt; 

            // check file size is valid
            if ( !$maxFileSize )
                $maxFileSize = $this->config->get('uploadyoda::max_file_size');

            if ( Input::file( $key )->getSize() > $maxFileSize )
                return "File size exceeds the application's maximum filesize"; // this will be changed to proper error behaviour eventually

            // we should change this to check the uploads table for the filename collisions and append a numeric if needs be 
            if ( !$name )
               $name = pathinfo(Input::file($key)->getClientOriginalName(), PATHINFO_FILENAME);

            $name = $this->createUniqueFilename( $name, $fileExt );

            

            if ( !$path )
                $path = $this->config->get('uploadyoda::uploads_directory');

            $response['name'] = $name . '.' . $fileExt;
            $response['path'] = $path;
            $response['mime_type'] = Input::file($key)->getMimeType();
            $response['size'] = Input::file($key)->getSize();

            if ( $response['size'] < 1000000 )
             $response['size'] = ( ceil( ( $response['size'] / 1000 ) * 100 ) / 100 ) . ' kB';
            else
              $response['size'] = ( ceil( ( $response['size'] / 1000000 ) * 100 ) / 100 ) . ' MB';

            Input::file( $key )->move( public_path() .'/' . $path, $name . '.' . $fileExt );

            return $response;
        }
    }
}
