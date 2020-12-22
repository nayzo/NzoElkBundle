<?php

namespace Nzo\ElkBundle\DependencyInjection;

use Nzo\ElkBundle\Encryptor\ElkEncryptor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class NzoElkExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('elk.app_name', $config['app_name']);
        $container->setParameter('elk.app_environment', $config['app_environment']);
        $container->setParameter('elk.log_encryptor', !empty($config['log_encryptor']) ? $config['log_encryptor'] : []);
    }

    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['NzoUrlEncryptorBundle'])) {
            $secretKey = !empty($config['log_encryptor']) ? $config['log_encryptor']['secret_key'] : 'secret_key';
            $newConfig = [
                'secret_key' => $container->resolveEnvPlaceholders($secretKey, true),
                'cipher_algorithm' => 'aes-128-cbc',
                'format_base64_output' => false,
            ];

            foreach ($container->getExtensions() as $name => $extension) {
                if ('nzo_encryptor' === $name) {
                    $container->prependExtensionConfig($name, $newConfig);
                    break;
                }
            }
        }
    }
}
