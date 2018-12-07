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

namespace BlackForest\Contao\Calendar\Tags\EventListener\Module\ListView;

use BlackForest\Contao\Calendar\Tags\EventListener\Module\Enum;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Events;
use Contao\Input;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * This class prepare the tag collection.
 */
class PrepareTagCollection
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The session bag.
     *
     * @var AttributeBag
     */
    private $sessionBag;

    /**
     * The input provider.
     *
     * @var Input
     */
    private $input;

    /**
     * The internal event list.
     *
     * @var array
     */
    private $internalEventList;

    /**
     * The constructor.
     *
     * @param RequestStack $requestStack The request stack.
     * @param Connection   $connection   The database connection.
     * @param AttributeBag $sessionBag   The session bag.
     * @param Adapter      $input        The input provider.
     */
    public function __construct(
        RequestStack $requestStack,
        Connection $connection,
        AttributeBag $sessionBag,
        Adapter $input
    ) {
        $this->requestStack = $requestStack;
        $this->connection   = $connection;
        $this->sessionBag   = $sessionBag;
        $this->input        = $input;
    }

    /**
     * Prepare the filter menu.
     *
     * @param array  $eventList    The event list.
     * @param array  $calendarList The calendar list.
     * @param int    $startDate    The start date.
     * @param int    $endDate      The end date.
     * @param Events $events       The events caller.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepare(array $eventList, array $calendarList, $startDate, $endDate, Events $events)
    {
        if (!('frontend' === $this->requestStack->getCurrentRequest()->get('_scope'))
            || !('eventlist' === $events->type)
            || (!$events->calendarEventsTagsPreFilter && !$events->calendarEventsTagsFilter)
        ) {
            return $eventList;
        }

        $this->sessionBag->remove(Enum::SESSION_TAG_COLLECTION);

        $this->prepareInternalEventList($eventList);
        $tagCollection = $this->fetchAllTags($calendarList);
        if (!\count($tagCollection)) {
            return $eventList;
        }

        $this->sessionBag->set(Enum::SESSION_TAG_COLLECTION, $tagCollection);

        return $eventList;
    }

    /**
     * Prepare the internal event list.
     *
     * @param array $eventList The event list.
     *
     * @return void
     */
    private function prepareInternalEventList(array $eventList)
    {
        foreach ($eventList as $item) {
            if (\count($eventList)
                && !\array_key_exists('id', $item)
            ) {
                $this->prepareInternalEventList($item);

                continue;
            }

            $this->internalEventList[$item['id']] = $item;
        }
    }

    /**
     * Fetch all tags.
     *
     * @param array $calendarList The calendar list.
     *
     * @return array
     */
    private function fetchAllTags(array $calendarList)
    {
        if (!$this->internalEventList) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.*')
            ->from('tl_calendar_events_tags', 't')
            ->innerJoin('t', 'tl_calendar_events_tags_relation', 'r', 'r.tag = t.id')
            ->where($queryBuilder->expr()->in('r.calendar', ':calendarList'))
            ->where($queryBuilder->expr()->in('r.calendarEvents', ':calendarEvents'))
            ->setParameter(':calendarList', \array_map('\intval', $calendarList), Connection::PARAM_STR_ARRAY)
            ->setParameter(
                ':calendarEvents',
                \array_map('\intval', \array_keys($this->internalEventList)),
                Connection::PARAM_STR_ARRAY
            );

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }
}
