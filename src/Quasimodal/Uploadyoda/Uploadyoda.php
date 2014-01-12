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

    public function __construct( Repository $config )
    {
        $this->config = $config;
    }

    public function upload( $key, $allowedMimeTypes=null, $maxFileSize=null, $path=null, $name=null )
    {
        $response  = array();

        if ( Input::hasFile( $key  ) )
        {
            // check Mime Type is valid 
            if ( !$allowedMimeTypes )
                $allowedMimeTypes = $this->config->get('uploadyoda::allowed_mime_types');

            $fileExt = pathinfo(Input::file( $key )->getClientOriginalName(), PATHINFO_EXTENSION);

            if ( !in_array( $fileExt, $allowedMimeTypes ) )
                return 'bad mime'; // this will be changed to proper error behaviour eventually

            // check file size is valid
            if ( !$maxFileSize )
                $maxFileSize = $this->config->get('uploadyoda::max_file_size');

            if ( Input::file( $key )->getSize() > $maxFileSize )
                return 'bad size'; // this will be changed to proper error behaviour eventually

            // we should change this to check the uploads table for the filename collisions and append a numeric if needs be 
            if ( !$name )
                $name = uniqid();

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

            

            // check response of move here

            Input::file( $key )->move( public_path() .'/' . $path, $name . '.' . $fileExt );

            return $response;
        }
    }
}
