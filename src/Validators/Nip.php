<?php
namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;
use Cichowski\Validator\Contracts\LaravelValidator;

class Nip extends BaseValidator implements LaravelValidator
{
    private const VALIDATION_WEIGHTS = [6, 5, 7, 2, 3, 4, 5, 6, 7];

    public function validate($attribute, $value, array $parameters, $validator): bool
    {
        return $this->isDigit($value)
            && strlen($value) === 10
            && $this->checkSum($value, self::VALIDATION_WEIGHTS, 11);
    }
}

