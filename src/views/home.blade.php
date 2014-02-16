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
        <h3 class="sectionText"><i class="fa fa-home"></i>&nbsp;&nbsp;Uploads</h3>
    </div>
    <div id="uploadBatchOptions">
        <select id="uploadSelectBatch">
            <option value="0">Batch options</option>
            <option value="1" id="uploadBatchApply">Move to trash</option>
        </select> 
        <button id="applyBatch" class="btn btn-xs">Apply</button>
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

<?php echo $uploads->links(); ?>

@stop
