<?php

Route::get('uploadyoda', array('as' => 'uploadyodaHome', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@index'));
Route::get('uploadyoda/upload', array('as' => 'uploadyodaUpload', 'uses' => 'Quasimodal\Uploadyoda\UploadsController@create'));
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\UploadsController@test');
Route::post('uploadyoda/delete', 'Quasimodal\Uploadyoda\UploadsController@destroy');
