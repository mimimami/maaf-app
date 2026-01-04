<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Example Test
 * 
 * This is an example test file to demonstrate how to write tests.
 */
final class ExampleTest extends TestCase
{
    public function testBasicAssertion(): void
    {
        $this->assertTrue(true);
    }

    public function testStringEquals(): void
    {
        $expected = 'Hello World';
        $actual = 'Hello World';
        
        $this->assertEquals($expected, $actual);
    }

    public function testArrayContains(): void
    {
        $array = [1, 2, 3, 4, 5];
        
        $this->assertContains(3, $array);
        $this->assertNotContains(10, $array);
    }
}
