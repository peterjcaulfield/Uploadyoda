@extends('uploadyoda::layouts.master')

@section('content')

<div id="content-inner">

    <div id="drop-area">
       Drop files to upload here 
    </div>
    <p><i class=" fa fa-upload"></i>&nbsp;&nbsp;File uploads</p>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="downloads" class="table table-condensed table-bordered">
      <tbody id="downloadsBody">
        <tr>
          <th id="name-header" align="left">Name</th>
          <th id="size-header" align="left">Size</th>
          <th id="progress-header" align="left">Progress</th>
          <th id="complete-header" align="left">Complete</th>
          <th id="status-header" align="left">Status</th>
        </tr>
      </tbody>
    </table>

</div>

@stop

@section('footer')
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/upload.js') }}"></script>
@stop
