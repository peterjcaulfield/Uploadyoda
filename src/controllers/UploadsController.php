<?php namespace Quasimodal\Uploadyoda; 

use BaseController, View;

class UploadsController extends BaseController
{

    protected $upload;

    public function __construct( Upload $upload )
    {
        $this->upload = $upload;
    }

    public function index()
    {
        return View::make('uploadyoda::home');
    }

    public function create()
    {
        return View::make('uploadyoda::upload');
    }

    public function store()
    {
       $this->upload->create( array('name' => 'test', 'type' => 'test', 'size' => '1000') ); 
    }

    public function destroy($id)
    {

    }
}
