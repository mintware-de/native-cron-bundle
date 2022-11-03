<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\Attribute;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use PHPUnit\Framework\TestCase;

class CronJobTest extends TestCase
{
    public function testConstructor(): void
    {
        $cronJob = new CronJob('example_cron_job', executeAt: '0 0 * * *', arguments: ['foo' => 'bar'], user: 'foo');
        self::assertEquals('example_cron_job', $cronJob->getName());
        self::assertEquals(['foo' => 'bar'], $cronJob->getArguments());

        self::assertEquals('0 0 * * *', $cronJob->getExecuteAt());
        self::assertEquals('foo', $cronJob->getUser());
    }
}
