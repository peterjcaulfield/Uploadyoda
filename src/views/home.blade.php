@extends('uploadyoda::layouts.master')

@section('content')
<table id="uploads-index" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<th id="name-header" align="left">Name</th>
<th id="size-header" align="left">Size</th>
<th id="type-header" align="left">Type</th>
<th id="created-header" align="left">Created</th>
<tr>
<?php foreach($uploads as $upload){ ?> 

    <tr>
    <td class="upload-name"><div class="upload-inner"><?php echo $upload->name; ?></div></td>
    <td><?php echo $upload->size; ?></td>
    <td><?php echo $upload->mime_type; ?></td>
    <td><?php echo $upload->created_at; ?></td>
    <tr>

<?php } ?>
</table>

@stop
