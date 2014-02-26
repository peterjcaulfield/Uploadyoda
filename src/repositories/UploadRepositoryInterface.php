<?php namespace Quasimodal\Uploadyoda\repositories;

interface UploadRepositoryInterface
{
    public function getAllUploads();

    public function getAllUploadsWithFilter($filter);

    public function count();

    public function create($upload);

    public function destroy($id);

}
