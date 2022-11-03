<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CronJob
{
    public const CONTAINER_TAG = 'mw_native_cron_job';

    /**
     * @param string               $name
     * @param string               $executeAt
     * @param array<string, mixed> $arguments
     */
    public function __construct(
        private readonly string $name,
        private readonly string $executeAt,
        /** @var array<string, mixed> */
        private readonly array $arguments = [],
        private readonly string $user = 'root',
    ) {
    }

    public function getExecuteAt(): string
    {
        return $this->executeAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
