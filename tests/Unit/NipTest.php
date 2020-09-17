<?php
namespace Cichowski\Tests\Unit;

use Cichowski\Validator\Validators\Nip;
use PHPUnit\Framework\TestCase;

class NipTest extends TestCase
{
    public $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Nip();
    }

    /**
     * @dataProvider correctNipDataProvider()
     * @param $number
     */
    public function testCorrectNipPassValidation($number): void
    {
        self::assertTrue($this->sut->validate(null, $number, [], null));
    }

    /**
     * @dataProvider wrongNipDataProvider()
     * @param $number
     */
    public function testWrongNipDoNotPassValidation($number): void
    {
        self::assertFalse($this->sut->validate(null, $number, [], null));
    }

    public function correctNipDataProvider(): array
    {
        return [
            [3560427310],
            [3594201702],
            [7744339954],
        ];
    }

    public function wrongNipDataProvider(): array
    {
        return [
            [0000000000],
            [3560427311],
            [3560427312],
            [3560427313],
            [3560427314],
            [3560427315],
            [3560427316],
            [3560427317],
            [3560427318],
            [3560427319],
            [359420170],
            [77443399541],
        ];
    }
}