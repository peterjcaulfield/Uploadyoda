<?php


Route::get('uploadyoda_user/welcome', 'Quasimodal\Uploadyoda\UploadyodaUsersController@welcome');
Route::get('uploadyoda_user/create', 'Quasimodal\Uploadyoda\UploadyodaUsersController@create');
Route::post('uploadyoda_user/store', 'Quasimodal\Uploadyoda\UploadyodaUsersController@store');
Route::get('uploadyoda_user/login', 'Quasimodal\Uploadyoda\UploadyodaUsersController@login');
Route::post('uploadyoda_user/login', 'Quasimodal\Uploadyoda\UploadyodaUsersController@login');

Route::get('uploadyoda', array('as' => 'uploadyodaHome', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@index'));
Route::get('uploadyoda/upload', array('as' => 'uploadyodaUpload', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@create'));
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\UploadsController@test');
Route::post('uploadyoda/delete', 'Quasimodal\Uploadyoda\UploadsController@destroy');
