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
 * Extend the news tables.
 */

$GLOBALS['BE_MOD']['content']['calendar']['tables'] = array_merge(
    $GLOBALS['BE_MOD']['content']['calendar']['tables'],
    ['tl_calendar_events_tags', 'tl_calendar_events_tags_relation']
);

/*
 * Hooks.
 */

$GLOBALS['TL_HOOKS']['getAllEvents'][]  = ['cb.module_calendar_tags_events_list.prepare_tag_collection', 'prepare'];
$GLOBALS['TL_HOOKS']['parseTemplate'][] = ['cb.module_calendar_tags_events_list.add_filter_menu', 'handle'];
$GLOBALS['TL_HOOKS']['getAllEvents'][]  = ['cb.module_calendar_tags_events_list.item_filter', 'prepare'];
$GLOBALS['TL_HOOKS']['parseTemplate'][] = ['cb.module_calendar_tags_events_detail.add_filter_menu', 'handle'];

/*
 * Add permissions.
 */

$GLOBALS['TL_PERMISSIONS'][] = 'calendareventstagsp';
$GLOBALS['TL_PERMISSIONS'][] = 'calendareventstagsrelationp';
