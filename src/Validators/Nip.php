<?php namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;

class Nip extends BaseValidator
{
    public function validate($attribute, $value, array $parameters, $validator)
    {        
        if ($this->isDigit($value) and strlen($value) == 10) {
            
            $weights = array(6, 5, 7, 2, 3, 4, 5, 6, 7);

            return $this->checkSum($value, $weights, 11, null, false);
        }                 
        
        return false;        
    }  
}

