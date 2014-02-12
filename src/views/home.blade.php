@extends('uploadyoda::layouts.master')

@section('content')

<div id="uploads-index">

    <!--<div id="header-container">
        <div id="thumbnail-header" class="header"><div class="header-text"></div></div>
        <div id="name-header" class="header"><div class="header-text">Name</div></div>
        <div id="size-header" class="header"><div class="header-text">Size</div></div>
        <div id="type-header" class="header"><div class="header-text">Type</div></div>
        <div id="created-header" class="header"><div class="header-text">Created</div></div>
    </div>

    <?php foreach($uploads as $upload){ ?> 
        
        <div class="row" style="position: relative">
            <div class="thumbnail"><?php //echo Uploadyoda::generateThumbnail($upload->name, $upload->mime_type); ?></div>
            <div class="upload-name row-text"><div class="upload-inner"><?php //echo $upload->name; ?></div></div>
            <div class="size"><div class="row-text"><?php //echo $upload->size; ?></div></div>
            <div class="mime"><div class="row-text"><?php //echo $upload->mime_type; ?></div></div>
            <div class="created"><div class="row-text"><?php //echo $upload->created_at; ?></div></div>
        </div>

    <?php } ?>

</div>-->

<?php //echo $uploads->links(); ?>
    
    <table id="header-container" class="table table-condensed table-bordered">
        <th id="thumbnail-header" class="header"><div class="header-text"></div></th>
        <th id="name-header" class="header"><div class="header-text">Name</div></th>
        <th id="size-header" class="header"><div class="header-text">Size</div></th>
        <th id="type-header" class="header"><div class="header-text">Type</div></th>
        <th id="created-header" class="header"><div class="header-text">Created</div></th>
    
    <?php foreach($uploads as $upload){ ?> 
           <tr> 
            <td class="preview"><?php echo Uploadyoda::generateThumbnail($upload->name, $upload->mime_type); ?></td>
            <td class="upload-name row-text"><div class="upload-inner row-text"><?php echo $upload->name; ?></div></td>
            <td class="size"><div class="row-text"><?php echo $upload->size; ?></div></td>
            <td class="mime"><div class="row-text"><?php echo $upload->mime_type; ?></div></td>
            <td class="created"><div class="row-text"><?php echo $upload->created_at; ?></div></td>
          </tr>
    
    <?php } ?>

    </table>

<?php echo $uploads->links(); ?>

@stop
