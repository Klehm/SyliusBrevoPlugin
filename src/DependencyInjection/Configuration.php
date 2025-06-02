<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klehm_sylius_brevo');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('api_key')
                    ->info('API key for Brevo')
                    ->defaultValue('')
                    ->cannotBeEmpty()
                    ->end()
                ->arrayNode('templates')
                    ->useAttributeAsKey('locale')
                    ->arrayPrototype()
                        ->useAttributeAsKey('name')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
