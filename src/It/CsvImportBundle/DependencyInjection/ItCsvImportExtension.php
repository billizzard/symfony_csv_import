<?php

namespace It\CsvImportBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ItCsvImportExtension extends Extension
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Add parameters form Resources/config/config.yml file
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $this->parseNode('it_csv_import.'.$key, $value);
        }

        $container->setParameter('it_csv_import', $config);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws \Exception
     */
    protected function parseNode($name, $value)
    {
        if (is_string($value)) {
            $this->set($name, $value);
            return;
        }

        if (is_integer($value)) {
            $this->set($name, $value);
            return;
        }

        if (is_array($value)) {
            foreach ($value as $newKey => $newValue) {
                $this->parseNode($name.'.'.$newKey, $newValue);
            }

            return;
        }

        if (is_bool($value)) {
            $this->set($name, $value);
            return;
        }

        throw new \Exception(gettype($value).' not supported');
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function set($key, $value)
    {
        $this->container->setParameter($key, $value);
    }
}
