<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Command;

use MintwareDe\NativeCron\Content\CronJobLine;
use MintwareDe\NativeCron\Content\Crontab;
use MintwareDe\NativeCron\CrontabManager;
use MintwareDe\NativeCronBundle\Command\InstallCronJobsCommand;
use MintwareDe\NativeCronBundle\Command\UninstallCronJobsCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UninstallCronJobsCommandTest extends TestCase
{
    private UninstallCronJobsCommand $command;
    private CrontabManager&MockObject $mockCrontabManager;

    protected function setUp(): void
    {
        $this->mockCrontabManager = self::createMock(CrontabManager::class);
        $this->command = new UninstallCronJobsCommand($this->mockCrontabManager);
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(Command::class, $this->command);
    }

    public function testExecute(): void
    {
        $tester = new CommandTester($this->command);

        $mockCrontab = self::createMock(Crontab::class);
        $mockCrontab
            ->expects(self::once())
            ->method('removeWhere')
            ->with(
                self::callback(function ($filter) {
                    return $filter(new CronJobLine('* * * * * root mw:cron:run', true))
                        && !$filter(new CronJobLine('* * * * * root different', true));
                })
            );

        $this->mockCrontabManager
            ->expects(self::once())
            ->method('readDropInCrontab')
            ->with(InstallCronJobsCommand::DROP_IN_NAME)
            ->willReturn($mockCrontab);

        $this->mockCrontabManager
            ->expects(self::once())
            ->method('writeDropInCrontab')
            ->with($mockCrontab, InstallCronJobsCommand::DROP_IN_NAME);

        $tester->execute([]);

        $display = $tester->getDisplay();
        self::assertStringContainsString('Uninstall cron jobs', $display);
        self::assertStringContainsString('The following cron jobs will be uninstalled:', $display);
        self::assertStringContainsString('mw:cron:run', $display);
        self::assertStringNotContainsString('different', $display);
        self::assertStringContainsString('Confirm uninstall?', $display);

        self::assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }
}
