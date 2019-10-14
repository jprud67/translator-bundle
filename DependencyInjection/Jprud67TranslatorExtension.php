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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Jprud67TranslatorExtension extends Extension
{

    /**
     * Jprud67TranslatorExtension constructor.
     */
    public function __construct()
    {
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('Jprud67\TranslatorBundle\Controller\TranslationController');
        $definition->replaceArgument(4, $config['locales']);

       // $container->setParameter($this->getAlias()."types",$config['types']);

    }

    public function getAlias(): string
    {
        return 'jprud67_translator';
    }

}