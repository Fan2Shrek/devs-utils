<?php

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;


/**
 * This is not the best way
 *
 * We should not invalidad all the container for this 
 * See SerializerCacheWarmer to find a better solution
 */
class TrackSerializerConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('serializer')) {
            return;
        }

        $resolvingBag = $container->getParameterBag();
        $frameworkConfig = $container->getExtensionConfig('framework');
        
        foreach ($frameworkConfig as $config) {
            if (($config['serializer']['mapping']['paths'] ?? false)) {
                $config = $resolvingBag->resolveValue($config);
                $paths = $config['serializer']['mapping']['paths'];

                foreach ($paths as $path) {

                    if (\is_dir($path)) {
                        $container->addResource(new DirectoryResource($path));
                    }
                }
            }
        }
    }
}
