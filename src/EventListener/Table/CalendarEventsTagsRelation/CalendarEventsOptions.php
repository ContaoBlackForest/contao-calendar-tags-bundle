<?php

/**
 * This file is part of contaoblackforest/contao-calendar-tags-bundle.
 *
 * (c) 2014-2018 The Contao Blackforest team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    contaoblackforest/contao-calendar-tags-bundle
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2014-2018 The Contao Blackforest team.
 * @license    https://github.com/contaoblackforest/contao-calendar-tags-bundle/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace BlackForest\Contao\Calendar\Tags\EventListener\Table\CalendarEventsTagsRelation;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * This class handle the calendar events options.
 */
class CalendarEventsOptions
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The constructor.
     *
     * @param Connection $connection The database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Handle the calendar events options.
     *
     * @param DataContainer $container The data container.
     *
     * @return array
     */
    public function handle(DataContainer $container)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('e.id, e.title')
            ->from('tl_calendar_events', 'e')
            ->orderBy('e.title');

        $this->addFilterForEditAction($queryBuilder, $container);

        if ($container->activeRecord->id && !$container->activeRecord->calendar) {
            return [];
        }

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $options = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $event) {
            $options[$event->id] = $event->title;
        }

        return $options;
    }

    /**
     * Add the filter if edit the model.
     *
     * @param QueryBuilder  $queryBuilder The query builder.
     * @param DataContainer $container    The data container.
     *
     * @return void
     */
    private function addFilterForEditAction(QueryBuilder $queryBuilder, DataContainer $container)
    {
        if (!$container->activeRecord->id && !$container->activeRecord->calendar) {
            return;
        }

        $queryBuilder
            ->where($queryBuilder->expr()->eq('e.pid', ':pid'))
            ->setParameter(':pid', $container->activeRecord->calendar);
    }
}
