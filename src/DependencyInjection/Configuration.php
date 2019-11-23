<?php

/*
 * This file is part of the DataMigrationBundle.
 *
 * (c) Grégoire Hébert <gregoire@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\DependencyInjection;

use Doctrine\Migrations\Configuration\Configuration as BaseConfiguration;
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
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('data_migrations');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('data_migrations');
        }

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
        $refClass = new \ReflectionClass(BaseConfiguration::class);
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
