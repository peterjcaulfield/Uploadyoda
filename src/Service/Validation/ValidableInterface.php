<?php namespace Quasimodal\Uploadyoda\Service\Validation;

interface ValidableInterface 
{
    public function with(array $input);

    public function valid($action=null);

    public function passes();

    public function errors(); 
}
