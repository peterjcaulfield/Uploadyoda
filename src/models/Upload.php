<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent,
    Quasimodal\Uploadyoda\Uploadyoda as Uploadyoda;

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
        return $type === "0" ? array_reduce(array_values(Uploadyoda::getMimes()), "array_merge", array()) : Uploadyoda::getMimes()[$type];
    
    }
}
