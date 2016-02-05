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
     *  Check sum 
     *  First digit from $value that doesn't have corresponding weight is taken for check     
     *  
     *  @param mix $value                   value to chec if its connrect
     *                                      accepts: string, integer, array     
     *  @param array $weights               list of weights to count
     *  @param integer $modulo              modulo divider
     *  @param string $format               see: adjustCheckSumToFormat() method description
     *  @param int|bool $inverseResult      if not false, result is substracted from $modulo (or from given value)     
     *  @param boolean $disallowAllZeros    early return false if sum is zero (prevents 0000000 like numbers with correct sum) 
     *  @return boolean
     */         
    protected function checkSum($value, array $weights, $modulo, $format = null, $inverseResult = true, $disallowAllZeros = true)
    {   
        if ( ! isset($value[count($weights)])) {
            
            return false;
        }
        
        $sum = $this->calculateCheckSum($value, $weights);
        
        if ($disallowAllZeros and $sum === 0) {
            
            return false;
        }   
        
        $checkSum = $this->adjustCheckSumToFormat(($sum % $modulo), $format);
        
        if ($checkSum === false) {
            
            return false;
        }
        
        if ($inverseResult and $checkSum != 0) {
            
            if($inverseResult === true) {                
                $checkSum = $modulo - $checkSum;
            }
            elseif (is_numeric($inverseResult)) {                
                $checkSum = (int)$inverseResult - $checkSum;
            }            
            else {                
                throw new \InvalidArgumentException('Wrong argument 4 for' . __METHOD__ . '()');
            }
        }
        
        return $checkSum == (int)$value[count($weights)];
    }
    
    /**
     * Formats check sum to one character
     * 
     * @param integer $checkSum
     * @param string $format
     * @return boolean|string
     */
    private function adjustCheckSumToFormat($checkSum, $format)
    {
        if ($checkSum > 9) {
            
            switch (strtolower($format)) {            
                
                case 'mod10':
                    return $checkSum % 10;
                    
                case 'x':
                    if ($checkSum == 10) {
                        return 'X';                        
                    }
                    return false;

                case 'hex':
                    if ($checkSum < 16) {
                        return dechex($checkSum);
                    }                    
                    return false;

                default:
                    return false;
            }
        }
        
        return $checkSum;
    }
    
    /**
     * Calculate check sum
     * 
     * @param mix $value
     * @param array $weights
     * @return integer
     */
    protected function calculateCheckSum($value, array $weights)
    {
        $sum = 0;
        
        foreach ($weights as $pos => $weight) {
        
            $sum += (int)$value[$pos] * $weight;
        }        
        
        return $sum;
    }
}

