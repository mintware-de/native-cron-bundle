<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle\DependencyInjection\Compiler;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\CronJobRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CronJobRegistryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registryDefinition = $container->getDefinition(CronJobRegistry::class);
        $cronJobs = $container->findTaggedServiceIds(CronJob::CONTAINER_TAG);
        foreach ($cronJobs as $id => $cronJob) {
            $first = $cronJob[0];
            $registryDefinition->addMethodCall(
                'register',
                [
                    $first['name'],
                    $first['execute_at'],
                    $first['arguments'],
                    $first['command'],
                ]
            );
        }
    }
}
