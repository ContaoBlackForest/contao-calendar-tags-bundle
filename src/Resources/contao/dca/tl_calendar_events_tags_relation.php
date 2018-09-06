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

$GLOBALS['TL_DCA']['tl_calendar_events_tags_relation'] = [

    'config'  => [
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'onload_callback'             => [
            ['cb.table_calendar_events_tags_relation.permission', 'handlePermission']
        ],
        'sql' => [
            'keys' => [
                'id'                            => 'primary',
                'calendar,calendarEvents,tag'   => 'index'
            ]
        ],
        'backlink' => 'do=calendar&amp;table=tl_calendar_events_tags'
    ],

    'list' => [
        'sorting' => [
            'mode'                => 2,
            'fields'              => ['tag', 'calendar', 'calendarEvents'],
            'flag'                => 1,
            'panelLayout'         => 'sort;filter;search,limit'
        ],
        'label' => [
            'fields'              => [
                'calendar:tl_calendar.title',
                'calendarEvents:tl_calendar_events.title',
                'tag:tl_calendar_events_tags.title'
            ],
            // @codingStandardsIgnoreStart
            'format'              => $GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['calendar'][0] . ': %s<br>' .
                                     $GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['calendarEvents'][0] . ': %s<br>' .
                                     $GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['tag'][0] . ': %s<br>'
            // @codingStandardsIgnoreEnd
        ],
        'global_operations' => [
            'all' => [
                'label'           => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'            => 'act=select',
                'class'           => 'header_edit_all',
                'attributes'      => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ],
        ],
        'operations' => [
            'edit' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['edit'],
                'href'            => 'act=edit',
                'icon'            => 'edit.svg',
                'button_callback' => ['cb.table_calendar_events_tags_relation.permission', 'handleButtonCanEdit']
            ],
            'copy' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.svg',
                'button_callback' => ['cb.table_calendar_events_tags_relation.permission', 'handleButtonCanEdit']
            ],
            'delete' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.svg',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))' .
                                     'return false;Backend.getScrollOffset()"',
                'button_callback' => ['cb.table_calendar_events_tags_relation.permission', 'handleButtonCanDelete']
            ],
            'show' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['show'],
                'href'            => 'act=show',
                'icon'            => 'show.svg'
            ]
        ]
    ],

    'palettes' => [
        'default' => '{calendar_legend},calendar;{calendarEvents_legend},calendarEvents;{tag_legend},tag'
    ],

    'fields' => [
        'id' => [
            'sql'              => 'int(10) unsigned NOT NULL auto_increment'
        ],
        'tstamp' => [
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ],
        'calendar' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['calendar'],
            'exclude'          => true,
            'sorting'          => true,
            'filter'           => true,
            'search'           => true,
            'inputType'        => 'select',
            'options_callback' => ['cb.table_calendar_events_tags_relation.calendar_options', 'handle'],
            'eval'             => [
                'multiple'           => false,
                'chosen'             => true,
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ],
        'calendarEvents' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['calendarEvents'],
            'exclude'          => true,
            'sorting'          => true,
            'filter'           => true,
            'search'           => true,
            'inputType'        => 'select',
            'options_callback' => ['cb.table_calendar_events_tags_relation.calendar_events_options', 'handle'],
            'eval'             => [
                'multiple'           => false,
                'chosen'             => true,
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ],
        'tag' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags_relation']['tag'],
            'exclude'          => true,
            'sorting'          => true,
            'filter'           => true,
            'search'           => true,
            'inputType'        => 'select',
            'options_callback' => ['cb.table_calendar_events_tags_relation.tag_options', 'handle'],
            'eval'             => [
                'multiple'           => false,
                'chosen'             => true,
                'mandatory'          => true,
                'includeBlankOption' => true
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ]
    ]
];
