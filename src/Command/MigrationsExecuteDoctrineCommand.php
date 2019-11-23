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

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command for executing single migrations up or down manually.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class MigrationsExecuteDoctrineCommand extends ExecuteCommand
{
    protected static $defaultName = 'gheb:data-migrations:execute';

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        /** @var Application $application */
        $application = $this->getApplication();

        Helper\DoctrineCommandHelper::setApplicationHelper($application, $input);

        $configuration = $this->getMigrationConfiguration($input, $output);
        $container = $application->getKernel()->getContainer();
        \assert($container instanceof ContainerInterface);
        DoctrineCommand::configureMigrations($container, $configuration);

        parent::initialize($input, $output);
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // EM and DB options cannot be set at same time
        if (null !== $input->getOption('em') && null !== $input->getOption('db')) {
            throw new \InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        return parent::execute($input, $output);
    }
}
