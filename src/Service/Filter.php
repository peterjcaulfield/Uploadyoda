<?php namespace Quasimodal\Uploadyoda\Service;

use Carbon,
    Config,
    Uploadyoda,
    Model;

class Filter
{

    protected $query;

    protected static $filters = [
        'date' => ['today', 'week', 'month', 'year']
        ];

    public function setQueryModel($model)
    {
        $this->query = $model->newQuery();
    }

    public function getFilteredQuery()
    {
        return $this->query;
    }

    public function buildQueryWithSearch($search)
    {
        $this->query->where('name', 'LIKE', '%' . $search . '%');
    }

    public function buildQueryWithFilters($filter)
    {
        if ( in_array( $filter, static::$filters['date'] ) )
            $this->filterDate($filter);
        else
            $this->filterFormat($filter);
    }

    protected function filterDate($filter)
    {
        switch($filter)
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

    protected function filterFormat($filter)
    {
        $this->query->whereIn('mime_type', $this->getAllowableFormats($filter));
    }

    protected function getAllowableFormats($filter)
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
        if ( !$filter )
            return $allowedMimeTypes;
        // we have a type so return mimes of that type
        $filterMimeTypes = array();
        foreach($allowedMimeTypes as $mimeType)
        {
            if ( strpos($mimeType, $filter) !== false )
                array_push($filterMimeTypes, $mimeType);
        }
        return $filterMimeTypes;
    }
}
