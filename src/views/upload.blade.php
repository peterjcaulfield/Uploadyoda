
@section('content')
<div id="sectionHeaderContainer">
    <h1 class="sectionText"><i class="fa fa-upload"></i>&nbsp;&nbsp;Upload</h1>
</div>
<div id="content-inner">

    <div id="drop-area">
       Drop files to upload here
    </div>
    <div id="uploads">
        <p><i class=" fa fa-upload"></i>&nbsp;&nbsp;File uploads</p>
        <ul id="uploadTabs" class="nav nav-tabs">
            <li><a href="#progressUploads" data-toggle="tab">Progress <div class="uploadStats"><span class="uploadCount" id="progressCount"></span></div></a></li>
            <li><a href="#successfulUploads" data-toggle="tab">Successful <div class="uploadStats"><span class="uploadCount" id="successCount"></span></div></a></li>
            <li><a href="#failedUploads" data-toggle="tab">Failed <div class="uploadStats"><span class="uploadCount" id="failCount"></span></div></a></li>
        </ul>
        <div class="tab-content">
            <div id="progressUploads" class="tab-pane">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="uploadsTable" class="uploads-table table table-condensed table-bordered">
                    <tbody id="progressUploadsTableBody">
                    <tr>
                    <th id="name-header" align="left">Name</th>
                    <th id="size-header" align="left">Size</th>
                    <th id="status-header" align="left">Complete</th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="successfulUploads" class="tab-pane">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="uploads-table table table-condensed table-bordered">
                    <tbody id="sucessfulUploadsTableBody">
                    <tr>
                    <th id="name-header" align="left">Name</th>
                    <th id="size-header" align="left">Size</th>
                    <th id="status-header" align="left">Action</th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="failedUploads" class="tab-pane">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="uploads-table table table-condensed table-bordered">
                    <tbody id="failedUploadsTableBody">
                    <tr>
                    <th id="name-header" align="left">Name</th>
                    <th id="size-header" align="left">Size</th>
                    <th id="status-header" align="left">Error</th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('footer')
<script>
$(function(){
    $('#uploadTabs a:first').tab('show');
});
/**
 * vars that upload.js depend on
 */

var configMaxFilesize = "<?php echo Config::get('uploadyoda::max_file_size'); ?>";
var serverMaxFilesize = "<?php echo Uploadyoda::returnBytes(ini_get('post_max_size')); ?>";
var mimes = <?php echo json_encode(Config::get('uploadyoda::allowed_mime_types')); ?>;
var defaultExtensions = <?php echo json_encode(Uploadyoda::getMimes()); ?>;
var csrf_token = "<?php echo csrf_token(); ?>"
</script>
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/uploadyoda.js') }}"></script>
@stop
