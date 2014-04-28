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

                    if (filterType !== 'search' )
                        uploadyoda.query[filter[0]] = filter[1].split('%2C').filter(function(filter){return globals.filterTypes.hasOwnProperty(filter);});
                    else
                        uploadyoda.query[filter[0]] = filter[1].split('%2C');
                }
            }
        }

        function updateFilterUI()
        {
            if ( uploadyoda.query.hasOwnProperty('filters') )
            {
                for ( var i = 0; i < uploadyoda.query.filters.length; i++ )
                {
                    var removeFilterLink = '';
                    var removeFilterFilters = uploadyoda.query.filters.filter(function(filter){ return filter!==uploadyoda.query.filters[i] }); 
                    var removeFilterLinkFilters = removeFilterFilters.join('%2C');

                    if ( removeFilterLinkFilters !== '' )
                        var haveFilters = true;

                    if ( uploadyoda.query.hasOwnProperty('search') )
                        var haveSearch = true;

                    if ( haveFilters && haveSearch )
                    {
                        removeFilterLink = '?filters=' + removeFilterLinkFilters +  '&search=' + uploadyoda.query.search; 
                    }
                    else if ( haveFilters  )
                    {
                        removeFilterLink = '?filters=' + removeFilterLinkFilters; 
                    }
                    else if ( haveSearch )
                    {
                        removeFilterLink = '?search=' + uploadyoda.query.search;
                    }

                    // update the UI
                    $('#filter-' + uploadyoda.query.filters[i]).append(' <a href="/uploadyoda' + removeFilterLink + '"><i class="fa fa-times"></i></a>');
                    $('#filters-in-use').append('<a href="/uploadyoda' + removeFilterLink + '"><button type="button" class="btn btn-default btn-sm in-use-filter-btn">' + uploadyoda.query.filters[i] + ' <i class="fa fa-times"></i></button></a>');
                }
            }
        }

        function updateFilterUrls()
        {
           $('.filter-link').each(function(){
                
            var href = $(this).attr('href');

            var newHref = '';

            console.log(href.substr(20));

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

        uploadyoda.init = function(){
            parseQuery();
            updateFilterUI();
            updateFilterUrls();
        };

    }( window.uploadyoda = window.uploadyoda || {} ));
    
    uploadyoda.init();
});

