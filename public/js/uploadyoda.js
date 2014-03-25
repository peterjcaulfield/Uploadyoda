
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
     * @param {number} numBytes - number of bytes in the file to be uploaded
     * @returns {string} formatted filesize 
     */
    function calculateFilesize(numBytes)
    {
        if ( numBytes < 1000000 )
            return (Math.ceil((numBytes / 1000) * 100) / 100) + ' kB';
        else 
            return (Math.ceil((numBytes / 1000000) * 100) / 100) + ' MB'; 
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
    function uploadFail(UIElements, statusText)
    {
        UIElements.progressBar.value = 0;
        UIElements.progressBar.className += ' failedUpload';
        UIElements.statusTd.innerHTML = 'Upload failed: ' + statusText;
    }

    /**
     * Updates UI for the table row of a successful upload
     *
     * @private
     * @function
     * @param {number} uploadNum - the upload number
     */
    function uploadSuccess(UIElements)
    {
        UIElements.progressBar.value = 0;
        UIElements.progressBar.className += ' succeededUpload';
        UIElements.statusTd.innerHTML = 'Uploaded successfully';
        setTimeout(function(){UIElements.uploadRow.parentNode.removeChild(UIElements.uploadRow)}, 500);
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

        var UIElements = {
            
            progressBar : document.getElementById('upload-' + fileNumber + '-progress'),
            statusTd : document.getElementById('upload-' + fileNumber + '-status'),
            completeTd : document.getElementById('upload-' + fileNumber + '-complete'),
            uploadRow : document.getElementById('upload-' + fileNumber)
        };
       
       return UIElements;
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
                    uploadFail(requestObject.UIElements, requestObject.xhr.responseText);
                }
                else
                {
                    uploadSuccess(requestObject.UIElements);
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
    function updateProgressUI(UIElements, progressEventObj)
    {
        var complete = (progressEventObj.loaded / progressEventObj.total * 100 | 0);
        UIElements.progressBar.value = complete;
        UIElements.completeTd.innerHTML = complete + '%';
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
                updateProgressUI(requestObject.UIElements, e);
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
        // array to hold each ajax request
        var requests = [];

        // process each file

        for ( var i = 0; i < files.length; i++)
        {
            totalFilesUploaded++;

            // create the request object
            requests[i] = {};
            
            // create the form object
            var formData = new FormData();
            formData.append('_token', csrf_token);
            formData.append('file', files[i]);

            // update UI 
            var requestUIElements = getFileInfo(files[i], totalFilesUploaded);
    
            // add the requests UI Elements to the request object
            requests[i].UIElements = requestUIElements;

            // validate file
            
            if ( !validFilesize(files[i].size) )
            {
                uploadFail(totalFilesUploaded, 'max file size exceeded' );
                continue;
            }

            var mime = defaultExtensions[files[i].type];

            if ( !validMime(mime, mimes) ) 
            {
                uploadFail(requests[i].UIElements, 'invalid mime type' );
                continue;
            }
            
            // Create the ajax request object
            requests[i].fileName = files[i].name;
            requests[i].fileSize = calculateFilesize(files[i].size); 
            requests[i].requestNo = totalFilesUploaded;
            requests[i].xhr = new XMLHttpRequest();
            requests[i].xhr.open('POST', '/uploadyoda/store');

            // bind the UI update handlers
            requests[i].xhr.onload = createOnloadFunction(requests[i]);  
            requests[i].xhr.upload.onprogress = createUploadProgressFunction(requests[i]); 
            
            // send the request
            requests[i].xhr.send(formData);
        }
    }

}( window.uploadyoda = window.uploadyoda || {} ));

/**
 * Bind the drag and drop events to the respective handlers
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
