<?php // lint >= 8.1

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MyTestCase extends TestCase
{
    private LoggerInterface&m\MockInterface $loggerMock;

    protected function setUp(): void
    {
        $this->loggerMock = m::mock(LoggerInterface::class);
    }
}
