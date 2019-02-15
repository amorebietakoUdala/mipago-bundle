<?php

namespace MiPago\Bundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('mi_pago');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
		->scalarNode('cpr')->defaultValue('9052180')->end()
		->scalarNode('sender')->isRequired()->end()
                ->scalarNode('format')->defaultValue('521')->end()
		->arrayNode('suffixes')->prototype('scalar')->end()->end()
		->scalarNode('language')->defaultValue('eu')->end()
		->scalarNode('return_url')->isRequired()->end()
		->scalarNode('confirmation_url')->end()
		->scalarNode('forwardController')->end()
//		->arrayNode('payment_modes')->beforeNormalization()->castToArray()->end()
		->booleanNode('test_environment')->defaultFalse()->end()
//		->booleanPrototype('test_environment')->defaultFalse()->end()
            ->end()
        ;
	
        return $treeBuilder;
    }
}
