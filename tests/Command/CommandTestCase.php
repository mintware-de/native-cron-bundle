<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class CommandTestCase extends TestCase
{
    protected InputInterface&MockObject $mockInput;
    protected ConsoleOutput&MockObject $mockOutput;

    protected function setUp(): void
    {
        $this->mockInput = self::createMock(InputInterface::class);
        $this->mockOutput = self::createMock(ConsoleOutput::class);
        parent::setUp();
    }

}

