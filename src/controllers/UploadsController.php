<?php namespace Quasimodal\Uploadyoda; 

use BaseController, Input, View, Redirect, Config, Uploadyoda;

class UploadsController extends BaseController
{

    protected $softDelete = true;
    protected $upload;
    public $layout;

    public function __construct( Upload $upload )
    {
        $this->upload = $upload;
        $this->layout = Config::get('uploadyoda::layout');
    }

    public function index()
    {
        $uploads = $this->upload->orderBy('created_at', 'desc')->paginate(10);
        View::share(array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home'));
        $this->layout->content = View::make('uploadyoda::home');
    }

    public function create()
    {
        $this->layout->with(array('pageTitle' => 'Upload file', 'icon' => 'fa-upload'));
        $this->layout->content = View::make('uploadyoda::upload');
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

    public function destroy()
    {
        $recordsToTrash = json_decode(Input::get('itemsToTrash'));
        if (count($recordsToTrash))
            $this->upload->destroy($recordsToTrash);
        return Redirect::back();
    }

    public function test()
    {
        return View::make('uploadyoda::test');
    }
}
