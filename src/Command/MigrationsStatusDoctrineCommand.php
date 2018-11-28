<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsStatusDoctrineCommand as BaseMigrationsStatusDoctrineCommand;

/**
 * Command to view the status of a set of migrations.
 */
class MigrationsStatusDoctrineCommand extends BaseMigrationsStatusDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:status')
        ;
    }
}
