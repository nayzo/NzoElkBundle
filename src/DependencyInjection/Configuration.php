<?php

declare(strict_types=1);

namespace Nzo\ElkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nzo_elk');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('app_name')
                    ->isRequired()
                ->end()
                ->scalarNode('app_environment')
                    ->isRequired()
                    ->defaultValue('local')
                ->end()
                ->arrayNode('log_encryptor')
                    ->children()
                        ->scalarNode('secret_key')
                            ->isRequired()
                        ->end()
                        ->arrayNode('fields')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
