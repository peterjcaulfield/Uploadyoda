<?php namespace Quasimodal\Uploadyoda\repositories;

use Quasimodal\Uploadyoda\models\Upload,
    Filter;

class EloquentUploadRepository implements UploadRepositoryInterface
{
    protected $paginate = 10;
    protected $filter = false;
    protected $model;

    public function __construct( Upload $model )
    {
        $this->model = $model;
    }
    
    protected function createFilterQuery()
    {
        $searchDates = Filter::getSearchDates($this->filter['date']);

        $mimes = Filter::getSearchMimeTypes($this->filter['type']);
        
        $filterQuery = $this->model->where('name', 'LIKE', '%' . $this->filter['search'] . '%')
            ->whereIn('mime_type', $mimes)
            ->where('created_at', '>=', $searchDates['start'])
            ->where('created_at', '<=', $searchDates['end']);
    
        return $filterQuery; 
    }

    protected function queryUploads($filterQuery=false, $orderBy='created_at', $direction='asc')
    {
        return $filterQuery ? $filterQuery->orderBy($orderBy, $direction) : $this->model->orderBy($orderBy, $direction); 
    }
    
    protected function performQuery($query)
    {
       return $this->paginate === false ? $query->get() : $query->paginate($this->paginate); 
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
            $filterQuery = $this->createFilterQuery();

            return $this->performQuery($this->queryUploads($filterQuery));
        }

        return $this->performQuery($this->queryUploads());
    }

    public function count()
    {
        return $this->model->count();
    }
}
