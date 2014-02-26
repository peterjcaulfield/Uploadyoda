<?php namespace Quasimodal\Uploadyoda\Service\Validation;

use Config;

class UploadyodaValidator extends AbstractValidator
{
    protected $rules = array(
        'file' => 
        'mimes:' . Config::get('uploadyoda::mimes') . ',' .
        'size:'  . Config::get('uploadyoda::max_file_size') . ',' .
         
    );

    public function upload()
    {
        $this->passes(); 
    }
}
