<?php namespace Quasimodal\Uploadyoda; 

use Input, View, Redirect, Config, Request, Uploadyoda, Quasimodal\Uploadyoda\EloquentUploadRepository as Upload;

class UploadsController extends BaseController
{
    protected $softDelete = true;
    protected $upload;
    public $layout;

    public function __construct( Upload $upload )
    {
        $this->upload = $upload;
        $this->layout = Config::get('uploadyoda::layout');
        //$this->beforeFilter('csrf', array('on'=>'post'));
        $this->beforeFilter('uploadyodaAuth');
    }

    public function index()
    {
        if ( count(Request::query()) > 1 )
        {
            $filters = Input::all();

            $uploads = $this->upload->getAllUploadsWithFilter($filters);
            
            View::share(array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home', 'count' => $this->upload->count()));
            $this->layout->content = View::make('uploadyoda::home');
        }
        else
        {
            $uploads = $this->upload->getAllUploads();
            View::share(array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home', 'count' => $this->upload->count()));
            $this->layout->content = View::make('uploadyoda::home');
        }
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
        dd($this->upload->getAllUploads());
    }
}
