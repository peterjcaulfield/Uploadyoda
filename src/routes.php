<?php

Route::get('uploadyoda', 'Quasimodal\Uploadyoda\UploadsController@index');
Route::get('uploadyoda/upload', 'Quasimodal\Uploadyoda\UploadsController@create');
Route::get('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
