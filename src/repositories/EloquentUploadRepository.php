<?php namespace Quasimodal\Uploadyoda\repositories;

use Quasimodal\Uploadyoda\models\Upload,
    Filter;

class EloquentUploadRepository implements UploadRepositoryInterface
{
    protected $paginate = 10;
    protected $filter = false;

    public function __construct( Upload $model )
    {
        $this->model = $model;
    }

    public function setPaginate($num)
    {
        $this->paginate = $num;
    }

    public function setFilter(array $filter)
    {
       $this->filter = $filter; 
       return $this;
    }
    
    public function create(array $upload)
    {
       $this->model->create($upload); 
    }

    public function destroy($id)
    {
        $this->model->destroy($id);
    }

    public function getAllUploads()
    {
        if ( $this->filter )
        {
            $searchDates = Filter::getSearchDates($this->filter['date']);

            $mimes = Filter::getSearchMimeTypes($this->filter['type']);

            $uploads = $this->model->where('name', 'LIKE', '%' . $this->filter['search'] . '%')
                ->whereIn('mime_type', $mimes)
                ->where('created_at', '>=', $searchDates['start'])
                ->where('created_at', '<=', $searchDates['end'])
                ->orderBy('created_at', 'desc')
                ->paginate($this->paginate);

            return $uploads;
        }
        return $this->model->orderBy('created_at', 'desc')->paginate($this->paginate);
    }

    public function count()
    {
        return $this->model->count();
    }
}
