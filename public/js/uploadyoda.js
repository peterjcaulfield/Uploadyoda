/**
 * @namespace uploadyoda
 */
$(document).ready(function(){

    (function( uploadyoda ) {

        var globals = {};
        /**
        * Total number of files uploaded 
        *
        * @private
        */
        globals.totalFilesUploaded = 0;
        
        /**
        * Total number of files uploaded 
        *
        * @private
        */
        globals.successfulUploads = 0;
        
        /**
        * Total number of files uploaded 
        *
        * @private
        */
        globals.failedUploads = 0;
    
        /**
        * Table for uploads in progress 
        *
        * @private
        */
        globals.uploadsProgressContainer = document.getElementById('progressUploadsTableBody');
    
        /**
        * Table for successful uploads 
        *
        * @private
        */
        globals.uploadsSuccessContainer = $('#sucessfulUploadsTableBody');
    
        /**
        * Table for failed uploads 
        *
        * @private
        */
        globals.uploadsFailedContainer = $('#failedUploadsTableBody'); 

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
        function uploadFail(upload, error)
        {
            globals.failedUploads++;
            upload.UIElements.uploadRow.remove();
            $('#failCount').html(' ' + globals.failedUploads + ' ');
            var uploadNameTd = '<td id="upload-' + upload.uploadNum + '-name" class="upload-name"><div class="upload-name-inner" id="upload-' + upload.uploadNum + '-name-inner">' + upload.uploadMeta.filename  + '</div></td>';
            var uploadSizeTd = '<td id="upload-' + upload.uploadNum + '-size" class="upload-size">'+ upload.uploadMeta.filesize + '</td>';
            var errorTd = '<td>' + error + '</td>';
            globals.uploadsFailedContainer.append('<tr>' + uploadNameTd + uploadSizeTd + errorTd + '</tr>');
        }

        /**
        * Updates UI for the table row of a successful upload
        *
        * @private
        * @function
        * @param {number} uploadNum - the upload number
        */
        function uploadSuccess(upload, id)
        {
            globals.successfulUploads++;
            upload.UIElements.uploadRow.remove();
            $('#successCount').html(' ' + globals.successfulUploads + ' ');
            var uploadNameTd = '<td id="upload-' + upload.uploadNum + '-name" class="upload-name"><div class="upload-name-inner" id="upload-' + upload.uploadNum + '-name-inner">' + upload.uploadMeta.filename  + '</div></td>';
            var uploadSizeTd = '<td id="upload-' + upload.uploadNum + '-size" class="upload-size">'+ upload.uploadMeta.filesize + '</td>';
            var actionsTd = '<td><div class=""><a href="/uploadyoda/' + id + '/edit">Edit</a></div></td>';
            globals.uploadsSuccessContainer.append('<tr>' + uploadNameTd + uploadSizeTd + actionsTd + '</tr>');
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
            var uploadMeta = {};
            uploadMeta.fileNumber = fileNumber;
            uploadMeta.filename = file.name;
            uploadMeta.filesize = calculateFilesize(file.size);

            var tableRow = document.createElement('tr');
            tableRow.id = 'upload-' + fileNumber;
            var uploadNameTd = '<td id="upload-' + fileNumber + '-name" class="upload-name"><div class="upload-name-inner" id="upload-' + fileNumber + '-name-inner">' + file.name  + '</div></td>';
            var uploadSizeTd = '<td id="upload-' + fileNumber + '-size" class="upload-size">'+ calculateFilesize(file.size) + '</td>';
            var uploadProgressTd = '<td id="upload-' + fileNumber + '-progress-td" class="upload-progress"><progress value=0 max=100 id="upload-' + fileNumber + '-progress" class="progress"></progress></td>';
            var uploadCompleteTd = '<td id="upload-' + fileNumber + '-complete" class="upload-complete">0%</td>';

            tableRow.innerHTML = uploadNameTd + uploadSizeTd + uploadProgressTd + uploadCompleteTd;
            globals.uploadsProgressContainer.appendChild(tableRow);

            var UIElements = {

                progressBar : document.getElementById('upload-' + fileNumber + '-progress'),
                uploadNameTd : document.getElementById('upload-' + fileNumber + '-name-inner'),
                completeTd : document.getElementById('upload-' + fileNumber + '-complete'),
                uploadRow : $('#upload-' + fileNumber)
            };

            var upload = {};
            upload.uploadMeta = uploadMeta;
            upload.UIElements = UIElements;

        return upload;
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
                    var response = JSON.parse(requestObject.xhr.response);

                    if ( response.code != 200 ) 
                    {
                        uploadFail(requestObject.upload, requestObject.xhr.responseText);
                    }
                    else
                    {
                        uploadSuccess(requestObject.upload, response.id);
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
        function updateProgressUI(upload, progressEventObj)
        {
            var complete = (progressEventObj.loaded / progressEventObj.total * 100 | 0);
            upload.UIElements.progressBar.value = complete;
            upload.UIElements.completeTd.innerHTML = complete + '%';
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
                    updateProgressUI(requestObject.upload, e);
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
                globals.totalFilesUploaded++;

                // create the request object
                requests[i] = {};
                
                // create the form object
                var formData = new FormData();
                formData.append('_token', csrf_token);
                formData.append('file', files[i]);

                // update UI 
                var upload  = getFileInfo(files[i], globals.totalFilesUploaded);
                upload.uploadNum = i;
        
                // add the requests UI Elements to the request object
                requests[i].upload = upload;

                // validate file
                
                if ( !validFilesize(files[i].size) )
                {
                    uploadFail(upload, 'max file size exceeded' );
                    continue;
                }

                var mime = defaultExtensions[files[i].type];

                if ( !validMime(mime, mimes) ) 
                {
                    uploadFail(requests[i].upload, 'invalid mime type' );
                    continue;
                }
                
                // Create the ajax request object
                requests[i].fileName = files[i].name;
                requests[i].fileSize = calculateFilesize(files[i].size); 
                requests[i].requestNo = globals.totalFilesUploaded;
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

});

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
