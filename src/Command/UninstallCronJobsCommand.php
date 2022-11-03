<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Command;

use MintwareDe\NativeCron\Content\CronJobLine;
use MintwareDe\NativeCron\CrontabManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mw:cron:uninstall',
    description: 'Uninstall all existing native-cron-bundle cron jobs.'
)]
class UninstallCronJobsCommand extends Command
{
    public function __construct(
        private readonly CrontabManager $manager,
    ) {
        parent::__construct('mw:cron:uninstall');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $crontab = $this->manager->readDropInCrontab(InstallCronJobsCommand::DROP_IN_NAME);

        /** @var CronJobLine[] $removed */
        $removed = [];
        $crontab->removeWhere(function ($x) use (&$removed) {
            $isMatch = $x instanceof CronJobLine && str_contains($x->build(), 'mw:cron:run');
            if ($isMatch) {
                $removed[] = $x;
            }

            return $isMatch;
        });
        $io = new SymfonyStyle($input, $output);
        $io->title('Uninstall cron jobs');
        $io->warning('The following cron jobs will be uninstalled:');
        $io->writeln(implode("\n", array_map(fn ($x) => $x->build(), $removed)));

        if (!$io->confirm('Confirm uninstall?')) {
            return Command::SUCCESS;
        }

        $this->manager->writeDropInCrontab($crontab, InstallCronJobsCommand::DROP_IN_NAME);

        return Command::SUCCESS;
    }
}
