<?php namespace Cichowski\Validator;

abstract class BaseValidator
{
    abstract protected function validate($attribute, $value, array $parameters, $validator);
    
    /**
     *  Check if value is digit
     * 
     *  @param mix $value
     *  @return boolean
     */
    protected function isDigit($value)
    {        
        return ctype_digit($value);        
    }
    
    /**
     *  Chek sum 
     *  First digit from $value that doesn't have corresponding weight is taken for check     
     *  
     *  @param mix $value                   value to chec if its connrect
     *                                      accepts: string, integer, array     
     *  @param array $weights               list of weights to count
     *  @param integer $mod                 modulo divider
     *  @param boolean $disallowAllZeros    disallow the value to consist of only zeros                     
     *  @return boolean
     */         
    protected function checkSum($value, array $weights, $mod = 11, $disallowAllZeros = true)
    {
        $sum = 0;
        
        foreach ($weights as $pos => $weight) {
        
            $sum += $value[$pos] * $weight;
        }
        
        if ($disallowAllZeros and $sum === 0) {
            
            return false;
        }
        
        return (($sum % $mod) % 10) == $value[count($weights)];
    }     
}

