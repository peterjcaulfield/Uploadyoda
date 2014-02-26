<?php namespace Quasimodal\Uploadyoda\repositories;

use Hash,
    Quasimodal\Uploadyoda\models\UploadyodaUser as UploadyodaUser;

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
       $user['password'] = Hash::make($user['password']);

       $this->model->create($user); 
    }
}
