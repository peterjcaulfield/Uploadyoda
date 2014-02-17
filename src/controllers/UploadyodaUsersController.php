<?php namespace Quasimodal\Uploadyoda;

use BaseController, Input, View, Redirect, Config, Request, Uploadyoda;

class UploadyodaUsersController extends BaseController 
{
    protected $uploadyodaUser;
    public $layout;

    public function __construct( UploadyodaUser $uploadyodaUser )
    {
        $this->uploadyodaUser = $uploadyodaUser;
        $this->layout = 'uploadyoda::layouts.welcome';
    }

    public function welcome()
    {
        return View::make('uploadyoda::welcome');
    }  
}
