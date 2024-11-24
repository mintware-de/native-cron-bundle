<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\DependencyInjection\Compiler;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CronJobRegistryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registryDefinition = $container->getDefinition(CronJobRegistry::class);
        $cronJobs = $container->findTaggedServiceIds(CronJob::CONTAINER_TAG);
        foreach ($cronJobs as $cronJob) {
            $this->handleSingleCronjob($cronJob[0], $registryDefinition);
        }
    }

    private function handleSingleCronjob(mixed $cronJob, Definition $registryDefinition): void
    {
        /** @var array{name: string, execute_at: string, arguments: string, command: string, user: string} $first */
        $first = $cronJob;
        $registryDefinition->addMethodCall(
            'register',
            [
                $first['name'],
                $first['execute_at'],
                $first['arguments'],
                $first['command'],
                $first['user'],
            ]
        );
    }
}
