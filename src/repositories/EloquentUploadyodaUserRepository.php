<?php namespace Quasimodal\Uploadyoda;

class EloquentUploadyodaUserRepository implements UploadyodaUserRepositoryInterface
{
    public function __construct( UploadyodaUser $model )
    {
        $this->model = $model;
    }

    public function getValidatorRules()
    {
        return UploadyodaUser::$rules;
    }
    
    public function create($user)
    {
       $this->model->create($user); 
    }
}
