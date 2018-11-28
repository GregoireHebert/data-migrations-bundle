<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand as BaseMigrationsMigrateDoctrineCommand;

/**
 * Command for executing a migration to a specified version or the latest available version.
 */
class MigrationsMigrateDoctrineCommand extends BaseMigrationsMigrateDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:migrate')
        ;
    }
}
