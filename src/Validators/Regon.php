<?php namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;
use Cichowski\Validator\Contracts\LaravelValidator;

class Regon extends BaseValidator implements LaravelValidator
{
    private const VALIDATION_WEIGHTS_9 = [8, 9, 2, 3, 4, 5, 6, 7];
    private const VALIDATION_WEIGHTS_14 = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];

    public function validate($attribute, $value, array $parameters, $validator)
    {        
        if ( ! $this->isDigit($value)) {
            return false;
        }

        switch (strlen($value)) {
            case 9:
                return $this->validateRegon9($value);
            case 14:
                return $this->validateRegon9(substr($value, 0, 9))
                    && $this->validateRegon14($value);
            default:
                return false;
        }
    }    
    
    private function validateRegon9($value)
    {
        return $this->checkSum($value, self::VALIDATION_WEIGHTS_9, 11, 'mod10');
    }
    
    private function validateRegon14($value)
    {
        return $this->checkSum($value, self::VALIDATION_WEIGHTS_14, 11, 'mod10');
    }
}

