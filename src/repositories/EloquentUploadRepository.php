<?php namespace Quasimodal\Uploadyoda;

class EloquentUploadRepository implements UploadRepositoryInterface
{

    public function __construct( Upload $model )
    {
        $this->model = $model;
    }
    
    public function create($upload)
    {
       $this->model->create($upload); 
    }

    public function getAllUploads()
    {
        return $this->model->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAllUploadsWithFilter($filters)
    {
        $searchDates = Upload::getSearchDates($filters['date']);

        $mimes = Upload::getMimeTypes($filters['type']);

        $uploads = $this->model->where('name', 'LIKE', '%' . $filters['search'] . '%')
            ->whereIn('mime_type', $mimes)
            ->where('created_at', '>=', $searchDates['start'])
            ->where('created_at', '<=', $searchDates['end'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $uploads;
    }

    public function count()
    {
        return $this->model->count();
    }
}