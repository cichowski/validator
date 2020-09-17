<?php

namespace Cichowski\Validator\Contracts;

interface LaravelValidator
{
    function validate($attribute, $value, array $parameters, $validator);
}