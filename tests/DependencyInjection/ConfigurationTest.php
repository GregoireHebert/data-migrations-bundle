<?php

namespace Gheb\DataMigrationsBundle\Tests\DependencyInjection;

use Gheb\DataMigrationsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Processor
     */
    private $processor;

    public function setUp()
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testDefaultConfig()
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();
        $config = $this->processor->processConfiguration($this->configuration, []);

        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $this->assertEquals([
            'dir_name' => '%kernel.root_dir%/DataMigrations',
            'namespace' => 'Application\DataMigrations',
            'table_name' => 'data_migration_versions',
            'column_name' => 'version',
            'column_length' => 14,
            'executed_at_column_name' => 'executed_at',
            'all_or_nothing' => false,
            'name' => 'Application Data Migrations',
            'custom_template' => null,
            'organize_migrations' => false,
        ], $config);
    }

    public function testOverwrittenConfig()
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();
        $config = $this->processor->processConfiguration($this->configuration, ['data_migrations' => [
            'dir_name' => '%kernel.root_dir%/CustomDataMigrations',
            'namespace' => 'Application\CustomDataMigrations',
            'table_name' => 'data_migration_versions_custom',
            'column_name' => 'version_custom',
            'column_length' => 255,
            'executed_at_column_name' => 'custom_executed_at',
            'all_or_nothing' => true,
            'name' => 'Custom Application Data Migrations',
            'custom_template' => 'tmple.tp',
            'organize_migrations' => 'BY_YEAR_AND_MONTH',
        ]]);

        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $this->assertEquals([
            'dir_name' => '%kernel.root_dir%/CustomDataMigrations',
            'namespace' => 'Application\CustomDataMigrations',
            'table_name' => 'data_migration_versions_custom',
            'column_name' => 'version_custom',
            'column_length' => 255,
            'executed_at_column_name' => 'custom_executed_at',
            'all_or_nothing' => true,
            'name' => 'Custom Application Data Migrations',
            'custom_template' => 'tmple.tp',
            'organize_migrations' => 'year_and_month',
        ], $config);
    }
}
