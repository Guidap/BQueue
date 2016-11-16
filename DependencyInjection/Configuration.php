<?php

namespace Strnoar\BQueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('strnoar_b_queue');

        $rootNode->children()
                    ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                    ->integerNode('port')->defaultValue(11300)->end()
                    ->scalarNode('default')->defaultValue('default')->end()
                    ->enumNode('adapter')->values(['sync', 'beanstalkd'])->defaultValue('sync')->end()
                    ->integerNode('tries')->defaultValue(1)->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
