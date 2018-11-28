<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsGenerateDoctrineCommand as BaseMigrationsGenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for generating new blank migration classes.
 */
class MigrationsGenerateDoctrineCommand extends BaseMigrationsGenerateDoctrineCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:generate')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // EM and DB options cannot be set at same time
        if (null !== $input->getOption('em') && null !== $input->getOption('db')) {
            throw new \InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        $versionNumber = $this->configuration->generateVersionNumber();

        $migrationGenerator = $this->dependencyFactory->getMigrationGenerator();

        $path = $migrationGenerator->generateMigration($versionNumber);

        $editorCommand = $input->getOption('editor-cmd');

        if (null !== $editorCommand) {
            $this->procOpen($editorCommand, $path);
        }

        $output->writeln([
            sprintf('Generated new migration class to "<info>%s</info>"', $path),
            '',
            sprintf(
                'To run just this migration for testing purposes, you can use <info>gheb:data-migrations:execute --up %s</info>',
                $versionNumber
            ),
            '',
            sprintf(
                'To revert the migration you can use <info>gheb:data-migrations:execute --down %s</info>',
                $versionNumber
            ),
        ]);

        return 0;
    }
}
