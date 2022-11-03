<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Command;

use MintwareDe\NativeCron\Content\CronJobLine;
use MintwareDe\NativeCron\Content\Crontab;
use MintwareDe\NativeCron\CrontabManager;
use MintwareDe\NativeCronBundle\Command\InstallCronJobsCommand;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class InstallCronJobsCommandTest extends TestCase
{
    private InstallCronJobsCommand $command;
    private CronJobRegistry $registry;
    private CrontabManager&MockObject $mockCrontabManager;

    protected function setUp(): void
    {
        $this->registry = new CronJobRegistry();
        $this->mockCrontabManager = self::createMock(CrontabManager::class);
        $this->command = new InstallCronJobsCommand('/root', $this->registry, $this->mockCrontabManager);
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(Command::class, $this->command);
    }

    public function testExecute(): void
    {
        $this->registry->register(
            'app_cron',
            '0 0 * * *',
            '{"a":"b"}',
            'MyCommand'
        );

        $mockCrontab = self::createMock(Crontab::class);

        $this->mockCrontabManager
            ->expects(self::once())
            ->method('readDropInCrontab')
            ->with(InstallCronJobsCommand::DROP_IN_NAME)
            ->willReturn($mockCrontab);

        $mockCrontab
            ->expects(self::once())
            ->method('removeWhere');
        $mockCrontab
            ->expects(self::atLeastOnce())
            ->method('build')
            ->willReturn('{{content}}');

        $mockCrontab
            ->expects(self::once())
            ->method('add')
            ->with(
                self::callback(function ($x) {
                    return $x instanceof CronJobLine
                        && str_contains($x->getCommand(), PHP_BINARY.' /root/bin/console mw:cron:run app_cron')
                        && $x->getDateTimeDefinition()->build() === '0 0 * * *';
                })
            );

        $this->mockCrontabManager
            ->expects(self::once())
            ->method('writeDropInCrontab')
            ->with($mockCrontab, InstallCronJobsCommand::DROP_IN_NAME);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $display = $commandTester->getDisplay();
        self::assertStringContainsString('New crontab content', $display);
        self::assertStringContainsString('{{content}}', $display);
        self::assertStringContainsString('Confirm crontab?', $display);

        self::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
