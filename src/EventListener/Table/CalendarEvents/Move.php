<?php

/**
 * This file is part of contaoblackforest/contao-news-tags-bundle.
 *
 * (c) 2014-2018 The Contao Blackforest team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    contaoblackforest/contao-news-tags-bundle
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2014-2018 The Contao Blackforest team.
 * @license    https://github.com/contaoblackforest/contao-news-tags-bundle/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace BlackForest\Contao\Calendar\Tags\EventListener\Table\CalendarEvents;

use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Input;
use Doctrine\DBAL\Connection;

/**
 * This handle if a calendar events model moved.
 */
class Move
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
     * Handle if a calendar events model moved.
     *
     * @param DataContainer $container The data container.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException Throws an exception if the delete failed.
     */
    public function handle(DataContainer $container)
    {
        $this->connection->update(
            'tl_calendar_events_tags_relation',
            ['tl_calendar_events_tags_relation.calendar' => $this->input->get('pid')],
            ['tl_calendar_events_tags_relation.calendarEvents' => $container->id]
        );
    }
}
