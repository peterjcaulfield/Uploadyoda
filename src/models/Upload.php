<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent,
    Config,
    Uploadyoda;

class Upload extends Eloquent
{
    protected $table = 'uploads';

    protected $fillable = array( 'name', 'path', 'mime_type', 'size' );

    public static function getSearchDates($date)
    {
        $dates = array();
    
            if ( $date )
            {
                $dates['start'] = \Carbon\Carbon::create(null, $date, 1, 0, 0, 0)->toDateTimeString(); 
                $dates['end'] = \Carbon\Carbon::create(null, $date, 1, 0, 0, 0)->addMonth()->toDateTimeString();
            }
            else
            {
                $dates['start'] = \Carbon\Carbon::create(1970, 1, 1, 0, 0, 0)->toDateTimeString(); 
                $dates['end'] = \Carbon\Carbon::now()->toDateTimeString();
            }
        return $dates;
    }

    public static function getMimeTypes($type)
    {
        $allMimes = array_flip(Uploadyoda::getMimes());

        $allowedExt = Config::get('uploadyoda::allowed_mime_types');
        $allowedMimeTypes = array();
        foreach ( $allowedExt as $ext )
        {
            if ( isset( $allMimes[$ext] ) )
                array_push($allowedMimeTypes, $allMimes[$ext]); 
        }
        
        $filterMimeTypes = array(); 
        foreach($allowedMimeTypes as $mimeType)
        {
            if ( strpos($mimeType, $type) !== false )
               array_push($filterMimeTypes, $mimeType); 
        }
        return $filterMimeTypes;
    }
}
