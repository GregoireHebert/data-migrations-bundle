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

namespace Gheb\DataMigrationsBundle\Command\Helper;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper as BaseDoctrineCommandHelper;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
abstract class DoctrineCommandHelper extends BaseDoctrineCommandHelper
{
    public static function setApplicationHelper(Application $application, InputInterface $input): void
    {
        $container = $application->getKernel()->getContainer();
        $doctrine = $container->get('doctrine');
        $managerNames = $doctrine->getManagerNames();

        if ($input->getOption('db') || empty($managerNames)) {
            self::setApplicationConnection($application, $input->getOption('db'));
        } else {
            self::setApplicationEntityManager($application, $input->getOption('em'));
        }

        if ($input->getOption('shard')) {
            $connection = $application->getHelperSet()->get('db')->getConnection();
            if (!$connection instanceof PoolingShardConnection) {
                if (empty($managerNames)) {
                    throw new \LogicException(sprintf("Connection '%s' must implement shards configuration.", $input->getOption('db')));
                }
                throw new \LogicException(sprintf("Connection of EntityManager '%s' must implement shards configuration.", $input->getOption('em')));
            }

            $connection->connect($input->getOption('shard'));
        }
    }
}
