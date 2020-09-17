<?php
namespace Cichowski\Validator;

abstract class BaseValidator
{
    /**
     *  Check if value is digit
     * 
     *  @param mixed $value
     *  @return bool
     */
    protected function isDigit($value): bool
    {        
        return ctype_digit($value);        
    }

    /**
     *  Check sum
     *  First character from $value that doesn't have corresponding weight is taken for check
     *
     * @param mixed $value value to verify
     * @param array|int[] $weights list of weights to count
     * @param int $modulo modulo divider
     * @param string|null $format see: adjustCheckSumToFormat() method description
     * @return bool
     */
    protected function checkSum($value, array $weights, int $modulo, ?string $format = null): bool
    {
        return (new ControlSumChecker($weights, $modulo, $format))->check($value);
    }
}

