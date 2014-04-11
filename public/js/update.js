$(':submit').click(function(e){
    e.preventDefault();
    if ( $(this).attr('id') == 'updateButton' )
    {
        $.post("/uploadyoda/" + uploadID + "/update", $('#editForm')
            .serialize())
            .done(function(data){
                response = JSON.parse(data);
                var status = $('#updateStatus');
                if ( response.code == 200 )
                {
                    $('#alertContainer').html('<div class="alert alert-success">Updated successfully</div>');
                }
                else
                {
                    $('#alertContainer').html('<div class="alert alert-danger">Update failed</div>');
                }
            });
    }
    else
    {
    }
});
