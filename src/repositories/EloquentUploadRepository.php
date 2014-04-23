<?php namespace Quasimodal\Uploadyoda\repositories;

use Quasimodal\Uploadyoda\models\Upload,
    Quasimodal\Uploadyoda\models\ImageMeta,
    Quasimodal\Uploadyoda\models\PdfMeta,
    Quasimodal\Uploadyoda\Service\Filter;

class EloquentUploadRepository implements UploadRepositoryInterface
{
    protected $paginate = 10;
    protected $filters = false;
    protected $filter;
    protected $model;

    public function __construct( Upload $model, Filter $filter )
    {
        $this->model = $model;
        $this->filter = $filter;
        $this->filter->setQueryModel($this->model);
    }

    protected function createFilterQuery()
    {
        if ( isset($this->filters['filters']) )
        {
            $filters = explode(',', $this->filters['filters']);

            foreach( $filters as $filter )
            {
                $this->filter->buildQueryWithFilters($filter);
            }
        }

        if ( isset($this->filters['search']) )
        {
            $this->filter->buildQueryWithSearch($this->filters['search']);
        }

        return $this->filter->getFilteredQuery();
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
       $this->filters = $filter;
       return $this;
    }

    public function create(array $upload)
    {
       $meta = $this->createMetaInstanceFromMime($upload['upload']['mime_type'], $upload['meta']);

       if ($meta)
       {
           $this->model->fill($upload['upload']);
           return $meta->uploads()->save($this->model);
       }
       else
           return $this->model->create($upload['upload']);
    }

    public function destroy($id)
    {
       return $this->model->destroy($id);
    }

    public function getAllUploads()
    {

        if ( $this->filters )
        {
            $filterQuery = $this->createFilterQuery();

            return $this->performQuery($this->queryUploads($filterQuery));
        }

        return $this->performQuery($this->queryUploads());
    }

    public function getUploadById($id)
    {
        return $this->model->with('metable')->find($id);
    }

    public function count()
    {
        return $this->model->count();
    }

    public function createMetaInstanceFromMime($mime, $attr)
    {
       switch($mime)
       {
           case (strpos($mime, 'image') !== false):
               return ImageMeta::create($attr);
           case (strpos($mime, 'pdf') !== false):
               return PdfMeta::create($attr);
           default:
               return false;
       }
    }

    public function update($id, $attr)
    {
        // no values (empty strings) should be saved as null
        static::formatNullAttributes($attr['meta']);

        return $this->model->find($id)->metable->fill($attr['meta'])->save();
    }

    public function testMeta()
    {
        return ImageMeta::create([]);
    }

    private static function formatNullAttributes(&$attr)
    {
        foreach( $attr as $key => &$value )
            if ( $value == '' )
                $value = NULL;
    }
}
