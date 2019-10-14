<?php
/*
 * This file is part of the Jprud67/TranslatorBundle
 *
 * (c) Prudence Dieudonné ASSOGBA <jprud67@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Jprud67\TranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode'))
        {
            $treeBuilder = new TreeBuilder('jprud67_translator');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('jprud67_translator');
        }

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->useAttributeAsKey('locale')
                    ->prototype('scalar')->end()
                ->end() // locales
            ->end()
        ;

        return $treeBuilder;
    }
}