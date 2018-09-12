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

/*
 * Add callbacks.
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ondelete_callback'][] =
    ['cb.table_calendar_events.delete', 'handle'];
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['oncut_callback'][]    =
    ['cb.table_calendar_events.move', 'handle'];


/*
 * Add model operation button.
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['selectTags'] = [
    'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events']['selectTags'],
    'href'            => 'act=select&amp;table=tl_calendar_events_tags',
    'icon'            => 'filter-apply.svg',
    'button_callback' => ['cb.table_calendar_events.select_tags_model_command', 'handle']
];
