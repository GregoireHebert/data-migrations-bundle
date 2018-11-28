<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsExecuteDoctrineCommand as BaseMigrationsExecuteDoctrineCommand;

/**
 * Command for executing single migrations up or down manually.
 */
class MigrationsExecuteDoctrineCommand extends BaseMigrationsExecuteDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:execute')
        ;
    }
}
