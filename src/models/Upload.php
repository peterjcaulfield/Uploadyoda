<?php namespace Quasimodal\Uploadyoda;

use Eloquent;

class Upload extends Eloquent
{
    protected $table = 'uploads';

    protected $fillable = array( 'name', 'type', 'size' );
}
