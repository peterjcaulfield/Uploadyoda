<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent;

class Upload extends Eloquent
{
    protected $table = 'image_meta';

    protected $fillable = array('title', 'altText', 'caption', 'description', 'height', 'width');

    public function uploads()
    {
        return $this->morphMany('Upload', 'metable');
    }
}
