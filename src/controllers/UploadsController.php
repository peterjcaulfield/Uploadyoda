<?php namespace Quasimodal\Uploadyoda; 

use BaseController, Input, View, Redirect, Config, Request, Uploadyoda;

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

            if ( $filters['date'] )
            {
                $start = \Carbon\Carbon::create(null, $filters['date'], 1, 0, 0, 0)->toDateTimeString(); 
                $end = \Carbon\Carbon::create(null, $filters['date'], 1, 0, 0, 0)->addMonth()->toDateTimeString();
            }
            else
            {
                $start = \Carbon\Carbon::create(1970, 1, 1, 0, 0, 0)->toDateTimeString(); 
                $end = \Carbon\Carbon::now()->toDateTimeString();
            }
            
            $searchQuery = $filters['search'];
            $mimes = $filters['type'] === "0" ? array_reduce(array_values(Uploadyoda::getMimes()), "array_merge", array()) : Uploadyoda::getMimes()[$filters['type']];

            $uploads = $this->upload->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->whereIn('mime_type', $mimes)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            View::share(array('uploads' => $uploads, 'pageTitle'=>'Uploads', 'icon' => 'fa-home', 'count' => $this->upload->count()));
            $this->layout->content = View::make('uploadyoda::home');
        }
        else
        {
            $uploads = $this->upload->orderBy('created_at', 'desc')->paginate(10);
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
        return View::make('uploadyoda::test');
    }
}
