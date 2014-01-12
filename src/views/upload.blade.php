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
       Drop files to upload here 
    </div>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="downloads">
    <th id="name-header" align="left">Name</th>
    <th id="size-header" align="left">Size</th>
    <th id="progress-header" align="left">Progress</th>
    <th id="complete-header" align="left">Complete</th>
    <th id="status-header" align="left">Status</th>
    </table>

</div>

@stop
