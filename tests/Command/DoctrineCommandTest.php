<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Tests\Command;

use Doctrine\Migrations\Configuration\Configuration;
use Gheb\DataMigrationsBundle\Command\DoctrineCommand;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class DoctrineCommandTest extends TestCase
{
    /** @var string */
    private $migrationsDirectory;

    protected function setUp() : void
    {
        vfsStream::setup('migrations_directory');

        $this->migrationsDirectory = vfsStream::url('migrations_directory');
    }

    public function testConfigureMigrations() : void
    {
        /** @var Configuration|MockObject $configurationMock */
        $configurationMock = $this->createMock(Configuration::class);

        $configurationMock->method('getMigrations')
            ->willReturn([]);

        $configurationMock->expects($this->once())
            ->method('setMigrationsDirectory')
            ->with($this->migrationsDirectory);

        $configurationMock->expects($this->once())
            ->method('setMigrationsNamespace')
            ->with('test');

        $configurationMock->expects($this->once())
            ->method('setName')
            ->with('test');

        $configurationMock->expects($this->once())
            ->method('setMigrationsTableName')
            ->with('test');

        $configurationMock->expects($this->once())
            ->method('setMigrationsColumnName')
            ->with('version');

        $configurationMock->expects($this->once())
            ->method('setMigrationsColumnLength')
            ->with(255);

        $configurationMock->expects($this->once())
            ->method('setMigrationsColumnLength')
            ->with(255);

        $configurationMock->expects($this->once())
            ->method('setMigrationsExecutedAtColumnName')
            ->with('executed_at');

        $configurationMock->expects($this->once())
            ->method('setMigrationsAreOrganizedByYear')
            ->with(true);

        $configurationMock->expects($this->once())
            ->method('setAllOrNothing')
            ->with(false);

        DoctrineCommand::configureMigrations($this->getContainer(), $configurationMock);
    }

    private function getContainer() : ContainerBuilder
    {
        return new ContainerBuilder(new ParameterBag([
            'data_migrations.dir_name' => $this->migrationsDirectory,
            'data_migrations.namespace' => 'test',
            'data_migrations.name' => 'test',
            'data_migrations.table_name' => 'test',
            'data_migrations.column_name' => 'version',
            'data_migrations.column_length' => 255,
            'data_migrations.executed_at_column_name' => 'executed_at',
            'data_migrations.organize_migrations' => Configuration::VERSIONS_ORGANIZATION_BY_YEAR,
            'data_migrations.custom_template' => null,
            'data_migrations.all_or_nothing' => false,
        ]));
    }
}
