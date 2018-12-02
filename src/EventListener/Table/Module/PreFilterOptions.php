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

namespace BlackForest\Contao\Calendar\Tags\EventListener\Table\Module;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class handle the pre filter options.
 */
class PreFilterOptions
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The constructor.
     *
     * @param Connection          $connection The database connection.
     * @param TranslatorInterface $translator The translator.
     */
    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * Handle the pre filter options.
     *
     * @param DataContainer $container The data container.
     *
     * @return array
     */
    public function handleOptions(DataContainer $container)
    {
        $activeRecord = $container->activeRecord;

        // @codingStandardsIgnoreStart
        $calendarList = @\unserialize($activeRecord->cal_calendar);
        // @codingStandardsIgnoreEnd
        if (!$calendarList) {
            return [];
        }

        $tags = $this->fetchAllTags($calendarList);
        if (!\count($tags)) {
            return [];
        }

        $options = [];
        foreach ($tags as $tag) {
            $options[$tag->id] = $tag->title;
        }

        return $options;
    }

    /**
     * Fetch all tags.
     *
     * @param array $calendarList The calender list.
     *
     * @return array
     */
    private function fetchAllTags(array $calendarList)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.*')
            ->from('tl_calendar_events_tags', 't')
            ->innerJoin('t', 'tl_calendar_events_tags_relation', 'r', 'r.tag = t.id')
            ->where($queryBuilder->expr()->in('r.calendar', ':calendarList'))
            ->setParameter(':calendarList', \array_map('\intval', $calendarList), Connection::PARAM_STR_ARRAY)
            ->orderBy('t.title')
            ->groupBy('t.id');

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        return \array_filter(
            $statement->fetchAll(\PDO::FETCH_OBJ),
            function ($tag) use ($calendarList) {
                return \count(\array_intersect((array) \unserialize($tag->calendar), $calendarList));
            }
        );
    }
}
