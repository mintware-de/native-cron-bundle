<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests\DependencyInjection\Compiler;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\Compiler\CronJobRegistryCompilerPass;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CronJobRegistryCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $compilerPass = new CronJobRegistryCompilerPass();
        self::assertInstanceOf(CompilerPassInterface::class, $compilerPass);

        $mockDefinition = self::createMock(Definition::class);

        $mockContainerBuilder = self::createMock(ContainerBuilder::class);
        $mockContainerBuilder
            ->expects(self::atLeastOnce())
            ->method('getDefinition')
            ->with(CronJobRegistry::class)
            ->willReturn($mockDefinition);

        $mockContainerBuilder
            ->expects(self::atLeastOnce())
            ->method('findTaggedServiceIds')
            ->with(CronJob::CONTAINER_TAG)
            ->willReturn([
                'MyCommand' => [[
                    'name' => 'my_cron_job',
                    'execute_at' => '0 0 * * *',
                    'arguments' => json_encode(['arg1' => 'foo']),
                    'command' => 'MyCommand',
                    'user' => 'foo',
                ]],
                'MyCommand2' => [[
                    'name' => 'my_cron_job2',
                    'execute_at' => '0 1 * * *',
                    'arguments' => json_encode(['arg2' => 'foo']),
                    'command' => 'MyCommand2',
                    'user' => 'bar',
                ]],
            ]);

        $mockDefinition
            ->expects(self::exactly(2))
            ->method('addMethodCall')
            ->withConsecutive(
                [
                    'register',
                    [
                        'my_cron_job',
                        '0 0 * * *',
                        json_encode(['arg1' => 'foo']),
                        'MyCommand',
                        'foo',
                    ],
                ],
                [
                    'register',
                    [
                        'my_cron_job2',
                        '0 1 * * *',
                        json_encode(['arg2' => 'foo']),
                        'MyCommand2',
                        'bar',
                    ],
                ],
            );

        $compilerPass->process($mockContainerBuilder);
    }
}
