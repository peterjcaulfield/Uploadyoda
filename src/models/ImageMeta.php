<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent;

class ImageMeta extends Eloquent
{
    protected $table = 'image_meta';

    protected $fillable = array('title', 'altText', 'caption', 'description', 'height', 'width');

    public function uploads()
    {
        return $this->morphMany('Quasimodal\Uploadyoda\models\Upload', 'metable');
    }
}
