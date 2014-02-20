<?php namespace Quasimodal\Uploadyoda;

interface UploadyodaUserRepositoryInterface
{
    public function create($user);

    public function getValidatorRules();
}
