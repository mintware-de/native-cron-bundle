<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Command;

use MintwareDe\NativeCronBundle\Command\RunCronJobCommand;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class RunCronJobCommandTest extends TestCase
{
    private CronJobRegistry $registry;
    private RunCronJobCommand $command;

    protected function setUp(): void
    {
        $this->registry = new CronJobRegistry();
        $this->command = new RunCronJobCommand($this->registry);
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(Command::class, $this->command);
    }

    public function testShouldFailIfTheCronjobWasNotFound(): void
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(['name' => 'foo']);

        $display = $commandTester->getDisplay();
        self::assertStringContainsString('Cronjob foo not found.', $display);

        self::assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testConfigure(): void
    {
        $arguments = $this->command->getDefinition()->getArguments();
        self::assertCount(1, $arguments);
        self::assertEquals('name', $arguments['name']->getName());
    }
}
