<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\DependencyInjection;

use MintwareDe\NativeCronBundle\Attribute\CronJob;

class RegisteredCronJob
{
    public function __construct(
        private readonly CronJob $annotation,
        private readonly string $command,
    ) {
    }

    public function getAnnotation(): CronJob
    {
        return $this->annotation;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
