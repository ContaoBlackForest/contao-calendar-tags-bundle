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
 * This class handle the tag options.
 */
class TagOptions
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
     * Handle the tag options.
     *
     * @param DataContainer $container The data container.
     *
     * @return array
     */
    public function handle(DataContainer $container)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.id, t.title')
            ->from('tl_calendar_events_tags', 't')
            ->orderBy('t.title');

        $this->addFilterForEditAction($queryBuilder, $container);

        if ($container->activeRecord->id
            && (!$container->activeRecord->calendar
                || !$container->activeRecord->calendarEvents)
        ) {
            return [];
        }

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $match = $this->matchEventsRelation($container);

        $options = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $tag) {
            if (\in_array($tag->id, $match)
                && ((int) $tag->id !== (int) $container->activeRecord->tag)
            ) {
                continue;
            }

            $options[$tag->id] = $tag->title;
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
        if (!$container->activeRecord->id
            && !$container->activeRecord->calendar
            && !$container->activeRecord->calendarEvents
        ) {
            return;
        }

        $queryBuilder
            ->where($queryBuilder->expr()->like('t.calendar', ':calendar'))
            ->orWhere($queryBuilder->expr()->eq('t.id', ':activeTag'))
            ->setParameter(':calendar', '%"' . $container->activeRecord->calendar . '"%')
            ->setParameter(':activeTag', $container->activeRecord->tag);
    }

    /**
     * Match calendar events relation.
     *
     * @param DataContainer $container The data container.
     *
     * @return array
     */
    private function matchEventsRelation(DataContainer $container)
    {
        if (!$container->activeRecord->id
            && !$container->activeRecord->calendar
            && !$container->activeRecord->calendarEvents
        ) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('r.*')
            ->from('tl_calendar_events_tags_relation', 'r')
            ->where($queryBuilder->expr()->eq('r.calendarEvents', ':calendarEventsId'))
            ->setParameter(':calendarEventsId', $container->activeRecord->calendarEvents);

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $match = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $relation) {
            $match[] = $relation->tag;
        }

        return $match;
    }
}
