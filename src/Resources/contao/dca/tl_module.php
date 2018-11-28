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
 * Add fields to the palette.
 */

Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField(
        ['calendarEventsTagsFilter'],
        'config_legend',
        Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND
    )
    ->applyToPalette('eventlist', 'tl_module');

Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField(
        ['calendarEventsTagsShow'],
        'config_legend',
        Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND
    )
    ->applyToPalette('eventreader', 'tl_module');

/*
 * Add fields.
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['calendarEventsTagsFilter'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['calendarEventsTagsFilter'],
    'exclude'   => true,
    'filter'    => true,
    'flag'      => 1,
    'inputType' => 'checkbox',
    'eval'      => ['doNotCopy' => true, 'tl_class' => 'w50 clr m12'],
    'sql'       => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['calendarEventsTagsShow'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['calendarEventsTagsShow'],
    'exclude'   => true,
    'filter'    => true,
    'flag'      => 1,
    'inputType' => 'checkbox',
    'eval'      => ['doNotCopy' => true, 'tl_class' => 'w50 clr m12'],
    'sql'       => "char(1) NOT NULL default ''"
];
