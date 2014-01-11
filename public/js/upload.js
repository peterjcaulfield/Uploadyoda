
totalFilesUploaded = 0;

function getFileInfo(file, fileNumber)
{
 console.log('adding file info');
 var fileInfo = '<div id="file-info-' + fileNumber + '-inner" class="file-info-inner">Uploading: ' + file.name + ' Size:' + (file.size ? (file.size/1024|0) + 'K' : '') + '</div><div id="download-' + fileNumber + '-error"' + 'class="upload-error"></div>';
 var downloadsContainer = document.getElementById('downloads');
 var downloadContainer = document.createElement('div');
 var downloadPercentage = document.createElement('div');
 var download = document.createElement('div');
 var downloadInfo = document.createElement('div');
 var progress = document.createElement('progress');
 downloadContainer.id = 'download-' + fileNumber + '-container';
 downloadContainer.className = 'download-container';
 downloadPercentage.id = 'download-' + fileNumber + '-percentage';
 downloadPercentage.className = 'download-percentage';
 downloadPercentage.innerHTML = '0%';
 download.id = "download-" + fileNumber;
 download.className = 'download';
 downloadInfo.id = 'download-' + fileNumber + '-info';
 downloadInfo.className = 'download-info';
 progress.id = "download-" + fileNumber + '-progress';
 progress.className = "progress";
 progress.max = 100;
 progress.value = 0;
 downloadInfo.innerHTML = fileInfo;
 download.appendChild(downloadInfo);
 download.appendChild(progress);
 downloadContainer.appendChild(download);
 downloadContainer.appendChild(downloadPercentage);
 downloadsContainer.appendChild(downloadContainer);
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
        var progressBar = document.getElementById( 'download-' + requestObject.requestNo + '-progress' );
        progressBar.value = 0;
        progressBar.className += ' failedUpload';
        var fileInfo = document.getElementById( 'file-info-' + requestObject.requestNo + '-inner');
        fileInfo.innerHTML= 'Upload failed for: ' + requestObject.fileName;
        var error = document.getElementById('download-' + requestObject.requestNo + '-error');
        error.innerHTML = 'Error: ' + requestObject.xhr.responseText;
        var percent = document.getElementById('download-' + requestObject.requestNo + '-percentage');
        percent.innerHTML = 'failed';
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
        var percent = document.getElementById('download-' + requestObject.requestNo + '-percentage');
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
    requests[i].fileSize = files[i].size ? (files[i].size/1024|0) + 'K' : ''; 
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
