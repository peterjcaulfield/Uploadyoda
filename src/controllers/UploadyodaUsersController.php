<?php namespace Quasimodal\Uploadyoda\controllers;

use Input, 
    View, 
    Redirect, 
    Config, 
    Request, 
    Validator, 
    Hash, 
    Auth,
    Quasimodal\Uploadyoda\repositories\UploadyodaUserRepositoryInterface as UploadyodaUserRepositoryInterface,
    Quasimodal\Uploadyoda\Service\Validation\UploadyodaUserValidator;

class UploadyodaUsersController extends BaseController 
{
    protected $uploadyodaUser;
    public $layout;

    public function __construct( UploadyodaUserRepositoryInterface $uploadyodaUser, UploadyodaUserValidator $validator )
    {
        $this->uploadyodaUser = $uploadyodaUser;
        $this->validator = $validator;
        $this->layout = 'uploadyoda::layouts.welcome';
        $this->beforeFilter('csrf', array('on'=>'post'));
    }

    public function welcome()
    {
        return View::make('uploadyoda::welcome');
    }  

    public function store()
    {
        if ( $this->validator->with(Input::all())->passes() )
        {
            $user = Input::all();
            $this->uploadyodaUser->create($user);
            return Redirect::to('uploadyoda_user/login')->with('success', 'You have registered successfully! You will be able to login as soon as your account has been activated.');
        }
        else
            return Redirect::to('uploadyoda_user/welcome')->with('danger', 'shit happened')->withErrors($this->validator->errors())->withInput();
    }
    
    public function login()
    {
        return View::make('uploadyoda::login');
    }

    public function attemptLogin()
    {
        if ( Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password'))) )
            return Redirect::to('uploadyoda/upload');
        else
            return Redirect::to('uploadyoda_user/login')->with('danger', 'username or password incorrect');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('uploadyoda_user/login');
    }
}
