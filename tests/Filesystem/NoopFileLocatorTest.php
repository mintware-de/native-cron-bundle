<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Filesystem;

use MintwareDe\NativeCron\Filesystem\CrontabFileLocatorInterface;
use MintwareDe\NativeCronBundle\Filesystem\NoopFileLocator;
use PHPUnit\Framework\TestCase;

class NoopFileLocatorTest extends TestCase
{
    private NoopFileLocator $locator;

    public function setUp(): void
    {
        $this->locator = new NoopFileLocator();
        self::assertInstanceOf(CrontabFileLocatorInterface::class, $this->locator);

        self::expectException(\Exception::class);
        self::expectExceptionMessage(NoopFileLocator::EXCEPTION_MSG);
    }

    public function testLocateDropInCrontabShouldThrow(): void
    {
        $this->locator->locateDropInCrontab('');
    }

    public function testLocateUserCrontabShouldThrow(): void
    {
        $this->locator->locateUserCrontab('');
    }

    public function testLocateSystemCrontabShouldThrow(): void
    {
        $this->locator->locateSystemCrontab();
    }
}
