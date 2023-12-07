<?php

namespace MiPago\Bundle\DependencyInjection;

use MiPago\Bundle\Services\MiPagoService;
use MiPago\Bundle\Controller\PaymentController;
use MiPago\Bundle\Doctrine\PaymentManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MiPagoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->getDefinition(MiPagoService::class);
        $definition->replaceArgument(1, $config['cpr']);
        $definition->replaceArgument(2, $config['sender']);
        $definition->replaceArgument(3, $config['format']);
        $definition->replaceArgument(4, $config['suffixes']);
        $definition->replaceArgument(5, $config['language']);
        $definition->replaceArgument(6, $config['return_url']);
        $definition->replaceArgument(7, $config['test_environment']);
        $definition->replaceArgument(8, $config['payment_modes']);

        $definition2 = $container->getDefinition(PaymentController::class);
        $definition2->replaceArgument(0, $config['forwardController']);

        $definition3 = $container->getDefinition(PaymentManager::class);
        $definition3->replaceArgument(1, $config['payment_class']);
    }
}
