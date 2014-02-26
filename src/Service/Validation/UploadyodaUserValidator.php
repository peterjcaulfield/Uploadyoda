<?php namespace Quasimodal\Uploadyoda\Service\Validation;

class UploadyodaUserValidator extends AbstractValidator
{
    protected $rules = array(
        'firstname'=>'required|alpha|min:2',
        'lastname'=>'required|alpha|min:2',
        'email'=>'required|email|unique:uploadyoda_users',
        'password'=>'required|alpha_num|between:6,12|confirmed',
        'password_confirmation'=>'required|alpha_num|between:6,12'
    );
}
