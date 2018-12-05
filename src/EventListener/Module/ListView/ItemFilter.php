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
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Events;
use Contao\Input;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * This class filter the event items.
 */
class ItemFilter
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The session bag.
     *
     * @var AttributeBag
     */
    private $sessionBag;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The controller.
     *
     * @var Controller
     */
    private $controller;

    /**
     * The input provider.
     *
     * @var Input
     */
    private $input;

    /**
     * The url suffix.
     *
     * @var string
     */
    private $urlSuffix;

    /**
     * The internal event identifier list.
     *
     * @var array
     */
    private $internalEventIds;

    /**
     * The constructor.
     *
     * @param RequestStack $requestStack The request stack.
     * @param AttributeBag $sessionBag   The session bag.
     * @param Connection   $connection   The database connection.
     * @param Adapter      $controller   The controller.
     * @param Adapter      $input        The input provider.
     * @param string       $urlSuffix    The url suffix.
     */
    public function __construct(
        RequestStack $requestStack,
        AttributeBag $sessionBag,
        Connection $connection,
        Adapter $controller,
        Adapter $input,
        $urlSuffix
    ) {
        $this->requestStack = $requestStack;
        $this->sessionBag   = $sessionBag;
        $this->connection   = $connection;
        $this->controller   = $controller;
        $this->input        = $input;
        $this->urlSuffix    = $urlSuffix;
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
            || (!$this->input->get('filterTag') && !$events->calendarEventsTagsPreFilter)
        ) {
            return $eventList;
        }

        $tagList = $this->sessionBag->get(Enum::SESSION_TAG_COLLECTION);
        if (!$tagList) {
            $this->redirectWithoutFilter();
        }

        $this->internalEventIds = $this->matchEventIdsByFilter($events->calendarEventsTagsPreFilter);
        if (!\count($this->internalEventIds)) {
            return $eventList;
        }

        $this->filterEventList($eventList);
        $this->cleanEventList($eventList);

        return $eventList;
    }

    /**
     * Match event identifier by filter.
     *
     * @param string $preFilter The pre filter. Is used if no tag filtered.
     *
     * @return array
     */
    private function matchEventIdsByFilter($preFilter)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('e.id')
            ->from('tl_calendar_events', 'e')
            ->innerJoin('e', 'tl_calendar_events_tags_relation', 'r', 'e.id = r.calendarEvents')
            ->innerJoin('r', 'tl_calendar_events_tags', 't', 'r.tag = t.id');

        if ($this->input->get('filterTag')) {
            $queryBuilder
                ->where($queryBuilder->expr()->eq('t.alias', ':alias'))
                ->setParameter(':alias', $this->input->get('filterTag'));
        } elseif ('all' !== $this->input->get('filterTag')) {
            $queryBuilder
                ->where($queryBuilder->expr()->eq('t.id', ':preFilter'))
                ->setParameter(':preFilter', $preFilter);
        }

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $matchingIds = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $event) {
            $matchingIds[] = (int) $event->id;
        }

        return $matchingIds;
    }

    /**
     * Filter the event list.
     *
     * @param array $eventList The event list.
     *
     * @return void
     */
    private function filterEventList(array &$eventList)
    {
        foreach ($eventList as &$item) {
            if (\count($eventList)
                && !\array_key_exists('id', $item)
            ) {
                $this->filterEventList($item);

                continue;
            }

            if (\in_array($item['id'], $this->internalEventIds)) {
                continue;
            }

            $item = [];
        }
    }

    /**
     * Clean the event list.
     *
     * @param array $eventList The event list.
     *
     * @return void
     */
    private function cleanEventList(array &$eventList)
    {
        foreach ($eventList as $item => $value) {
            if (\count($value)
                && !\array_key_exists('id', $value)
            ) {
                $this->cleanEventList($value);
            }

            if (\array_key_exists('id', $value)
                || \count($value)
            ) {
                continue;
            }

            unset($eventList[$item]);
        }
    }

    /**
     * Redirect to the page with out the filter parameter.
     *
     * @return void
     */
    private function redirectWithoutFilter()
    {
        $pathInfo = $this->requestStack->getCurrentRequest()->getPathInfo();
        if ($this->urlSuffix) {
            $pathInfo = \substr($pathInfo, 0, -\strlen($this->urlSuffix));
        }

        if ($this->input->get('filterTag')) {
            $pathInfo = \substr($pathInfo, 0, -\strlen('/filterTag/' . $this->input->get('filterTag')));
        }

        $this->controller
            ->redirect($this->requestStack->getCurrentRequest()->getBaseUrl() . $pathInfo . $this->urlSuffix);
    }
}
