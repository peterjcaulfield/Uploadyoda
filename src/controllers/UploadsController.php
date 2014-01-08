<?php namespace Quasimodal\Uploadyoda; 

use BaseController, View, Redirect, Uploadyoda;

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
        $response = Uploadyoda::upload('file');

        $this->upload->create( $response ); 

        return Redirect::to('uploadyoda/upload')->with('success', 'uploaded successfully');
    }

    public function destroy($id)
    {

    }

    public function test()
    {
        return View::make('uploadyoda::test');
    }
}
