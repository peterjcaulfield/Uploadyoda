<?php namespace Quasimodal\Uploadyoda\Service\Validation;

use Illuminate\Validation\Factory as Validator;

abstract class AbstractValidator implements ValidableInterface
{
    protected $validator;

    protected $data = array();

    protected $errors = array();

    protected $rules = array();

    protected $messages = array();

    public function __construct( Validator $validator )
    {
        $this->validator = $validator;
    }

    public function with(array $data)
    {
        $this->data = $data;
        
        return $this;
    }

    public function passes()
    {
        $validator = $this->validator->make(
            $this->data,
            $this->rules,
            $this->messages
        );

        if ( $validator->fails() )
        {
            $this->errors = $validator->messages();
            return false;
        } 
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}
