/**
 * @namespace uploadyoda
 */
$(function(){
    
    (function( uploadyoda ) {

        var globals = {};

        globals.filterTypes = {
            'today' : 'date',
            'week' : 'date',
            'month' : 'date',
            'year' : 'date',
            'image' : 'format',
            'video' : 'format',
            'audio' : 'format'
        };

        uploadyoda.query = {};

        function parseQuery()
        {
            var filters = location.search.slice(1).split('&');

            if ( filters[0] !== '' )
            {
                for( var i = 0; i < filters.length; i++ )
                {
                    var filter = filters[i].split('=');

                    var filterType = filter[0];
                    var filterValues = filter[1];

                    if (filterType !== 'search' && filterType !== 'sort' )
                    {
                        uploadyoda.query[filter[0]] = filter[1].split('%2C').filter(function(filter){return globals.filterTypes.hasOwnProperty(filter);});
                    }
                    else
                    {
                        uploadyoda.query[filter[0]] = filter[1].split('%2C');
                    }
                }
            }
        }

        function updateFilterUI()
        {
            if ( uploadyoda.query.hasOwnProperty('filters') )
            {
                for ( var i = 0; i < uploadyoda.query.filters.length; i++ )
                {
                    var removeFilterLink = '/uploadyoda?';
                
                    var filters = uploadyoda.query.filters.filter(function(filter) { return filter!==uploadyoda.query.filters[i]; } );

                    console.log(filters);

                    var formattedLinkParts = [];

                    for ( var param in uploadyoda.query )
                    {
                        if ( param == 'filters' )
                        {
                            if ( filters.length )
                                formattedLinkParts.push('filters=' + filters.join('%2C'));
                        }
                        else
                        {
                            formattedLinkParts.push(param + '=' + uploadyoda.query[param]); 
                        }
                    }

                    removeFilterLink += formattedLinkParts.join('&');

                    // update the UI
                    $('#filter-' + uploadyoda.query.filters[i]).append(' <a href="' + removeFilterLink + '"><i class="fa fa-times"></i></a>');
                    $('#filters-in-use').append('<a href="' + removeFilterLink + '"><button type="button" class="btn btn-default btn-sm in-use-filter-btn">' + uploadyoda.query.filters[i] + ' <i class="fa fa-times"></i></button></a>');
                }
            }
        }

        function updateFilterUrls()
        {
           $('.filter-link').each(function(){
                
            var href = $(this).attr('href');

            var newHref = '';

            if ( uploadyoda.query.hasOwnProperty('filters') )
            {
                for ( var i = 0; i < uploadyoda.query.filters.length; i++ )
                {
                    // if href doesn't contain filter already and if filter and href are not both date filters append the filter
                    if ( href.indexOf(uploadyoda.query.filters[i]) == -1 && !( globals.filterTypes[href.substr(20)] == 'date' && globals.filterTypes[uploadyoda.query.filters[i]] == 'date' ) )
                    {
                        newHref += '%2C' + uploadyoda.query.filters[i];
                    }
                }
            }

            if ( uploadyoda.query.hasOwnProperty('search') )
                newHref += '&search=' + uploadyoda.query.search;

            $(this).attr('href', href + newHref);

           });
        }

        function updateSortUrls()
        {
            $('.sort-link').each(function(){
                
                var href = $(this).attr('href');

                var newHref = '';

                if ( uploadyoda.query.hasOwnProperty('filters') )
                {
                    var filterString = uploadyoda.query.filters.join('%2C');

                    newHref += '&filters=' + filterString;
                    
                }

                if ( uploadyoda.query.hasOwnProperty('search') )
                    newHref += '&search=' + uploadyoda.query.search;

                $(this).attr('href', href + newHref);

            });
        }

        function updateSearchUI()
        {
            if ( uploadyoda.query.hasOwnProperty('search') )
                    $('#searchFilter').val(uploadyoda.query.search);
        }

        function bindEventHandlers()
        {
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
                        inputToken.value = csrfToken;
                        form.appendChild(inputToken);
                        form.style.display = 'none';
                        document.body.appendChild(form);
                        form.submit();
                    }
            });
        }

        uploadyoda.init = function(){
            parseQuery();
            updateFilterUI();
            updateFilterUrls();
            updateSortUrls();
            updateSearchUI();
            bindEventHandlers();
        };

    }( window.uploadyoda = window.uploadyoda || {} ));
    
    uploadyoda.init();
});

