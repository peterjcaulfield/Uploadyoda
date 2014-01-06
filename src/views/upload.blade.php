@extends('uploadyoda::layouts.master')

@section('content')

upload form

{{ Form::open(array('action' => 'UploadsController@storeUpload', 'files' => true, 'enctype' => 'multipart/form-data')) }

{{ Form::close }}

@stop
