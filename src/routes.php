<?php

Route::get('uploadyoda', 'Quasimodal\Uploadyoda\UploadsController@index');
Route::get('uploadyoda/upload', 'Quasimodal\Uploadyoda\UploadsController@create');
Route::post('uploadyoda/store', 'Quasimodal\Uploadyoda\UploadsController@store');
Route::get('uploadyoda/test', 'Quasimodal\Uploadyoda\UploadsController@test');
Route::get('uploadyoda/func', function(){

    echo DB::table('uploads')->where('name', '=', 'Addison.Wesley.Growing.Object.Oriented.Software.Guided.by.Tests.Oct.2009 (1)-1.pdf')->count();

});

Route::get('uploadyoda/func', function(){

        $filename = 'keypad-2';
        $ext = 'png';    
        // first we replace any spaces with hyphens

        $filename = preg_replace('/\s+/', '-', $filename);

        if ( DB::table('uploads')->where( 'name', '=', $filename . '.' . $ext )->count() )
        {   // if filename ends in hyphen(s) or hyphen(s) with number(s) remove these and add a single hyphen at the end 
            if ( preg_match( '/-+(\d+)?$/', $filename, $match ) )
                $filename = substr($filename, 0, -(strlen($match[0]))) . '-';
            else
              $filename = $filename . '-';  
            
            // check if there is a versioned filename in the database already and retrieve the highest versioned filename 
            $existing_versioned = DB::table('uploads')->select('name')
                ->where( 'name', 'like', $filename . '_%.%' ) // wildcards appended to query for one or more characters after the hyphen
                ->orderBy('name', 'desc')
                ->take(1)
                ->get();
            // check for 
            if ( count( $existing_versioned ) && preg_match('/\d+$/', pathinfo( $existing_versioned[0]->name, PATHINFO_FILENAME ) ) )
            {
                $last_versioned = pathinfo( $existing_versioned[0]->name, PATHINFO_FILENAME );
                preg_match('/\d+$/', $last_versioned, $match );
                if ( count( $match ) ){
                    $version_number = ++$match[0];

                    return $filename . $version_number;
                }
            }
            else // no versioned filenames exist yet so we can create the first
                return $filename . 1;
        }// no record exists with current filename to return it for db insertion
        
        return $filename; 

});
