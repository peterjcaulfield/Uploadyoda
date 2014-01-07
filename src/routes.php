<?php

Route::get('uploadyoda', 'Quasimodal\Uploadyoda\UploadsController@index');
Route::get('uploadyoda/upload', 'Quasimodal\Uploadyoda\UploadsController@create');
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
