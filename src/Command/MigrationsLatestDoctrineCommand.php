<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for outputting the latest version number.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class MigrationsLatestDoctrineCommand extends LatestCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:latest')
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // EM and DB options cannot be set at same time
        if (null !== $input->getOption('em') && null !== $input->getOption('db')) {
            throw new \InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        Helper\DoctrineCommandHelper::setApplicationHelper($this->getApplication(), $input);

        $configuration = $this->getMigrationConfiguration($input, $output);
        DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        return parent::execute($input, $output);
    }
}
