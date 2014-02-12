<?php namespace Quasimodal\Uploadyoda; 

use BaseController, Input, View, Redirect, Uploadyoda;

class UploadsController extends BaseController
{

    protected $upload;

    public function __construct( Upload $upload )
    {
        $this->upload = $upload;
    }

    public function index()
    {
        return View::make('uploadyoda::home')->with(array('uploads'=>$this->upload->paginate(10), 'pageTitle'=>'Uploads', 'icon' => 'fa-home'));
    }

    public function create()
    {
        return View::make('uploadyoda::upload')->with(array('pageTitle' => 'Upload file', 'icon' => 'fa-upload'));
    }

    public function store()
    {
        try 
        {
            $response = Uploadyoda::upload('file');
            $this->upload->create( $response ); 
            return 'success';
        }
        catch ( UploadyodaException $e )
        {
            return $e->getMessage();
        }

    }

    public function destroy($id)
    {

    }

    public function test()
    {
        return View::make('uploadyoda::test');
    }
}
