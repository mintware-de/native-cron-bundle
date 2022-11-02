<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Command;

use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mw:cron:run',
    description: 'Runs a specific cron job'
)]
class RunCronJobCommand extends Command
{
    public function __construct(
        private readonly CronJobRegistry $registry,
    ) {
        parent::__construct('mw:cron:run');
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the cron job.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = strval($input->getArgument('name'));
        $io = new SymfonyStyle($input, $output);

        if (!$this->registry->hasCronJob($name)) {
            $io->error('Cronjob '.$name.' not found.');

            return Command::FAILURE;
        }

        $job = $this->registry->getCronJob($name);
        $command = $this->getApplication()->find($job->getCommand());

        $input = new ArrayInput($job->getAnnotation()->getArguments());
        $input->setInteractive(false);

        return $command->run($input, $io);
    }

}
