<?php

Route::filter(Config::get('uploadyoda::auth'), function(){
    if ( Quasimodal\Uploadyoda\UploadyodaUser::count() )
    {
        if ( Auth::guest() ) return Redirect::guest('uploadyoda_user/login');
    }
    else
        return Redirect::to('uploadyoda_user/welcome');
});


Route::get('uploadyoda_user/welcome', 'Quasimodal\Uploadyoda\UploadyodaUsersController@welcome');
Route::get('uploadyoda_user/create', 'Quasimodal\Uploadyoda\UploadyodaUsersController@create');
Route::post('uploadyoda_user/store', 'Quasimodal\Uploadyoda\UploadyodaUsersController@store');
Route::get('uploadyoda_user/login', 'Quasimodal\Uploadyoda\UploadyodaUsersController@login');
Route::post('uploadyoda_user/login', 'Quasimodal\Uploadyoda\UploadyodaUsersController@attemptLogin');
Route::get('uploadyoda_user/logout', 'Quasimodal\Uploadyoda\UploadyodaUsersController@logout');



Route::get('uploadyoda', array('as' => 'uploadyodaHome', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@index'));
Route::get('uploadyoda/upload', array('as' => 'uploadyodaUpload', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@create'));
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\UploadsController@test');
Route::post('uploadyoda/delete', 'Quasimodal\Uploadyoda\UploadsController@destroy');
