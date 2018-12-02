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
 * Fields.
 */

$GLOBALS['TL_LANG']['tl_calendar_events_tags']['title']           = ['Title', 'Please enter the tag title'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['alias']           = ['Alias', 'The alias for this tag. The alias is generated automatically if you do not enter anything.'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['calendar']        = ['Calendar', 'Here you can determine in which calendar this tag should be available.'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['tagLink']         = ['Activate link', 'Here you can activate the tag in the tag list as a link for the detail page.'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['tagLinkFallback'] = ['Fallback page', 'Here you can select the fallback page. If the list page of the events was not found automatically, then the fallback page is used.'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['note']            = ['Note', 'Here can you enter a note.'];

/*
 * Legends.
 */

$GLOBALS['TL_LANG']['tl_calendar_events_tags']['title_legend']    = 'Title';
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['calendar_legend'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['note_legend']     = 'Note';

/*
 * Global operations.
 */

$GLOBALS['TL_LANG']['tl_calendar_events_tags']['new']       = ['New tag', 'Create a new tag'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['relations'] = ['Event relation', 'Manage Tags relations'];

/*
 * Modal operations.
 */

$GLOBALS['TL_LANG']['tl_calendar_events_tags']['edit']   = ['Edit tag', 'Edit tag ID %s'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['copy']   = ['Copy tag', 'Copy tag ID %s'];
$GLOBALS['TL_LANG']['tl_calendar_events_tags']['delete'] = ['Delete tag', 'Delete tag ID %s'];
