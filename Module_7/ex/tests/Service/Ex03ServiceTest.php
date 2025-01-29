<?php

namespace App\Tests\Service;

use App\Service\Ex03Service;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Ex03ServiceTest extends KernelTestCase
{
    private Ex03Service $service;

    protected function setUp(): void
    {
        $this->service = new Ex03Service();
    }

    /**
     * @dataProvider provideUppercaseWordsData
     */
    public function testUppercaseWords(string $input, string $expected): void
    {
        $result = $this->service->uppercaseWords($input);
        $this->assertEquals($expected, $result);
    }

    public function provideUppercaseWordsData(): array
    {
        return [
            'simple string' => [
                'hello world',
                'Hello World'
            ],
            'mixed case string' => [
                'hElLo WoRlD',
                'Hello World'
            ],
            'string with numbers' => [
                'hello 42 world',
                'Hello 42 World'
            ]
        ];
    }

    /**
     * @dataProvider provideCountNumbersData
     */
    public function testCountNumbers(string $input, int $expected): void
    {
        $result = $this->service->countNumbers($input);
        $this->assertEquals($expected, $result);
    }

    public function provideCountNumbersData(): array
    {
        return [
            'no numbers' => [
                'hello world',
                0
            ],
            'single number' => [
                'hello 4 world',
                1
            ],
            'multiple numbers' => [
                'hello 42 world 123',
                5
            ]
        ];
    }
}