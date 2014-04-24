<?php namespace Quasimodal\Uploadyoda\Service;

use Carbon,
    Config,
    Uploadyoda,
    Model;

class Filter
{

    protected $query;

    protected $filters = [
        'date' => ['today', 'week', 'month', 'year'],
        'format' => ['audio', 'video', 'pdf', 'image']
            ];

    public function setQueryModel($model)
    {
        $this->query = $model->newQuery();
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
}
