<?php namespace Quasimodal\Uploadyoda\Service;

use Carbon,
    Config,
    Uploadyoda;

class Filter 
{
    public function getSearchDates($date)
    {
        $dates = array();
    
            if ( $date )
            {
                $dates['start'] = Carbon\Carbon::create(null, $date, 1, 0, 0, 0)->toDateTimeString(); 
                $dates['end'] = Carbon\Carbon::create(null, $date, 1, 0, 0, 0)->addMonth()->toDateTimeString();
            }
            else
            {
                $dates['start'] = Carbon\Carbon::create(1970, 1, 1, 0, 0, 0)->toDateTimeString(); 
                $dates['end'] = Carbon\Carbon::now()->toDateTimeString();
            }
        return $dates;
    }

    public function getSearchMimeTypes($type)
    {
        $allMimes = array_flip(Uploadyoda::getMimes());
        $allowedExt = Config::get('uploadyoda::allowed_mime_types');

        $allowedMimeTypes = array();
        foreach ( $allowedExt as $ext )
        {
            if ( isset( $allMimes[$ext] ) )
                array_push($allowedMimeTypes, $allMimes[$ext]); 
        }
        // all types should be returned
        if ( !$type )
            return $allowedMimeTypes;
        // we have a type so return mimes of that type 
        $filterMimeTypes = array(); 
        foreach($allowedMimeTypes as $mimeType)
        {
            if ( strpos($mimeType, $type) !== false )
                array_push($filterMimeTypes, $mimeType); 
        }
        return $filterMimeTypes;
    }
}
