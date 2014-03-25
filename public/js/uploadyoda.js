
/**
 * @namespace uploadyoda
 */
(function( uploadyoda ) {

    /**
     * Total number of files uploaded 
     *
     * @private
     */
    var totalFilesUploaded = 0;
    
    /**
     * calculate the file size in kB/MB from total bytes
     *
     * @private
     * @function
     * @param {number} num_bytes - number of bytes in the file to be uploaded
     * @returns {string} formatted filesize 
     */
    function calculateFilesize(num_bytes)
    {
        if ( num_bytes < 1000000 )
            return (Math.ceil((num_bytes / 1000) * 100) / 100) + ' kB';
        else 
            return (Math.ceil((num_bytes / 1000000) * 100) / 100) + ' MB'; 
    }

    /**
     * validate the file size against max file size in app config and php.ini
     *
     * @private
     * @function
     * @param {number} filesize - number of bytes in the file to be uploaded
     * @returns {boolean} if the filesize is valid or not
     */
    function validFilesize(filesize)
    {
        if ( filesize > configMaxFilesize || filesize > serverMaxFilesize )
            return false;
        else
            return true;
    }

    /**
     * Check  if a value is in an array
     *
     * @private
     * @function
     * @param {mixed} value - needle
     * @param {array} array - haystack
     * @returns {boolean} if the value exists in the array or not 
     */
    function isInArray(value, array) 
    {
        return array.indexOf(value) > -1;
    }

    /**
     * Check if a files mime type is valid
     *
     * @private
     * @function
     * @param {string} mime
     * @returns {boolean} if the mime is valid or not 
     */
    function validMime(mime)
    {
        return isInArray(mime, mimes);  
    }

    /**
     * Updates UI for the table row of a failed upload
     *
     * @private
     * @function
     * @param {number} uploadNum - the upload number
     * @param {string} statusText - the error message 
     */
    function uploadFail(uploadNum, statusText)
    {
        var progressBar = document.getElementById( 'upload-' + uploadNum + '-progress' );
        progressBar.value = 0;
        progressBar.className += ' failedUpload';
        var uploadStatus = document.getElementById( 'upload-' + uploadNum + '-status' );
        uploadStatus.innerHTML = 'Upload failed: ' + statusText;
        console.error('request no: ' + uploadNum + ' ' + statusText ); 
    }

    /**
     * Updates UI for the table row of a successful upload
     *
     * @private
     * @function
     * @param {number} uploadNum - the upload number
     */
    function uploadSuccess(uploadNum)
    {
        console.log( 'success for request no: ' + uploadNum );
        var progressBar = document.getElementById( 'upload-' + uploadNum + '-progress' );
        progressBar.value = 0;
        progressBar.className += ' succeededUpload';
        var uploadStatus = document.getElementById( 'upload-' + uploadNum + '-status' );
        uploadStatus.innerHTML = 'Uploaded successfully';
        var upload = document.getElementById('upload-' + uploadNum);
        setTimeout(function(){upload.parentNode.removeChild(upload)}, 500);
    }

    /**
     * Updates UI creating new table row for a new upload
     *
     * @private
     * @function
     * @param {object} file - the file object
     * @param {number} fileNumber - the number of the uploaded file
     */
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

    /**
     * Creates callback for the xhr onload event
     *
     * @private
     * @function
     * @param {object} requestObject - the object that encapsulates the XMLHttpRequest
     */
    function createOnloadFunction(requestObject)
    {
        return function()
        { 
            if ( requestObject.xhr.status === 200 )  
            {
                if ( requestObject.xhr.responseText != 'success' ) 
                {
                    uploadFail(requestObject.requestNo, requestObject.xhr.responseText);
                }
                else
                {
                    uploadSuccess(requestObject.requestNo);
                }
            }
            else
                console.log(requestObject.xhr.responseText + requestObject.requestNo);
        }
    }

    /**
     * Updates UI upload progress for an upload
     *
     * @private
     * @function
     * @param {number} uploadNum - the number of the upload
     * @param {object} progressEventObj - the progess object of the XMLHttpRequest
     */
    function updateProgressUI(uploadNum, progressEventObj)
    {
        var complete = (progressEventObj.loaded / progressEventObj.total * 100 | 0);
        var progressBar = document.getElementById( 'upload-' + uploadNum + '-progress' );
        progressBar.value = complete;
        var percent = document.getElementById('upload-' + uploadNum + '-complete');
        percent.innerHTML = complete + '%';
    }

    /**
     * Creates callback for the xhr onprogresss event
     *
     * @private
     * @function
     * @param {object} requestObject - the object that encapsulates the XMLHttpRequest
     */
    function createUploadProgressFunction(requestObject)
    {
        return function(e)
        {
            if (e.lengthComputable)
            {
                updateProgressUI(requestObject.requestNo, e);
            }
        }
    }

    /**
     * Function that handles the uploading of files when a file/files are dragged and dropped into upload area 
     *
     * @public
     * @function
     * @param {object} files - object containing files that were dropped
     */
    uploadyoda.readFiles = function( files )
    {
        var requests = [];
        console.log('total files uploaded = ' + totalFilesUploaded);

        for ( var i = 0; i < files.length; i++)
        {
            totalFilesUploaded++;
            var formData = new FormData();
            formData.append('_token', csrf_token);
            formData.append('file', files[i]);
            getFileInfo(files[i], totalFilesUploaded);
            console.log(files[i].type);
            console.log(mimes);

            /**
            * Validation
            */

            if ( !validFilesize(files[i].size) )
            {
                uploadFail(totalFilesUploaded, 'max file size exceeded' );
                continue;
            }

            var mime = defaultExtensions[files[i].type];

            if ( !validMime(mime, mimes) ) 
            {
                uploadFail(totalFilesUploaded, 'invalid mime type' );
                continue;
            }

            /**
            * Prepare ajax request and handlers and send the request
            */

            requests[i] = {};
            requests[i].fileName = files[i].name;
            requests[i].fileSize = calculateFilesize(files[i].size); 
            requests[i].requestNo = totalFilesUploaded;
            requests[i].xhr = new XMLHttpRequest();
            requests[i].xhr.open('POST', '/uploadyoda/store');

            requests[i].xhr.onload = createOnloadFunction(requests[i]);  
            requests[i].xhr.upload.onprogress = createUploadProgressFunction(requests[i]); 

            requests[i].xhr.send(formData);
        }
    }

}( window.uploadyoda = window.uploadyoda || {} ));

/**
 * Binding the drag and drop events to the respective handlers
 */
window.onload = function(){
 
  var dropArea = document.getElementById('drop-area');

  dropArea.ondragover = function (e) { 
    e.preventDefault();
    return false; 
  };
  dropArea.ondragenter = function () { 
    this.className = 'drop-hover'; 
    return false; 
  };
  dropArea.ondragleave = function () { 
    this.className = ''; 
    return false; 
  };
  dropArea.ondrop = function (e) {
    e.preventDefault && e.preventDefault();
    this.className = '';
    uploadyoda.readFiles(e.dataTransfer.files);
    return false;
  };

};
