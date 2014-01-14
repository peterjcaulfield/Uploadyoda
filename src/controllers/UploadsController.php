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
        return View::make('uploadyoda::home')->with(array('uploads'=>$this->upload->paginate(10), 'pageTitle'=>'Uploads'));
    }

    public function create()
    {
        return View::make('uploadyoda::upload')->with('pageTitle', 'Upload file');
    }

    public function store()
    {
        $response = Uploadyoda::upload('file');

        if ( is_array( $response ) )
        {
            $this->upload->create( $response ); 
            return 'success';
        }
        else
            return $response;

    }

    public function destroy($id)
    {

    }

    public function test()
    {
        return View::make('uploadyoda::test');
    }
}
