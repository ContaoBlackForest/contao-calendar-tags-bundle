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
use Contao\DataContainer;
use Contao\Input;
use Doctrine\DBAL\Connection;

/**
 * This class handle for manipulate calendar events tags, if load the table in modal view for select tags for events.
 */
class ManipulateEventsModalView
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The input.
     *
     * @var Input
     */
    private $input;

    /**
     * The constructor.
     *
     * @param Connection $connection The database connection.
     * @param Adapter    $input      The input.
     */
    public function __construct(Connection $connection, Adapter $input)
    {
        $this->connection = $connection;
        $this->input      = $input;
    }

    /**
     * Handle for manipulate calendar events tags, if load the table in modal view for select tags for events.
     *
     * @param DataContainer $container The data container.
     *
     * @return void
     */
    public function handle(DataContainer $container)
    {
        if (!$this->input->get('popup')
            || !$this->input->get('calendarId')
            || !$this->input->get('calendarEventsId')
            || !('tl_calendar' === $this->input->get('calendarTable'))
        ) {
            return;
        }

        $this->removePanel($container->table);
        $this->addFilter($container->table, $this->input->get('calendarId'));
        $this->loadCss();
        $this->injectSelectModelsScript();
    }

    /**
     * Remove the panel.
     *
     * @param string $providerName The data provider name.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function removePanel($providerName)
    {
        $GLOBALS['TL_DCA'][$providerName]['list']['sorting']['panelLayout'] = '';
    }

    /**
     * Filter the tags for used in the calendar.
     *
     * @param string $providerName The data provider name.
     * @param string $calendarId   The calendar id.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function addFilter($providerName, $calendarId)
    {
        $GLOBALS['TL_DCA'][$providerName]['list']['sorting']['filter'] = \array_merge(
            (array) $GLOBALS['TL_DCA'][$providerName]['list']['sorting']['filter'],
            [
                ['calendar LIKE ?', '%%"' . $calendarId . '"%%']
            ]
        );
    }

    /**
     * Load the css from bundle directory.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function loadCss()
    {
        $GLOBALS['TL_CSS'][] = '/bundles/blackforestcontaocalendartags/css/calendar-events-select-tags-modal.css';
    }

    /**
     * Inject javascript for add checked state for available relation.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function injectSelectModelsScript()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('r.tag')
            ->from('tl_calendar_events_tags_relation', 'r')
            ->where($queryBuilder->expr()->eq('r.calendar', ':calendar'))
            ->setParameter(':calendar', $this->input->get('calendarId'))
            ->andWhere($queryBuilder->expr()->eq('r.calendarEvents', ':calendarEvents'))
            ->setParameter(':calendarEvents', $this->input->get('calendarEventsId'));

        $statement = $queryBuilder->execute();
        if (!$statement->rowCount()) {
            return;
        }

        $selector = '';
        foreach ($statement->fetchAll(\PDO::FETCH_OBJ) as $relation) {
            if ($selector) {
                $selector .= ', ';
            }

            $selector .= '#ids_' . $relation->tag;
        }

        $GLOBALS['TL_MOOTOOLS'][] = "
        <script>
            var checkbox = document.querySelectorAll('${selector}');
            checkbox.forEach(function(e) {
                e.checked = true;
            });

            var formSubmit = document.querySelector('input[name=FORM_SUBMIT]');
            formSubmit.value = 'tl_apply';
        </script>
        ";
    }
}
