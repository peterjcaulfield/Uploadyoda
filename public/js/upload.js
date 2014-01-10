
totalFilesUploaded = 0;

function getFileInfo(file, fileNumber)
{
 console.log('adding file info');
 var fileInfo = '<p>Uploading: ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
 var downloadsContainer = document.getElementById('downloads');
 var download = document.createElement('div');
 var downloadInfo = document.createElement('div');
 var progress = document.createElement('progress');
 download.id = "download-" + fileNumber;
 download.className = 'download';
 downloadInfo.className = 'download-info';
 progress.id = "download-" + fileNumber + '-progress';
 progress.className = "progress";
 progress.max = 100;
 progress.value = 0;
 downloadInfo.innerHTML = fileInfo;
 download.appendChild(downloadInfo);
 download.appendChild(progress);
 downloadsContainer.appendChild(download);
 
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
       console.error('request no: ' + requestObject.requestNo + ' ' + requestObject.xhr.responseText ); 
      }
      else
        console.log( 'success for request no: ' + requestObject.requestNo );
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
        var complete = (e.loaded / e.total * 100 | 0);
        var progressBar = document.getElementById( 'download-' + requestObject.requestNo + '-progress' );
        progressBar.value = complete;
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