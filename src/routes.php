<?php

Route::filter('uploadyodaAuth', function(){
    if ( Quasimodal\Uploadyoda\models\UploadyodaUser::count() )
    {
        if ( Auth::guest() ) return Redirect::guest('uploadyoda_user/login');
    }
    else
        return Redirect::to('uploadyoda_user/welcome');
});


Route::get('uploadyoda_user/welcome', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@welcome');
Route::get('uploadyoda_user/create', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@create');
Route::post('uploadyoda_user/store', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@store');
Route::get('uploadyoda_user/login', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@login');
Route::post('uploadyoda_user/login', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@attemptLogin');
Route::get('uploadyoda_user/logout', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@logout');



Route::get('uploadyoda', array('as' => 'uploadyodaHome', 'uses' => 'Quasimodal\Uploadyoda\controllers\UploadsController@index'));
Route::get('uploadyoda/upload', array('as' => 'uploadyodaUpload', 'uses' => 'Quasimodal\Uploadyoda\controllers\UploadsController@create'));
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\controllers\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\controllers\UploadsController@test');
Route::post('uploadyoda/delete', 'Quasimodal\Uploadyoda\controllers\UploadsController@destroy');
