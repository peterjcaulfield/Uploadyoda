<?php

/**
 * Filters
 */

/**
 * This filter checks if a user has been registered in the app and if not redirects 
 * to the app's welcome screen. If a user has been registered, but the current user 
 * is not logged in, we redirect to the login page.
 */

Route::filter('uploadyodaAuth', function(){

    $UploadyodaUser = App::make('Quasimodal\Uploadyoda\repositories\UploadyodaUserRepositoryInterface');

    if ( $UploadyodaUser->count() )
    {
        if ( Auth::guest() ) return Redirect::guest('uploadyoda_user/login');
    }
    else
        return Redirect::to('uploadyoda_user/welcome');
});

/**
 * An empty $_FILES array can be caused for various reasons during a file upload.
 * Unfortunately some of these causes also cause the $_POST superglobal to be emptied
 * of it's contents (if the upload exceeds the servers max_post_size for instance).
 * This then causes a csrf token mismatch exception to occur masking the true issue.
 * This filter is therefore run before the csrf filter to catch these scenarios and
 * report an error that is more accurate in respect of it's cause.
 */

Route::filter('emptyFiles', function(){
    if ( !count( $_FILES ) )
        return 'Server error'; // should be updated to something generic but instructive 
});

/**
 * Uploadyoda registration/login routes
 */

Route::get('uploadyoda_user/welcome', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@welcome');
Route::get('uploadyoda_user/create', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@create');
Route::post('uploadyoda_user/store', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@store');
Route::get('uploadyoda_user/login', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@login');
Route::post('uploadyoda_user/login', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@attemptLogin');
Route::get('uploadyoda_user/logout', 'Quasimodal\Uploadyoda\controllers\UploadyodaUsersController@logout');

/**
 * Uploadyoda application routes
 */

Route::get('uploadyoda', array('as' => 'uploadyodaHome', 'uses' => 'Quasimodal\Uploadyoda\controllers\UploadsController@index'));
Route::get('uploadyoda/upload', array('as' => 'uploadyodaUpload', 'uses' => 'Quasimodal\Uploadyoda\controllers\UploadsController@create'));
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\controllers\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\controllers\UploadsController@test');
Route::post('uploadyoda/delete', 'Quasimodal\Uploadyoda\controllers\UploadsController@destroy');

