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

use Contao\BackendUser;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This class handle the calendar options.
 */
class CalendarOptions
{
    /**
     * The backend user.
     *
     * @var BackendUser
     */
    private $user;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The constructor.
     *
     * @param ContaoFrameworkInterface $framework  The framework.
     * @param RequestStack             $request    The request.
     * @param Connection               $connection The database connection.
     */
    public function __construct(
        ContaoFrameworkInterface $framework,
        RequestStack $request,
        Connection $connection
    ) {
        $this->connection = $connection;

        if (!$request->getCurrentRequest()
            || !('contao_backend' === $request->getCurrentRequest()->get('_route'))
        ) {
            return;
        }

        $this->user = $framework->createInstance(BackendUser::class);
    }

    /**
     * Handle the calendar options.
     *
     * @return array
     */
    public function handle()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('c.id', 'c.title')
            ->from('tl_calendar', 'c')
            ->orderBy('c.title');
        if (!$this->user->isAdmin) {
            $queryBuilder
                ->where($queryBuilder->expr()->in('c.id', ':ids'))
                ->setParameter(':ids', \array_map('\intval', $this->user->calendars), Connection::PARAM_STR_ARRAY);
        }

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $taggedArchive = $this->getTaggedCalendar();
        if (!\count($taggedArchive)) {
            return [];
        }

        $options = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $calendar) {
            if (!\in_array($calendar->id, $taggedArchive)) {
                continue;
            }

            $options[$calendar->id] = $calendar->title;
        }

        return $options;
    }

    /**
     * Get the tagged calendar.
     *
     * @return array
     */
    private function getTaggedCalendar()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.calendar')
            ->from('tl_calendar_events_tags', 't');

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $taggedCalendar = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $tag) {
            if (!($calendar = \unserialize($tag->calendar))) {
                continue;
            }

            $taggedCalendar = \array_unique(\array_merge_recursive($taggedCalendar, $calendar));
        }

        return $taggedCalendar;
    }
}
