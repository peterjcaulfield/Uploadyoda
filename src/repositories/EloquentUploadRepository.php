<?php namespace Quasimodal\Uploadyoda\repositories;

use Quasimodal\Uploadyoda\models\Upload,
    Quasimodal\Uploadyoda\models\ImageMeta,
    Quasimodal\Uploadyoda\models\PdfMeta,
    Carbon,
    Uploadyoda,
    Config;

class EloquentUploadRepository implements UploadRepositoryInterface
{
    protected $paginate = 10;

    protected $model;

    protected $query;

    protected $queryFilters = false;

    protected $filters = [
        'date' => ['today', 'week', 'month', 'year'],
        'format' => ['audio', 'video', 'pdf', 'image']
            ];

    public function __construct( Upload $model )
    {
        $this->model = $model;
    }

    protected function createFilterQuery()
    {
        // initialise the query
        $this->query = $this->model->newQuery();

        $this->buildFilteredQuery($this->queryFilters);

        return $this->getFilteredQuery();
    }

    public function getFilteredQuery()
    {
        return $this->query;
    }

    public function filterSearch($search)
    {
        $this->query->where('name', 'LIKE', '%' . $search . '%');
    }

    public function buildFilteredQuery($raw)
    {
        if ( isset($raw['filters'] ) )
        {
            $filters = explode(',', $raw['filters'] );

            $dateFilter = array_intersect($filters, $this->filters['date']);

            if  ( $date = reset($dateFilter) )
            {
                $this->filterDate($date);
            }

            $formatFilters = array_intersect($filters, $this->filters['format']);

            if ( count($formatFilters) )
            {
                $this->filterFormat($formatFilters);
            }
        }

        if ( isset($raw['search']) )
        {
            $this->filterSearch($raw['search']);
        }
    }

    protected function filterDate($date)
    {
        switch($date)
        {
            case 'today':
                $this->query->where('created_at', '>=', Carbon\Carbon::today())
                            ->where('created_at', '<=', Carbon\Carbon::tomorrow());
                break;
            case 'week':
                $this->query->where('created_at', '>=', Carbon\Carbon::now()->subWeek())
                            ->where('created_at', '<=', Carbon\Carbon::tomorrow());
                break;
            case 'month':
                $this->query->where('created_at', '>=', Carbon\Carbon::now()->subMonth())
                            ->where('created_at', '<=', Carbon\Carbon::tomorrow());
                break;
            case 'year':
                $this->query->where('created_at', '>=', Carbon\Carbon::now()->subYear())
                            ->where('created_at', '<=', Carbon\Carbon::tomorrow());
                break;
        }

    }

    protected function filterFormat($formats)
    {
        $this->query->whereIn('mime_type', $this->getAllowableFormats($formats));
    }

    protected function getAllowableFormats($formats)
    {
        $allMimes = array_flip(Uploadyoda::getMimes());
        $allowedExt = Config::get('uploadyoda::allowed_mime_types');

        $allowedMimeTypes = array();
        foreach ( $allowedExt as $ext )
        {
            if ( isset( $allMimes[$ext] ) )
                array_push($allowedMimeTypes, $allMimes[$ext]);
        }
        // all types should be returned
        if ( !$formats )
            return $allowedMimeTypes;
        // we have a type so return mimes of that type
        $filterMimeTypes = array();
        foreach( $formats as $format )
        {
            foreach( $allowedMimeTypes as $mimeType )
            {
                if ( strpos($mimeType, $format) !== false )
                    array_push($filterMimeTypes, $mimeType);
            }
        }
        return $filterMimeTypes;
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

    public function setQueryFilters(array $filters)
    {
       $this->queryFilters = $filters;
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
