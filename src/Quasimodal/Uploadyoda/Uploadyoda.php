<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Config\Repository;
use Input;

class Uploadyoda {

    protected $config;

    public function __construct( Repository $config )
    {
        $this->config = $config;
    }

    public function upload( $key, $allowedMimeTypes=null, $maxFileSize=null, $path=null, $name=null )
    {
        if ( Input::hasFile( $key) )
        {
           // check Mime Type is valid 
            if ( !$allowedMimeTypes )
                $allowedMimeTypes = $this->config->get('uploadyoda::allowedMimeTypes');
            
            $fileExt = pathinfo(Input::file($key)->getClientOriginalName(), PATHINFO_EXTENSION);

            if ( !in_array( $fileExt, $allowedMimeTypes ) )
                return false; // this will be changed to proper error behaviour eventually

            // check file size is valid
            if ( !$maxFileSize )
                $maxFileSize = $this->config->get('uploadyoda::maxFileSize');

            if ( Input::file($key)->getSize() > $maxFileSize )
                return false; // this will be changed to proper error behaviour eventually
            
            // we should change this to check the uploads table for the filename collisions and append a numeric if needs be 
            if ( !$name )
               $name = uniqid(); 

            if ( !$path )
                $path = $this->config->get('uploadyoda::upload_path');

            Input::file($key)->move( public_path() . $path, $name . '.' . $fileExt );
            
            // returns path to upload to be saved in the database (note we can change this to save to database if we provide a whole media upload package like wordpress?) 
            return $path. $name. $fileExt;        
        }
    }
}
