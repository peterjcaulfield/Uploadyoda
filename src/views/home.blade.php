@extends('uploadyoda::layouts.master')

@section('content')
<script>
    $(document).ready(function(){

        $('#uploadCheckboxBatch').on('click', function(e){ 
            var checked = $('#uploadCheckboxBatch:checked').length > 0; 
            $('.uploadCheckbox').prop('checked', checked); 

            if ( checked )
               $('.uploadCheckbox').addClass('checked').removeClass('unchecked'); 
            else
               $('.uploadCheckbox').addClass('unchecked').removeClass('checked'); 
        }); 

        $('.uploadCheckbox').on('click', function(e){
            var checkbox = $(this);
            if (checkbox.prop('checked'))            
               checkbox.removeClass('unchecked').addClass('checked'); 
            else
               checkbox.removeClass('checked').addClass('unchecked'); 
        });
        
        $('#applyBatch').on('click', function(){
            var batchAction = $('#uploadSelectBatch').val();
            if ( batchAction == 1 )
            {
                var uploadsToTrash = [];
                $('.checked').each(function(){
                    uploadsToTrash.push($(this).val());
                });    
                
                var form = document.createElement("form");
                form.method = 'post';
                form.action = '/uploadyoda/delete';
                var input = document.createElement('input');
                input.name = 'itemsToTrash';
                input.value = JSON.stringify(uploadsToTrash);
                form.appendChild(input);
                form.style.display = 'none';
                document.body.appendChild(form);
                form.submit();
            }
        });
    })
</script>
<div id="uploads-index">
    <div id="sectionHeaderContainer">
        <h1 class="sectionText"><i class="fa fa-home"></i>&nbsp;&nbsp;Uploads</h1>
    </div>
    <div id="uploadBatchOptions">
        <select id="uploadSelectBatch">
            <option value="0">Batch options</option>
            <option value="1" id="uploadBatchApply">Move to trash</option>
        </select> 
        <button id="applyBatch" class="btn btn-xs btn-primary">Apply</button>
    </div>
    <div id="filters">
        <form method="get" action="<?php echo URL::route('uploadyodaHome'); ?>" id="filterForm">
            <select name="type" id="filter" class="uploadInput">
                <option value="0">all types</option>
                <option value="image">images</option>
                <option value="video">video</option>
            </select> 
            <select name="date" id="filterDate" class="uploadInput">
                <option value="0">all dates</option>
                <option value="1" >january</option>
                <option value="2">feburary</option>
                <option value="3">march</option>
                <option value="4">april</option>
                <option value="5" >may</option>
                <option value="6">june</option>
                <option value="7" >july</option>
                <option value="8">august</option>
                <option value="9">september</option>
                <option value="10">october</option>
                <option value="11">november</option>
                <option value="12">december</option>
            </select> 
            <button id="applyFilter" class="btn btn-xs btn-primary">Filter</button>
                <input name="search" type="text" name="search" id="searchBox" class="uploadInput">
                <button id="applySearch" class="btn btn-xs btn-primary">Search</button>
        </form>
    </div>
    <table id="header-container" class="table table-condensed table-bordered">
        <th id="thumbnail-header" class="header"><div class="header-text"><input type="checkbox" id="uploadCheckboxBatch" class=""></div></th>
        <th id="name-header" class="header"><div class="header-text">Name</div></th>
        <th id="size-header" class="header"><div class="header-text">Size</div></th>
        <th id="type-header" class="header"><div class="header-text">Type</div></th>
        <th id="created-header" class="header"><div class="header-text">Created</div></th>
    
    <?php foreach($uploads as $upload){ ?> 
           <tr> 
            <td class="preview">
                <input type="checkbox" class="uploadCheckbox" value="<?php echo $upload->id; ?>">
                <?php echo Uploadyoda::generateThumbnail($upload->name, $upload->mime_type); ?>
            </td>
            <td class="upload-name row-text"><div class="upload-inner row-text"><?php echo $upload->name; ?></div></td>
            <td class="size"><div class="row-text"><?php echo $upload->size; ?></div></td>
            <td class="mime"><div class="row-text"><?php echo $upload->mime_type; ?></div></td>
            <td class="created"><div class="row-text"><?php echo $upload->created_at; ?></div></td>
          </tr>
    
    <?php } ?>

    </table>
<?php 

    $queryString = Request::query();
    unset($queryString['page']);
?>
<?php echo $uploads->appends($queryString)->links(); ?>

@stop
