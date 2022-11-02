<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\Tests;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\Compiler\CronJobRegistryCompilerPass;
use MintwareDe\NativeCronBundle\MintwareDeNativeCronBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MintwareDeNativeCronBundleTest extends TestCase
{
    private MintwareDeNativeCronBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new MintwareDeNativeCronBundle();
    }

    public function testInheritance(): void
    {
        self::assertInstanceOf(AbstractBundle::class, $this->bundle);
    }

    public function testBuild(): void
    {
        $mockContainerBuilder = self::createMock(ContainerBuilder::class);
        $mockContainerBuilder
            ->expects(self::atLeastOnce())
            ->method('addCompilerPass')
            ->with(
                self::callback(fn ($pass) => $pass instanceof CronJobRegistryCompilerPass)
            );

        $mockChildDefinition = self::createMock(ChildDefinition::class);
        $mockChildDefinition
            ->expects(self::once())
            ->method('addTag')
            ->with(CronJob::CONTAINER_TAG, [
                'name' => 'foo_cron_job',
                'execute_at' => '0 0 * * *',
                'arguments' => json_encode(['foo' => 'bar']),
            ]);

        $mockCronJob = self::createMock(CronJob::class);
        $mockCronJob
            ->expects(self::once())
            ->method('getName')
            ->willReturn('foo_cron_job');

        $mockCronJob
            ->expects(self::once())
            ->method('getArguments')
            ->willReturn(['foo' => 'bar']);

        $mockCronJob
            ->expects(self::once())
            ->method('getExecuteAt')
            ->willReturn('0 0 * * *');

        $mockReflector = self::createMock(\ReflectionClass::class);

        $mockContainerBuilder
            ->expects(self::atLeastOnce())
            ->method('registerAttributeForAutoconfiguration')
            ->with(
                CronJob::class,
                self::callback(function ($x) use ($mockChildDefinition, $mockCronJob, $mockReflector) {
                    $x($mockChildDefinition, $mockCronJob, $mockReflector);

                    return true;
                })
            );

        $this->bundle->build($mockContainerBuilder);
    }
}
