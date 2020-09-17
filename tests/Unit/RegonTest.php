<?php
namespace Cichowski\Tests\Unit;

use Cichowski\Validator\Validators\Regon;
use PHPUnit\Framework\TestCase;

class RegonTest extends TestCase
{
    public $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Regon();
    }

    /**
     * @dataProvider correctRegonDataProvider()
     * @param $number
     */
    public function testCorrectRegonPassValidation($number): void
    {
        self::assertTrue($this->sut->validate(null, $number, [], null));
    }

    /**
     * @dataProvider wrongRegonDataProvider()
     * @param $number
     */
    public function testWrongRegonDoNotPassValidation($number): void
    {
        self::assertFalse($this->sut->validate(null, $number, [], null));
    }

    public function correctRegonDataProvider(): array
    {
        return [
            [779469802],
            [399054334],
            [43925867832812],
            [15412224147440],
        ];
    }

    public function wrongRegonDataProvider(): array
    {
        return [
            [000000000],
            [77946980],
            [3990543341],
            [00000000000000],
            [4392586783281],
            [152065941478691],
            [15412224147441],
            [15412224147442],
            [15412224147443],
            [15412224147444],
            [15412224147445],
            [15412224147446],
            [15412224147447],
            [15412224147448],
            [15412224147449],
        ];
    }
}