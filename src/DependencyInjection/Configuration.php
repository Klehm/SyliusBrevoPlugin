<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klehm_sylius_brevo_mailer');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
