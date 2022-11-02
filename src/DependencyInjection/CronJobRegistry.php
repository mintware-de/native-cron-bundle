<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\DependencyInjection;

use MintwareDe\NativeCronBundle\Attribute\CronJob;

/**
 * @implements \Iterator<RegisteredCronJob>
 */
class CronJobRegistry implements \Iterator
{
    private int $index = 0;

    /** @var RegisteredCronJob[] */
    private array $registeredCronJobs = [];

    /** @var array<string, int> */
    private array $cronJobsByName = [];

    public function current(): ?RegisteredCronJob
    {
        if (isset($this->registeredCronJobs[$this->index])) {
            return $this->registeredCronJobs[$this->index];
        }

        return null;
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->registeredCronJobs[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function register(string $name, string $executeAt, string $jsonArguments, string $command): void
    {
        if ($this->hasCronJob($name)) {
            throw new \RuntimeException(sprintf('A cron job with the name %s is already registered.', $name));
        }

        $arguments = json_decode($jsonArguments, true);
        if (!is_array($arguments)) {
            $arguments = [];
        }
        $this->registeredCronJobs[] = new RegisteredCronJob(
            new CronJob(
                $name,
                $executeAt,
                $arguments
            ),
            $command,
        );

        $newIndex = count($this->registeredCronJobs) - 1;
        $this->cronJobsByName[$name] = $newIndex;
    }

    public function hasCronJob(string $name): bool
    {
        return isset($this->cronJobsByName[$name]);
    }

    public function getCronJob(string $name): RegisteredCronJob
    {
        if (!$this->hasCronJob($name)) {
            throw new \RuntimeException(sprintf('There is no cron job with the name %s.', $name));
        }
        $index = $this->cronJobsByName[$name];

        return $this->registeredCronJobs[$index];
    }
}
