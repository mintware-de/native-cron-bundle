<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Command;

use MintwareDe\NativeCron\Content\CronJobLine;
use MintwareDe\NativeCron\CrontabManager;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mw:cron:install',
    description: 'Install all existing cron jobs.'
)]
class InstallCronJobsCommand extends Command
{
    public const DROP_IN_NAME = 'mw_native_cron_bundle';
    public function __construct(
        private readonly string $projectDir,
        private readonly CronJobRegistry $registry,
        private readonly CrontabManager $manager,
    ) {
        parent::__construct('mw:cron:install');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $crontab = $this->manager->readDropInCrontab(self::DROP_IN_NAME);
        $crontab
            ->removeWhere(fn ($c) => $c instanceof CronJobLine && str_contains($c->getCommand(), 'mw:cron:run'));

        $consoleFile = sprintf('%s/bin/console', $this->projectDir);

        foreach ($this->registry as $cronJob) {
            $crontab->add(
                new CronJobLine(
                    sprintf(
                        '%s root %s %s mw:cron:run %s',
                        $cronJob->getAnnotation()->getExecuteAt(),
                        PHP_BINARY,
                        $consoleFile,
                        $cronJob->getAnnotation()->getName(),
                    ),
                    true
                )
            );
        }

        $io->title('New crontab content');
        $io->writeln($crontab->build());

        if (!$io->confirm('Confirm crontab?')) {
            return Command::SUCCESS;
        }

        $this->manager->writeDropInCrontab($crontab, self::DROP_IN_NAME);

        return Command::SUCCESS;
    }

}
