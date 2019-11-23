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

namespace Gheb\DataMigrationsBundle\Tests\Command;

use Doctrine\Migrations\Configuration\Configuration;
use Gheb\DataMigrationsBundle\Command\DoctrineCommand;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class DoctrineCommandTest extends TestCase
{
    public function testConfigureMigrations(): void
    {
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock->method('getMigrations')
            ->willReturn([]);
        $configurationMock->method('getMigrationsDirectory')
            ->willReturn(__DIR__.'/../../');

        $reflectionClass = new ReflectionClass(Configuration::class);
        if ($reflectionClass->hasMethod('getCustomTemplate')) {
            $configurationMock
                ->expects($this->once())
                ->method('setCustomTemplate')
                ->with('migrations.tpl');
        }

        $configurationMock
            ->expects($this->once())
            ->method('setMigrationsTableName')
            ->with('migrations');

        $configurationMock
            ->expects($this->once())
            ->method('setMigrationsNamespace')
            ->with('App\Migrations');

        $configurationMock
            ->expects($this->once())
            ->method('setMigrationsDirectory')
            ->with(__DIR__.'/../../');

        DoctrineCommand::configureMigrations($this->getContainer(), $configurationMock);
    }

    private function getContainer()
    {
        return new ContainerBuilder(new ParameterBag([
            'data_migrations.dir_name' => __DIR__.'/../../',
            'data_migrations.namespace' => 'App\\Migrations',
            'data_migrations.name' => 'App migrations',
            'data_migrations.table_name' => 'migrations',
            'data_migrations.organize_migrations' => Configuration::VERSIONS_ORGANIZATION_BY_YEAR,
            'data_migrations.custom_template' => 'migrations.tpl',
        ]));
    }
}
