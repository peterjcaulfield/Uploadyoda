<?php namespace Quasimodal\Uploadyoda\repositories;

interface UploadRepositoryInterface
{
    public function getAllUploads();

    public function getUploadById($id);

    public function setQueryFilters(array $filters);

    public function count();

    public function create(array $upload);

    public function destroy($id);

    public function setPaginate($num);
}
