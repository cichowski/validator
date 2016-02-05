<?php namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;

class Regon extends BaseValidator
{
    public function validate($attribute, $value, array $parameters, $validator)
    {        
        if ($this->isDigit($value)) {
            
            if (strlen($value) == 9) {
                            
                return $this->validateRegon9($value);
            }
            elseif(strlen($value) == 14) {
                
                if ($this->validateRegon9(substr($value, 0, 9))) {                
                    
                    return $this->validateRegon14($value);            
                }
            }      
        }                 
        
        return false;        
    }    
    
    private function validateRegon9($value)
    {
        $weights = array(8, 9, 2, 3, 4, 5, 6, 7);

        return $this->checkSum($value, $weights, 11, 'mod10', false);        
    }
    
    private function validateRegon14($value)
    {
        $weights = array(2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8);

        return $this->checkSum($value, $weights, 11, 'mod10', false);         
    }
}

