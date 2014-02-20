<?php namespace Quasimodal\Uploadyoda;

interface UploadRepositoryInterface
{
    public function getAllUploads();

    public function getAllUploadsWithFilter($filter);

    public function count();

    public function create($upload);

}
