<?php namespace Quasimodal\Uploadyoda;

use Hash;

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
