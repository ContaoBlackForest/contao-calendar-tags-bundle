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
 * Add global operation button.
 */

$GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations']['tags'] = [
    'label'           => &$GLOBALS['TL_LANG']['tl_calendar']['tags'],
    'href'            => 'table=tl_calendar_events_tags',
    'class'           => 'header_icon',
    'attributes'      => sprintf(
        '%s %s',
        'style="background-image: url(' . \Contao\Image::getPath('filter-apply.svg') . ');"',
        'onclick="Backend.getScrollOffset()"'
    ),
    'button_callback' => ['cb.table_calendar.permission', 'handleGlobalTagsCommand']
];
