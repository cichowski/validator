<?php namespace Cichowski\Validator\Validators;

use Cichowski\Validator\BaseValidator;
use Carbon\Carbon;
use Cichowski\Validator\Contracts\LaravelValidator;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Pesel extends BaseValidator implements LaravelValidator
{   
    /**
     * Date of birth retrieved from PESEL number
     * 
     * @var Carbon
     */
    private $date;
    
    /**
     * Date of birth provided by $parameters in format acceptable by Carbon (and transformed into Carbon)
     * 
     * @var Carbon
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
        
        if ($this->isDigit($value) && strlen($value) === 11) {
            
            $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
            
            $genderDigit = (int)substr($value, 9, 1);           

            return  $this->checkSum($value, $weights, 10, 'invert') and
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

            } catch (Exception $ex) {

                throw new InvalidArgumentException('Unable to read provided date of birth.');
            }
        }

        throw new InvalidArgumentException('Unknown parameter passed to PESEL validator.');
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
        
        $this->date = Carbon::create($year, $month, $day);
        
        return true;
    }    
    
    private function getCenturyFromMonthDigits(int $monthDigits): int
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

        throw new RuntimeException('Problem with date of birth calculation occurred.');
    }
    
    private function confrontDateOfBirth(): bool
    {         
        if (isset($this->dateToConfront)) {
            
            return $this->date->diffInDays($this->dateToConfront) == 0;
        }

        return true;
    }
    
    private function confrontGender(int $genderDigit): bool
    {        
        if (isset($this->genderToConfront)) {
            
            return ($genderDigit % 2) == ($this->genderToConfront % 2);
        }

        return true;
    }
}

