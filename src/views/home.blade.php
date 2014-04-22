@extends('uploadyoda::layouts.master')

@section('content')
<?php
    $queryString = Request::query();
    unset($queryString['page']);
    $filters = isset($queryString['filters']) ? $queryString['filters'] : '';
    $searchQuery = isset($queryString['search']) ? $queryString['search'] : '';
?>
<script>
    $(document).ready(function(){

        var filterTypes = {
            'today' : 'date',
            'week' : 'date',
            'month' : 'date',
            'year' : 'date',
            'image' : 'format',
            'video' : 'format',
            'audio' : 'format',
        }

        var filterString = "<?php echo $filters; ?>";
        var searchQuery = "<?php echo $searchQuery; ?>";

        var filters = filterString.split(',');

        if ( filters.length > 1 )
        {
            for ( var i = 0; i < filters.length; i++ )
            {
                var xLinkFilters = filters.filter(function(filter) { return filter !== filters[i]});
                var xLinkHref = xLinkFilters.join(',');

                if ( searchQuery !== '')
                    xLinkHref+= '&search=' + searchQuery;

                $('#filter-' + filters[i]).append(' <a href="/uploadyoda?filters=' + xLinkHref + '"><i class="fa fa-times"></i></a>');
                $('#filters-in-use').append('<button type="button" class="btn btn-default btn-sm in-use-filter-btn"><a href="/uploadyoda?filters=' + xLinkHref + '">' + filters[i] + ' <i class="fa fa-times"></i></a></button>');
            }
        }

        $('.filter-link').each(function(){
            var href = $(this).attr('href');

            var newHref = '';

            if ( filters.length )
            {
                for ( var i = 0; i < filters.length; i++ )
                {
                    // if href doesn't contain filter already and if filter and href are not both date filters append the filter
                    if ( href.indexOf(filters[i]) == -1 && !( filterTypes[href.substr(20)] == 'date' && filterTypes[filters[i]] == 'date' ) )
                    {
                        newHref += ',' + filters[i];
                    }
                }
            }

            if ( searchQuery !== '')
                newHref += '&search=' + searchQuery;

            $(this).attr('href', href + newHref);
        });

        $('#searchFilter').val(searchQuery);

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

        $('#filter-action').on('click', function(){
            $('#filters').toggle();
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
                input.name = 'delete';
                input.value = JSON.stringify(uploadsToTrash);
                form.appendChild(input);
                var inputToken = document.createElement('input');
                inputToken.name = '_token';
                inputToken.value = "<?php echo csrf_token(); ?>";
                form.appendChild(inputToken);
                form.style.display = 'none';
                document.body.appendChild(form);
                form.submit();
            }
        });
    })
</script>
<div id="uploads-index">
    <div id="tableActions">
        <div id="uploadBatchOptions">
            <select id="uploadSelectBatch" class="form-control">
                <option value="0">Actions</option>
                <option value="1" id="uploadBatchApply">Move to trash</option>
            </select>
            <button id="applyBatch" class="btn btn-default btn-sm">Apply</button>
        </div>
        <div id="search">
            <form method="get" action="<?php echo URL::route('uploadyodaHome'); ?>" id="filterForm">
                <div class="input-group" id="searchContainer">
                    <input id="searchFilter" type="text" class="form-control" name="search">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-sm" type="submit">Search</button>
                    </span>
                </div>
            </form>
        </div>
        <button type="button" id="filter-action" class="btn btn-default btn-sm">Filters <i class="fa fa-caret-down"></i></button>
        <div id="filters-in-use"></div>
        <div id="filters">
            <div class="filter-options">
                <p><strong>Upload date</strong></p>
                <p id="filter-today"><a class="filter-link" href="/uploadyoda?filters=today">Today</a></p>
                <p id="filter-week"><a class="filter-link" href="/uploadyoda?filters=week">This week</a></p>
                <p id="filter-month"><a class="filter-link" href="/uploadyoda?filters=month">This month</a></p>
                <p id="filter-year"><a class="filter-link" href="/uploadyoda?filters=year">This year</a></p>
            </div>
            <div class="filter-options">
                <p><strong>Result type</strong></p>
                <p id="filter-image"><a class="filter-link" href="/uploadyoda?filters=image">Image</a></p>
                <p id="filter-video"><a class="filter-link" href="/uploadyoda?filters=video">Video</a></p>
                <p id="filter-audio"><a class="filter-link" href="/uploadyoda?filters=audio">Audio</a></p>
            </div>
            <div class="filter-options">
                <p><strong>Sort by</strong></p>
            </div>
        </div>
    </div>
    <table id="header-container" class="table table-condensed table-bordered" style="position: relative">
        <th id="thumbnail-header" class="header"><div class="header-text"><input type="checkbox" id="uploadCheckboxBatch" class=""></div></th>
        <th id="name-header" class="header"><div class="header-text">Name</div></th>
        <th id="size-header" class="header"><div class="header-text">Size</div></th>
        <th id="type-header" class="header"><div class="header-text">Type</div></th>
        <th id="created-header" class="header"><div class="header-text">Created</div></th>
    <?php
    if ( $uploads->count() )
    {
        foreach($uploads as $upload){ ?>
           <tr id="{{'upload-' . $upload->id }}">
            <td class="preview">
                <input type="checkbox" class="uploadCheckbox" value="<?php echo $upload->id; ?>">
                <?php echo Helpers::generateThumbnail($upload->name, $upload->mime_type); ?>
            </td>
            <td class="upload-name row-text">
                <div class="upload-inner row-text">
                    {{ $upload->name }}<br/><br/>
                    <div class="rowActions">
                        <ul class="rowActionLinks">
                            <li><a class="rowAction" href="<?php echo '/uploadyoda/' . $upload->id . '/view'; ?>">View</a></li>|
                            <li><a class="rowAction" href="<?php echo '/uploadyoda/' . $upload->id . '/edit'; ?>">Edit</a></li>|
                            <li><a class="rowAction" href="<?php echo '/uploadyoda/' . $upload->id . '/destroy'; ?>">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </td>
            <td class="size"><div class="row-text"><?php echo $upload->size; ?></div></td>
            <td class="mime"><div class="row-text"><?php echo $upload->mime_type; ?></div></td>
            <td class="created"><div class="row-text"><?php echo $upload->created_at; ?></div></td>
          </tr>
    <?php } // end loop ?>
        </table>
    <?php
        $queryString = Request::query();
        unset($queryString['page']);
        echo $uploads->appends($queryString)->links();
    } // end if
    else
    { ?>
        </table>
    <?php }// end else ?>

@stop
