<?php namespace Quasimodal\Uploadyoda\controllers;

use Input,
    View,
    Redirect,
    Config,
    Request,
    Uploadyoda,
    Quasimodal\Uploadyoda\repositories\UploadRepositoryInterface as UploadRepositoryInterface,
    Quasimodal\Uploadyoda\Service\Validation\UploadyodaValidator;

class UploadsController extends BaseController
{
    protected $softDelete = true;
    protected $upload;
    public $layout;

    public function __construct( UploadRepositoryInterface $upload, UploadyodaValidator $validator )
    {
        $this->upload = $upload;
        $this->validator = $validator;
        $this->layout = Config::get('uploadyoda::layout');
        $this->beforeFilter(Config::get('uploadyoda::auth'));
        //$this->beforeFilter('emptyFiles', array('only' => 'store'));
        $this->beforeFilter('csrf', array('on'=>'post'));
    }

    public function index()
    {
        if ( count(Request::query()) > 1 )
        {
            $filter = Input::all();
            $uploads = $this->upload->setFilter($filter)->getAllUploads();
            return View::make('uploadyoda::home', array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home', 'count' => $this->upload->count()));
        }
        else
        {
            $uploads = $this->upload->getAllUploads();
            return View::make('uploadyoda::home', array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home', 'count' => $this->upload->count()));
        }
    }

    public function create()
    {
        $this->layout->with(array('pageTitle' => 'Upload file', 'icon' => 'fa-upload'));
        $this->layout->content = View::make('uploadyoda::upload');
    }

    public function store()
    {
        if ( true || $this->validator->with(Input::all() )->valid('upload') )
        {
            $upload = Uploadyoda::upload(Input::file('file'));
            $this->upload->create($upload);
            return 'success';
        }
        else
        {
            return $this->validator->errors()->first();
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
        dd( Input::all() );
    }

    public function update($id)
    {
        if ( $this->upload->update($id, Input::all()) )
            return json_encode(['code' => 200, 'status' => 'updated successfully']);
        else
            return json_encode(['code' => 500, 'status' => 'update failed']);
    }

    public function edit($id=null)
    {
        $upload = $this->upload->getUploadById($id);

        if ( $upload )
        {
            switch ( $upload->mime_type )
            {
                case (strpos($upload->mime_type, 'image') !== false):
                    return View::make('uploadyoda::edit-image', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
                    break;
                case ($upload->mime_type == 'application/pdf'):
                    return View::make('uploadyoda::edit-pdf', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
                    break;
                default:
                    return View::make('uploadyoda::edit', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
            }
        }
        else
            return View::make('uploadyoda::404');
    }

    public function view($id=null)
    {
        $upload = $this->upload->getUploadById($id);

        if ( $upload )
        {
            switch ( $upload->mime_type )
            {
                case (strpos($upload->mime_type, 'image') !== false):
                    return View::make('uploadyoda::view-image', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
                    break;
                case ($upload->mime_type == 'application/pdf'):
                    return View::make('uploadyoda::view-pdf', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
                    break;
                default:
                    return View::make('uploadyoda::view', array('upload' => $upload, 'path' => '/' . $upload->path . '/' . $upload->name));
            }
        }
        else
            return View::make('uploadyoda::404');
    }
}
