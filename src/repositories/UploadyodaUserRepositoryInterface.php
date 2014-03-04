<?php namespace Quasimodal\Uploadyoda\repositories;

interface UploadyodaUserRepositoryInterface
{
    public function create($user);

    public function getValidatorRules();

    public function count();
}
