@extends('uploadyoda::layouts.master')
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/upload.js') }}"></script>

@section('content')
<?php /*
    <div id="content-inner">
        {{ Form::open(array('action' => 'Quasimodal\Uploadyoda\UploadsController@store', 'files' => true, 'enctype' => 'multipart/form-data')) }}
        <?php echo '<div class="form_field">' . Form::file('file') . '<span class="admin_error">' . $errors->first('file') . '</span></div>'; ?>
        <?php echo '<div class="form_field">' . Form::submit('Upload file', array('class' => 'btn btn-large')) . '</div>'; ?>
        {{ Form::close() }}
    </div>
 */?>

<div id="content-inner">

    <div id="drop-area">
       Drop files here 
    </div>
    <div id="downloads"></div>

</div>

@stop
