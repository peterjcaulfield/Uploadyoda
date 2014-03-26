<?php namespace Quasimodal\Uploadyoda\models;

use Eloquent,
    Config,
    Uploadyoda;

class Upload extends Eloquent
{
    protected $table = 'uploads';

    protected $fillable = array( 'name', 'path', 'mime_type', 'size' );

    public function metable()
    {
        return $this->morphTo();
    }
}
