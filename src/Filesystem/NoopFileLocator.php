<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Filesystem;

use MintwareDe\NativeCron\Filesystem\CrontabFileLocatorInterface;

class NoopFileLocator implements CrontabFileLocatorInterface
{
    public const EXCEPTION_MSG = 'You need to specify a CrontabFileLocatorInterface. Check the README at https://github.com/mintware-de/native-cron-bundle.';

    public function locateDropInCrontab(string $name): string
    {
        throw new \Exception(self::EXCEPTION_MSG);
    }

    public function locateUserCrontab(string $username): string
    {
        throw new \Exception(self::EXCEPTION_MSG);
    }

    public function locateSystemCrontab(): string
    {
        throw new \Exception(self::EXCEPTION_MSG);
    }
}
