@extends('uploadyoda::layouts.master')

@section('content')
<?php
    $queryString = Request::query();
    unset($queryString['page']);
    $filters = isset($queryString['filters']) ? $queryString['filters'] : '';
    $sort = isset($queryString['sort']) ? $queryString['sort'] : '';
?>
<script>
    var csrfToken = "{{ csrf_token() }}";
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
                    @if ( $filters !== '' )
                        <input type="hidden" name="filters" value="{{ $filters }}">
                    @endif
                    @if ( $sort !== '' )
                        <input type="hidden" name="sort" value="{{ $sort }}">
                    @endif
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
                <p><a id="sort-date" class="sort-link" href="/uploadyoda?sort=date">Date</a></p>
                <p><a id="sort-name" class="sort-link" href="/uploadyoda?sort=name">Name</a></p>
                <p><a id="sort-size" class="sort-link" href="/uploadyoda?sort=size">Size</a></p>
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
        echo $uploads->appends($queryString)->links();
    } // end if
    else
    { ?>
        </table>
    <?php }// end else ?>

@stop

@section('footer')
<script type="text/javascript" src="{{ URL::asset('packages/quasimodal/uploadyoda/js/home.js') }}"></script>
@stop
