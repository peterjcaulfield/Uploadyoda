
totalFilesUploaded = 0;

function calculateFilesize(num_bytes)
{
  if ( num_bytes < 1000000 )
    return (Math.ceil((num_bytes / 1000) * 100) / 100) + ' kB';
  else return (Math.ceil((num_bytes / 1000000) * 100) / 100) + ' MB'; 
}

function getFileInfo(file, fileNumber)
{
 var downloadsContainer = document.getElementById('downloadsBody');
 var tableRow = document.createElement('tr');
 tableRow.id = 'upload-' + fileNumber;
 var uploadNameTd = '<td id="upload-' + fileNumber + '-name" class="upload-name"><div class="upload-name-inner">' + file.name  + '</div></td>';
 var uploadSizeTd = '<td id="upload-' + fileNumber + '-size" class="upload-size">'+ calculateFilesize(file.size) + '</td>';
 var uploadProgressTd = '<td id="upload-' + fileNumber + '-progress-td" class="upload-progress"><progress value=0 max=100 id="upload-' + fileNumber + '-progress" class="progress"></progress></td>';
 var uploadCompleteTd = '<td id="upload-' + fileNumber + '-complete" class="upload-complete">0%</td>';
 var uploadStatusTd = '<td id="upload-' + fileNumber + '-status" class="upload-status">Uploading</td>';

 tableRow.innerHTML = uploadNameTd + uploadSizeTd + uploadProgressTd + uploadCompleteTd + uploadStatusTd;
 downloadsContainer.appendChild(tableRow);
}

function createOnloadFunction(requestObject)
{
  return function()
  { 
    if ( requestObject.xhr.status === 200 )  
    {
      console.log('request no: ' + requestObject.requestNo + ' complete');
      if ( requestObject.xhr.responseText != 'success' ) 
      {
        var progressBar = document.getElementById( 'upload-' + requestObject.requestNo + '-progress' );
        progressBar.value = 0;
        progressBar.className += ' failedUpload';
        var uploadStatus = document.getElementById( 'upload-' + requestObject.requestNo + '-status' );
        uploadStatus.innerHTML = 'Upload failed: ' + requestObject.xhr.responseText;
        console.error('request no: ' + requestObject.requestNo + ' ' + requestObject.xhr.responseText ); 
      }
      else
      {
        console.log( 'success for request no: ' + requestObject.requestNo );
        var progressBar = document.getElementById( 'upload-' + requestObject.requestNo + '-progress' );
        progressBar.value = 0;
        progressBar.className += ' succeededUpload';
        var uploadStatus = document.getElementById( 'upload-' + requestObject.requestNo + '-status' );
        uploadStatus.innerHTML = 'Uploaded successfully';
        var upload = document.getElementById('upload-' + requestObject.requestNo);
        setTimeout(function(){upload.parentNode.removeChild(upload)}, 500);
      }
    }
    else
      console.log('something went wrong for request no: ' + requestObject.requestNo);
  }
}

function createUploadProgressFunction(requestObject)
{
  return function(e)
  {
    if (e.lengthComputable)
    {
       console.log(e.loaded);

        var complete = (e.loaded / e.total * 100 | 0);
        var progressBar = document.getElementById( 'upload-' + requestObject.requestNo + '-progress' );
        progressBar.value = complete;
        var percent = document.getElementById('upload-' + requestObject.requestNo + '-complete');
        percent.innerHTML = complete + '%';
        console.log('progress for request no: ' + requestObject.requestNo + ' is ' + complete);
    }
  }
}

function readFiles(files)
{
  var requests = [];

  for ( var i = 0; i < files.length; i++)
  {
    var formData = new FormData();
    formData.append('file', files[i]);
    getFileInfo(files[i], totalFilesUploaded + 1);

    requests[i] = {};
    requests[i].fileName = files[i].name;
    requests[i].fileSize = calculateFilesize(files[i].size); 
    requests[i].requestNo = totalFilesUploaded + 1;
    requests[i].xhr = new XMLHttpRequest();
    requests[i].xhr.open('POST', '/uploadyoda/store');
    requests[i].xhr.onload = createOnloadFunction(requests[i]);  
      
    
    requests[i].xhr.upload.onprogress = createUploadProgressFunction(requests[i]); 
    
    requests[i].xhr.send(formData);
    totalFilesUploaded++;
  }
}

window.onload = function(){
 
  var dropArea = document.getElementById('drop-area');

  dropArea.ondragover = function (e) { 
    e.preventDefault();
    return false; 
  };
  dropArea.ondragenter = function () { 
    console.log('dragged on');
    this.className = 'drop-hover'; 
    return false; 
  };
  dropArea.ondragleave = function () { 
    console.log('dragged off');
    this.className = ''; 
    return false; 
  };
  dropArea.ondrop = function (e) {
    console.log('dropped on');
    e.preventDefault && e.preventDefault();
    this.className = '';
    readFiles(e.dataTransfer.files);
    
    // now do something with:
    //var files = event.dataTransfer.files;

    return false;
  };
};
