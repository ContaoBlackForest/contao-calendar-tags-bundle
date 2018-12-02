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

namespace BlackForest\Contao\Calendar\Tags\EventListener\Table\CalendarEventsTags;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This handle the formatting of the model label.
 */
class FormatModelLabel
{
    /**
     * The input.
     *
     * @var Input
     */
    private $input;

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
     * @param Adapter             $input      The input.
     */
    public function __construct(Connection $connection, TranslatorInterface $translator, Adapter $input)
    {
        $this->connection = $connection;
        $this->input      = $input;
        $this->translator = $translator;
    }

    /**
     * Handle the formatting of the model label.
     *
     * @param array $row The row data.
     *
     * @return string
     */
    public function handle(array $row)
    {
        if ($this->input->get('popup')) {
            return $row['title'];
        }

        $label  = '<p>' . $this->trans('MOD.tl_calendar_events_tags', 'default') . ': ';
        $label .= '<br>&nbsp;&nbsp;' . $row['title'] . '</p>';

        $calendarNames = $this->fetchCalendarNames($row['calendar']);

        $label .= '<p style="margin-bottom: 0;">' . $this->trans('tl_calendar_events_tags.calendar.0') . ': ';
        if (!\count($calendarNames)) {
            $label .= '<br>&nbsp;' . $this->trans('MSC.noResult', 'default');
        }
        if (\count($calendarNames)) {
            $label .= '<ul>';

            foreach ($calendarNames as $archiveName) {
                $label .= '<li>- ' . $archiveName . '</li>';
            }

            $label .= '</ul>';
        }
        $label .= '</p>';

        if ($row['tagLink'] && $row['tagLinkFallback']) {
            $page   = $this->fetchPageById($row['tagLinkFallback']);
            $label .= '<p>' . $this->trans('tl_calendar_events_tags.tagLinkFallback.0') . ': ';
            $label .= '<br>&nbsp;&nbsp;' . $page->title . '</p>';
        }

        return $label;
    }

    /**
     * Fetch the calendar names.
     *
     * @param string $calendarList The calendar list.
     *
     * @return array
     */
    private function fetchCalendarNames($calendarList)
    {
        if (!$calendarList) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('c.title')
            ->from('tl_calendar', 'c')
            ->where($queryBuilder->expr()->in('c.id', ':calendarIds'))
            ->setParameter(
                ':calendarIds',
                \array_map('\intval', \unserialize($calendarList)),
                Connection::PARAM_STR_ARRAY
            );

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return [];
        }

        $calendarNames = [];
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $calendar) {
            $calendarNames[] = $calendar->title;
        }

        return $calendarNames;
    }

    /**
     * Fetch the page by id.
     *
     * @param string $pageId The page id.
     *
     * @return mixed|null
     */
    private function fetchPageById($pageId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('p.*')
            ->from('tl_page', 'p')
            ->where($queryBuilder->expr()->eq('p.id', ':pageId'))
            ->setParameter(':pageId', $pageId);

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return null;
        }

        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Translate the identifier.
     *
     * @param string $identifier The translation identifier.
     * @param string $domain     The translation domain.
     *
     * @return string
     */
    private function trans($identifier, $domain = 'tl_calendar_events_tags')
    {
        return $this->translator->trans($identifier, [], 'contao_' . $domain);
    }
}
