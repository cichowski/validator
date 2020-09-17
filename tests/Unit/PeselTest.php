<?php
namespace Cichowski\Tests\Unit;

use Cichowski\Validator\Validators\Pesel;
use PHPUnit\Framework\TestCase;

class PeselTest extends TestCase
{
    public $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Pesel();
    }

    /**
     * @dataProvider correctPeselDataProvider()
     * @param $number
     */
    public function testCorrectPeselPassValidation($number): void
    {
        self::assertTrue($this->sut->validate(null, $number, [], null));
    }

    /**
     * @dataProvider wrongPeselDataProvider()
     * @param $number
     */
    public function testWrongPeselDoNotPassValidation($number): void
    {
        self::assertFalse($this->sut->validate(null, $number, [], null));
    }

    /**
     * @todo: add more tests for date of birth check & gender check
     * @tags: #drk#
     */

    public function correctPeselDataProvider(): array
    {
        return [
            [34030610331],
            [22092206320],
        ];
    }

    public function wrongPeselDataProvider(): array
    {
        return [
            [00000000000],
            [22092206321],
            [22092206322],
            [22092206323],
            [22092206324],
            [22092206325],
            [22092206326],
            [22092206327],
            [22092206328],
            [22092206329],
        ];
    }
}