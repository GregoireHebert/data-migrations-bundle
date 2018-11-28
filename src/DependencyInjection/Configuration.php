<?php

namespace Gheb\DataMigrationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder The config tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('data_migrations', 'array');

        $organizeMigrationModes = $this->getOrganizeMigrationsModes();

        /* @var NodeBuilder $children */
        $rootNode->children()
            ->scalarNode('dir_name')->defaultValue('%kernel.root_dir%/DataMigrations')->cannotBeEmpty()->end()
            ->scalarNode('namespace')->defaultValue('Application\DataMigrations')->cannotBeEmpty()->end()
            ->scalarNode('table_name')->defaultValue('data_migration_versions')->cannotBeEmpty()->end()
            ->scalarNode('name')->defaultValue('Application Data Migrations')->end()
            ->scalarNode('custom_template')->defaultValue(null)->end()
            ->scalarNode('organize_migrations')->defaultValue(false)
            ->info('Organize migrations mode. Possible values are: "BY_YEAR", "BY_YEAR_AND_MONTH", false')
            ->validate()
                ->ifTrue(function ($v) use ($organizeMigrationModes) {
                    if (false === $v) {
                        return false;
                    }

                    if (\is_string($v) && \in_array(strtoupper($v), $organizeMigrationModes, true)) {
                        return false;
                    }

                    return true;
                })
                ->thenInvalid('Invalid organize migrations mode value %s')
            ->end()
            ->validate()
                ->ifString()
                ->then(function ($v) {
                    return \constant('Doctrine\Migrations\Configuration\Configuration::VERSIONS_ORGANIZATION_'.strtoupper($v));
                })
            ->end()
        ->end()
        ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Find organize migrations modes for their names.
     *
     * @return string[]
     */
    private function getOrganizeMigrationsModes()
    {
        $constPrefix = 'VERSIONS_ORGANIZATION_';
        $prefixLen = \strlen($constPrefix);
        $refClass = new \ReflectionClass('Doctrine\DBAL\Migrations\Configuration\Configuration');
        $constsArray = $refClass->getConstants();
        $namesArray = [];

        foreach ($constsArray as $key => $value) {
            if (0 !== strpos($key, $constPrefix)) {
                continue;
            }

            $namesArray[] = substr($key, $prefixLen);
        }

        return $namesArray;
    }
}
