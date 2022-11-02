<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\DependencyInjection;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\RegisteredCronJob;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class RegisteredCronJobTest extends TestCase
{
    public function testExists(): void
    {
        $mockAnnotation = self::createMock(CronJob::class);
        $registered = new RegisteredCronJob($mockAnnotation, Command::class);

        self::assertEquals($mockAnnotation, $registered->getAnnotation());
        self::assertEquals(Command::class, $registered->getCommand());
    }
}
