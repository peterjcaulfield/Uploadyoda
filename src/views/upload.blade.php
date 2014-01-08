@extends('uploadyoda::layouts.master')

@section('content')
    <h1>Upload</h1>
    <div id="content-inner">
        {{ Form::open(array('action' => 'Quasimodal\Uploadyoda\UploadsController@store', 'files' => true, 'enctype' => 'multipart/form-data')) }}
        <?php echo '<div class="form_field">' . Form::file('file') . '<span class="admin_error">' . $errors->first('file') . '</span></div>'; ?>
        <?php echo '<div class="form_field">' . Form::submit('Upload file', array('class' => 'btn btn-large')) . '</div>'; ?>
        {{ Form::close() }}
    </div>
</div>
@stop
