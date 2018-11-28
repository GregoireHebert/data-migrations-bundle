<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsVersionDoctrineCommand as BaseMigrationsVersionDoctrineCommand;

/**
 * Command for manually adding and deleting migration versions from the version table.
 */
class MigrationsVersionDoctrineCommand extends BaseMigrationsVersionDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:version')
        ;
    }
}
