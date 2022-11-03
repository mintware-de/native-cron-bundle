<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Command;

use MintwareDe\NativeCronBundle\Command\ListCronJobsCommand;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ListCronJobsCommandTest extends TestCase
{
    private ListCronJobsCommand $command;
    private CronJobRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new CronJobRegistry();
        $this->command = new ListCronJobsCommand($this->registry);
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(Command::class, $this->command);
    }

    public function testShouldOutputRegisteredCronjobs(): void
    {
        $this->registry->register(
            'foo',
            '0 0 * * *',
            '{"a":"b"}',
            'MyCommand',
            'root'
        );

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        self::assertStringContainsString('foo', $commandTester->getDisplay());
        self::assertStringContainsString('{"a":"b"}', $commandTester->getDisplay());
        self::assertStringContainsString('MyCommand', $commandTester->getDisplay());
        self::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
