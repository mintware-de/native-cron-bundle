<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\DependencyInjection;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use MintwareDe\NativeCronBundle\DependencyInjection\RegisteredCronJob;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class CronJobRegistryTest extends TestCase
{
    private CronJobRegistry $registry;

    public function setUp(): void
    {
        $this->registry = new CronJobRegistry();
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(\Iterator::class, $this->registry);
    }

    public function testRegisterCronJobShouldFailForDuplicateName(): void
    {
        $this->registry->register('foo_bar', '', '{}', Command::class, 'root');

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('A cron job with the name foo_bar is already registered.');
        $this->registry->register('foo_bar', '', '{}', Command::class, 'root');
    }

    public function testRegisterCronJob(): void
    {
        self::assertCount(0, $this->registry);
        $this->registry->register('foo_bar', '0 0 * * *', '{}', Command::class, 'root');
        self::assertCount(1, $this->registry);
        self::assertTrue($this->registry->hasCronJob('foo_bar'));
        $fooBar = $this->registry->getCronJob('foo_bar');
        self::assertInstanceOf(RegisteredCronJob::class, $fooBar);
        self::assertInstanceOf(CronJob::class, $fooBar->getAnnotation());
        self::assertEquals(Command::class, $fooBar->getCommand());
    }

    public function testGetCronJobShouldThrowWhenMissing(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('There is no cron job with the name foo_bar.');
        $this->registry->getCronJob('foo_bar');
    }
}
