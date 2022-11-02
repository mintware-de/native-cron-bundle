<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Command;

use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mw:cron:list',
    description: 'List all existing cron jobs.'
)]
class ListCronJobsCommand extends Command
{
    public function __construct(
        private readonly CronJobRegistry $registry,
    ) {
        parent::__construct('mw:cron:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Cron jobs');

        $tableEntries = [];
        foreach ($this->registry as $cronJob) {
            $tableEntries[] = [
                $cronJob->getAnnotation()->getName(),
                $cronJob->getAnnotation()->getExecuteAt(),
                json_encode($cronJob->getAnnotation()->getArguments()),
                $cronJob->getCommand(),
            ];
        }

        $io->table(
            ['Name', 'Execute At', 'Arguments', 'Command'],
            $tableEntries,
        );

        return Command::SUCCESS;
    }
}
