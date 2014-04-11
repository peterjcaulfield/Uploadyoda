<?php namespace Quasimodal\Uploadyoda\repositories;

use Quasimodal\Uploadyoda\models\Upload,
    Quasimodal\Uploadyoda\models\ImageMeta,
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
        if ( $this->filter )
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
