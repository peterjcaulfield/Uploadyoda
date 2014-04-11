<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent;

class PdfMeta extends Eloquent
{
    protected $table = 'pdf_meta';

    protected $fillable = array('title', 'author', 'published', 'caption', 'description');

    public function uploads()
    {
        return $this->morphMany('Quasimodal\Uploadyoda\models\Upload', 'metable');
    }
}
