<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseCommand;
use Doctrine\Migrations\Configuration\AbstractFileConfiguration;
use Doctrine\Migrations\Configuration\Configuration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
abstract class DoctrineCommand extends BaseCommand
{
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration)
    {
        if (!$configuration->getMigrationsDirectory()) {
            $dir = $container->getParameter('data_migrations.dir_name');
            if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
                $error = error_get_last();
                throw new \ErrorException($error['message']);
            }
            $configuration->setMigrationsDirectory($dir);
        } else {
            $dir = $configuration->getMigrationsDirectory();
            // class Kernel has method getKernelParameters with some of the important path parameters
            $pathPlaceholderArray = ['kernel.root_dir', 'kernel.cache_dir', 'kernel.logs_dir'];
            foreach ($pathPlaceholderArray as $pathPlaceholder) {
                if ($container->hasParameter($pathPlaceholder) && preg_match('/\%'.$pathPlaceholder.'\%/', $dir)) {
                    $dir = str_replace('%'.$pathPlaceholder.'%', $container->getParameter($pathPlaceholder), $dir);
                }
            }
            if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
                $error = error_get_last();
                throw new \ErrorException($error['message']);
            }
            $configuration->setMigrationsDirectory($dir);
        }
        if (!$configuration->getMigrationsNamespace()) {
            $configuration->setMigrationsNamespace($container->getParameter('data_migrations.namespace'));
        }
        if (!$configuration->getName()) {
            $configuration->setName($container->getParameter('data_migrations.name'));
        }
        // For backward compatibility, need use a table from parameters for overwrite the default configuration
        if (!($configuration instanceof AbstractFileConfiguration) || !$configuration->getMigrationsTableName()) {
            $configuration->setMigrationsTableName($container->getParameter('data_migrations.table_name'));
        }
        // Migrations is not register from configuration loader
        if (!($configuration instanceof AbstractFileConfiguration)) {
            $configuration->registerMigrationsFromDirectory($configuration->getMigrationsDirectory());
        }

        if (method_exists($configuration, 'getCustomTemplate') && !$configuration->getCustomTemplate()) {
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
     * @param array $versions
     *
     * Injects the container to migrations aware of it
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions)
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }
}
