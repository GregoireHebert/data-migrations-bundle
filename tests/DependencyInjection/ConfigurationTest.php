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
            'name' => 'Application Data Migrations',
            'custom_template' => null,
            'organize_migrations' => false,
        ], $config);
    }

    public function testOverwrittenConfig()
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();
        $config = $this->processor->processConfiguration($this->configuration, ['data_migrations' => [
            'dir_name' => 'CustomDir/DataMigrations',
            'namespace' => 'My\DataMigrations',
            'table_name' => 'custom_migration',
            'name' => 'my app migrations',
            'custom_template' => true,
            'organize_migrations' => 'BY_YEAR_AND_MONTH'
        ]]);

        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $this->assertEquals([
            'dir_name' => 'CustomDir/DataMigrations',
            'namespace' => 'My\DataMigrations',
            'table_name' => 'custom_migration',
            'name' => 'my app migrations',
            'custom_template' => true,
            'organize_migrations' => 'year_and_month'
        ], $config);
    }
}
