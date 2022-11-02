<?php

declare(strict_types=1);

namespace MintwareDe\NativeCronBundle;

use MintwareDe\NativeCronBundle\Attribute\CronJob;
use MintwareDe\NativeCronBundle\DependencyInjection\Compiler\CronJobRegistryCompilerPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MintwareDeNativeCronBundle extends AbstractBundle
{
    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../config/services.yaml');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerAttributeForAutoconfiguration(
            CronJob::class,
            static function (
                ChildDefinition $definition,
                CronJob $attribute,
                \Reflector $reflector,
            ): void {
                $definition->addTag(CronJob::CONTAINER_TAG, [
                    'name' => $attribute->getName(),
                    'execute_at' => $attribute->getExecuteAt(),
                    'arguments' => json_encode($attribute->getArguments()),
                ]);
            }
        );

        $container->addCompilerPass(new CronJobRegistryCompilerPass());
    }
}
