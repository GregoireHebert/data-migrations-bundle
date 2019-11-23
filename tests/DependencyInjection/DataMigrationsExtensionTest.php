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

namespace Gheb\DataMigrationsBundle\Tests\DependencyInjection;

use Gheb\DataMigrationsBundle\DependencyInjection\DataMigrationsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class DataMigrationsExtensionTest extends TestCase
{
    const DEFAULT_CONFIG = ['data_migration' => [
        'dir_name' => '%kernel.root_dir%/DataMigrations',
        'namespace' => 'Application\DataMigrations',
        'table_name' => 'data_migration_versions',
        'name' => 'Application Data Migrations',
        'custom_template' => null,
        'organize_migrations' => false,
    ]];

    private $container;
    private $extension;

    protected function setUp(): void
    {
        $this->container = $this->getContainer();
        $this->extension = new DataMigrationsExtension();
    }

    public function testLoadDefaultConfig(): void
    {
        $this->extension->load(self::DEFAULT_CONFIG, $this->container);

        $this->assertEquals('%kernel.root_dir%/DataMigrations', $this->container->getParameter('data_migrations.dir_name'));
        $this->assertEquals('Application\DataMigrations', $this->container->getParameter('data_migrations.namespace'));
        $this->assertEquals('data_migration_versions', $this->container->getParameter('data_migrations.table_name'));
        $this->assertEquals('Application Data Migrations', $this->container->getParameter('data_migrations.name'));
        $this->assertNull($this->container->getParameter('data_migrations.custom_template'));
        $this->assertFalse($this->container->getParameter('data_migrations.organize_migrations'));
    }

    private function getContainer()
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__.'/../../', // src dir
        ]));
    }
}
