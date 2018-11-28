<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseCommand;
use Doctrine\Migrations\Configuration\AbstractFileConfiguration;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Version\Version;
use ErrorException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use function error_get_last;
use function is_dir;
use function method_exists;
use function mkdir;
use function preg_match;
use function sprintf;
use function str_replace;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
abstract class DoctrineCommand extends BaseCommand
{
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration): void
    {
        $dir = $configuration->getMigrationsDirectory();

        if (null === $dir) {
            $dir = $container->getParameter('data_migrations.dir_name');

            if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
                $error = error_get_last();

                throw new ErrorException(sprintf(
                    'Failed to create directory "%s" with message "%s"',
                    $dir,
                    $error['message']
                ));
            }

            $configuration->setMigrationsDirectory($dir);
        } else {
            // class Kernel has method getKernelParameters with some of the important path parameters
            $pathPlaceholderArray = ['kernel.root_dir', 'kernel.cache_dir', 'kernel.logs_dir'];

            foreach ($pathPlaceholderArray as $pathPlaceholder) {
                if (!$container->hasParameter($pathPlaceholder) || !preg_match('/\%'.$pathPlaceholder.'\%/', $dir)) {
                    continue;
                }

                $dir = str_replace('%'.$pathPlaceholder.'%', $container->getParameter($pathPlaceholder), $dir);
            }

            if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
                $error = error_get_last();

                throw new ErrorException(sprintf(
                    'Failed to create directory "%s" with message "%s"',
                    $dir,
                    $error['message']
                ));
            }

            $configuration->setMigrationsDirectory($dir);
        }

        if (null === $configuration->getMigrationsNamespace()) {
            $configuration->setMigrationsNamespace($container->getParameter('data_migrations.namespace'));
        }

        if (null === $configuration->getName()) {
            $configuration->setName($container->getParameter('data_migrations.name'));
        }

        if ('' !== $configuration->getMigrationsTableName()) {
            $configuration->setMigrationsTableName($container->getParameter('data_migrations.table_name'));
        }

        $configuration->setMigrationsColumnName($container->getParameter('data_migrations.column_name'));
        $configuration->setMigrationsColumnLength($container->getParameter('data_migrations.column_length'));
        $configuration->setMigrationsExecutedAtColumnName($container->getParameter('data_migrations.executed_at_column_name'));
        $configuration->setAllOrNothing($container->getParameter('data_migrations.all_or_nothing'));

        // Migrations is not register from configuration loader
        if (!($configuration instanceof AbstractFileConfiguration)) {
            $migrationsDirectory = $configuration->getMigrationsDirectory();

            if (null !== $migrationsDirectory) {
                $configuration->registerMigrationsFromDirectory($migrationsDirectory);
            }
        }

        if (method_exists($configuration, 'getCustomTemplate') && null === $configuration->getCustomTemplate()) {
            $configuration->setCustomTemplate($container->getParameter('data_migrations.custom_template'));
        }

        $organizeMigrations = $container->getParameter('data_migrations.organize_migrations');

        switch ($organizeMigrations) {
            case Configuration::VERSIONS_ORGANIZATION_BY_YEAR:
                $configuration->setMigrationsAreOrganizedByYear(true);
                break;
            case Configuration::VERSIONS_ORGANIZATION_BY_YEAR_AND_MONTH:
                $configuration->setMigrationsAreOrganizedByYearAndMonth(true);
                break;
            case false:
                break;
            default:
                throw new InvalidArgumentException('Invalid value for "data_migrations.organize_migrations" parameter.');
        }

        self::injectContainerToMigrations($container, $configuration->getMigrations());
    }

    /**
     * @param Version[] $versions
     *
     * Injects the container to migrations aware of it
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions): void
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if (!($migration instanceof ContainerAwareInterface)) {
                continue;
            }

            $migration->setContainer($container);
        }
    }
}
