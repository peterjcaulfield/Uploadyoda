<?php namespace Quasimodal\Uploadyoda;

use BaseController, Input, View, Redirect, Config, Request, Validator, Hash, Auth, Uploadyoda;

class UploadyodaUsersController extends BaseController 
{
    protected $uploadyodaUser;
    public $layout;

    public function __construct( UploadyodaUser $uploadyodaUser )
    {
        $this->uploadyodaUser = $uploadyodaUser;
        $this->layout = 'uploadyoda::layouts.welcome';
    }

    public function welcome()
    {
        return View::make('uploadyoda::welcome');
    }  

    public function store()
    {
        $validator = Validator::make(Input::all(), $this->uploadyodaUser->rules);
        if ( $validator->passes() )
        {
            $user = Input::all();
            $user['password'] = Hash::make($user['password']);
            $this->uploadyodaUser->create($user);
            return Redirect::to('uploadyoda_user/login')->with('success', 'You have registered successfully! You will be able to login as soon as your account has been activated.');
        }
        else
            return Redirect::to('uploadyoda_user/welcome')->with('danger', 'shit happened')->withErrors($validator)->withInput();
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
