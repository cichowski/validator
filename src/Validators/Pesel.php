<?php namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;
use Carbon\Carbon;

class Pesel extends BaseValidator
{   
    /**
     * Date of birth retrieved from PESEL number
     * 
     * @var Carbon\Carbon 
     */
    private $date;
    
    /**
     * Date of birth provided by $parameters in format acceptable by Carbon (and transformed into Carbon)
     * 
     * @var Carbon\Carbon
     */
    private $dateToConfront;
    
    /**
     * Gender provided by $parameters in ISO IEC 5218 format:
     * 1 = male
     * 2 = female
     * 
     * @var integer
     */
    private $genderToConfront;
    
    public function validate($attribute, $value, array $parameters, $validator)
    {   
        $this->setParameters($parameters);               
        
        if ($this->isDigit($value) and strlen($value) == 11) {
            
            $weights = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
            
            $genderDigit = (int)substr($value, 9, 1);           

            return  $this->checkSum($value, $weights, 10) and
                    $this->checkDateOfBirth($value) and
                    $this->confrontDateOfBirth() and
                    $this->confrontGender($genderDigit);
        }                 
        
        return false;        
    }
    
    private function setParameters(array $parameters)
    {
        while(count($parameters) and (! isset($this->dateToConfront) or ! isset($this->genderToConfront))) {
            
            $this->setSuitableParameter(array_shift($parameters));
        }        
    }
    
    private function setSuitableParameter($value)
    {
        if (in_array($value, [1,2]) and ! isset($this->genderToConfront)) {     
            
            return $this->genderToConfront = (int)$value;
        }         
        elseif (! isset($this->dateToConfront)) {
           
            try {             
                $date = new Carbon($value);
                
                return $this->dateToConfront = $date;
                
            } catch (\Exception $ex) { 
                
                //do nothing, Exception is thrown anyway if value is not recognized
            }                        
        } 
        
        throw new \InvalidArgumentException('Unknown parameter passed to PESEL validator.');
    }
    
    private function checkDateOfBirth($pesel)
    {        
        $monthDigits = (int)substr($pesel, 2, 2);
        
        $year = $this->getCenturyFromMonthDigits($monthDigits) . (int)substr($pesel, 0, 2);
        $month = $monthDigits % 20;
        $day = (int)substr($pesel, 4, 2);
        
        if ( ! checkdate($month, $day, $year)) {
            
            return false;
        }
        
        $this->date = Carbon::create($year, $month, $day, 0, 0, 0);        
        
        return true;
    }    
    
    private function getCenturyFromMonthDigits($monthDigits)
    {       
        switch (floor($monthDigits / 20)) {
            
            case 0:
                return 19;
            case 1:
                return 20;
            case 2:
                return 21;
            case 3:
                return 22;
            case 4:
                return 18;
        }
    }
    
    private function confrontDateOfBirth()
    {         
        if (isset($this->dateToConfront)) {
            
            return $this->date->diffInDays($this->dateToConfront) == 0;
        }
        return true;
    }
    
    private function confrontGender($genderDigit)
    {        
        if (isset($this->genderToConfront)) {
            
            return ($genderDigit % 2) == ($this->genderToConfront % 2);
        }
        return true;
    }
}

