<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;
use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;
use MAAF\Core\Application;

/**
 * Example Integration Test
 * 
 * This is a sample integration test file.
 */
final class ExampleIntegrationTest extends TestCase
{
    private ?Application $app = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test application
        $this->app = new Application(__DIR__ . '/../../');
    }

    public function testApplicationBootstrap(): void
    {
        $this->assertNotNull($this->app);
    }

    public function testRequestCreation(): void
    {
        $request = Request::fromGlobals();
        
        $this->assertInstanceOf(Request::class, $request);
    }
}

