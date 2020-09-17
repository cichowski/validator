<?php

namespace Cichowski\Validator;

use Cichowski\Validator\Exceptions\ControlSumFormatException;

class ControlSumChecker
{
    public const FORMAT_RAW = null;
    public const FORMAT_MOD10 = 'mod10';
    public const FORMAT_INVERT = 'invert';
    public const FORMAT_X_FOR_10 = 'x';
    public const FORMAT_HEX = 'hex';

    /**
     * @var array|int[]
     */
    private $weights;
    /**
     * @var int
     */
    private $controlDigitIndex;
    /**
     * @var int
     */
    private $finalModuloBy;
    /**
     * @var string
     */
    private $controlCharFormat;
    /**
     * @var bool
     */
    private $disallowAllZeros = true;

    /**
     * @param array|int[] $weights
     * @param int $finalModuloBy
     * @param string|null $controlCharFormat
     */
    public function __construct(array $weights, int $finalModuloBy = 1, ?string $controlCharFormat = null)
    {
        $this->weights = $weights;
        $this->controlDigitIndex = count($weights);
        $this->finalModuloBy = $finalModuloBy;
        $this->controlCharFormat = strtolower($controlCharFormat);
    }

    /**
     * @param bool $disallowAllZeros
     */
    public function setDisallowAllZeros(bool $disallowAllZeros): void
    {
        $this->disallowAllZeros = $disallowAllZeros;
    }

    /**
     * @param mixed|array|string $value
     * @return bool
     */
    public function check($value): bool
    {
        $valueDigits = (is_array($value) ? $value : str_split($value));

        if ( ! isset($valueDigits[$this->controlDigitIndex])) {
            return false;
        }

        $calculatedSum = $this->calculate($valueDigits);

        if ($this->disallowAllZeros and $calculatedSum === 0) {
            return false;
        }

        return $valueDigits[$this->controlDigitIndex] == $this->adjustCheckSumToFormat($calculatedSum);
    }

    /**
     * @param array $valueDigits
     * @return int
     */
    public function calculate(array $valueDigits): int
    {
        $sum = 0;
        foreach ($this->weights as $pos => $weight) {
            $sum += (int)$valueDigits[$pos] * $weight;
        }

        return $sum;
    }

    /**
     * @param int $calculatedSum
     * @return string
     */
    private function adjustCheckSumToFormat(int $calculatedSum): string
    {
        $calculatedSumTail = $calculatedSum % $this->finalModuloBy;

        switch ($this->controlCharFormat) {

            case self::FORMAT_RAW:
                return (string)$calculatedSumTail;
            case self::FORMAT_MOD10:
                return (string)($calculatedSumTail % 10);
            case self::FORMAT_INVERT:
                return (string)($calculatedSumTail === 0 ? $calculatedSumTail : $this->finalModuloBy - $calculatedSumTail);
            case self::FORMAT_X_FOR_10:
                if ($calculatedSumTail == 10) {
                    return 'X';
                }
                throw new ControlSumFormatException('Format "' . self::FORMAT_X_FOR_10 . '" cannot be compelled.');
            case self::FORMAT_HEX:
                if ($calculatedSumTail < 16) {
                    return dechex($calculatedSumTail);
                }
                throw new ControlSumFormatException('Format "' . self::FORMAT_HEX . '" cannot be compelled.');
            default:
                throw new ControlSumFormatException('Unsupported format provided: ' . $this->controlCharFormat);
        }
    }
}