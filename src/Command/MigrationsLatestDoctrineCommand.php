<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsLatestDoctrineCommand as BaseMigrationsLatestDoctrineCommand;

/**
 * Command for outputting the latest version number.
 */
class MigrationsLatestDoctrineCommand extends BaseMigrationsLatestDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:latest')
        ;
    }
}
