<?php namespace Quasimodal\Uploadyoda\Service\Validation;

use Config,
    Illuminate\Support\MessageBag as MessageBag;

class UploadyodaValidator extends AbstractValidator
{
    protected $php_upload_errors = array(
        'File exceeds max server filesize',
        "File exceeds the HTML form's maximum filesize",
        'File was only partially uploaded',
        'No file was uploaded',
        'Server missing tmp folder',
        'Failed to write to disk',
        'PHP extension stopped the file upload'
    );

    public function getPHPUploadError($errorCode)
    {
        return $this->php_upload_errors[$errorCode - 1];
    }
    
    public function upload()
    {
        // here we validate the parts of the upload Laravel doesn't have validation rules for
        if ( empty($_FILES) )
        {
            $validationErrors = new MessageBag();
            $validationErrors->add('error', 'server error');
            $this->errors = $validationErrors;
            return false;
        }

        if ( ($_FILES['file']['error']) != 0 ) 
        {
            $validationErrors = new MessageBag();
            $validationErrors->add('error', $this->php_upload_errors[$_FILES['file']['error'] - 1]);
            $this->errors = $validationErrors;
            return false;
        }
        
        if ($this->data['file']->getSize() > Config::get('uploadyoda::max_file_size'))
        {
            $validationErrors = new MessageBag();
            $validationErrors->add('error', 'file size exceeds config max file size');
            $this->errors = $validationErrors;
            return false; 
        }
        
        // we can procceed to validate using laravel helpers now 
        $this->rules = array('file' =>'mimes:' . implode(',', Config::get('uploadyoda::allowed_mime_types')));
        $this->messages = array('file.mimes' => 'Invalid mime type');
        return $this->passes(); 
    }
}
