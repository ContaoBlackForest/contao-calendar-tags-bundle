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

$GLOBALS['TL_DCA']['tl_calendar_events_tags'] = [

    'select' => [
        'buttons_callback' => [
            ['cb.table_calendar_events_tags.modal_select_button', 'handle']
        ]
    ],

    'config' => [
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'onload_callback'             => [
            ['cb.table_calendar_events_tags.permission', 'handlePermission'],
            ['cb.table_calendar_events_tags.apply_relations_calendar_events_modal_view', 'handle'],
            ['cb.table_calendar_events_tags.manipulate_calendar_events_modal_view', 'handle']
        ],
        'ondelete_callback'           => [
            ['cb.table_calendar_events_tags.delete', 'handle']
        ],
        'sql' => [
            'keys' => [
                'id'    => 'primary',
                'alias' => 'index'
            ]
        ],
        'backlink' => 'do=calendar'
    ],

    'list' => [
        'sorting' => [
            'mode'                => 1,
            'fields'              => ['title'],
            'flag'                => 1,
            'panelLayout'         => 'filter;search,limit'
        ],
        'label' => [
            'fields'              => ['title'],
            'label_callback'      => ['cb.table_calendar_events_tags.format_model_label', 'handle']
        ],
        'global_operations' => [
            'relations'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['relations'],
                'href'            => 'table=tl_calendar_events_tags_relation',
                'class'           => 'header_icon',
                'attributes'      => sprintf(
                    '%s %s',
                    'style="background-image: url(' . \Contao\Image::getPath('sizes.svg') . ');"',
                    'onclick="Backend.getScrollOffset()"'
                ),
                'button_callback' => ['cb.table_calendar_events_tags.permission', 'handleGlobalTagsCommand']
            ],
            'all' => [
                'label'           => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'            => 'act=select',
                'class'           => 'header_edit_all',
                'attributes'      => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ],
        ],
        'operations' => [
            'edit' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['edit'],
                'href'            => 'act=edit',
                'icon'            => 'edit.svg',
                'button_callback' => ['cb.table_calendar_events_tags.permission', 'handleButtonCanEdit']
            ],
            'copy' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.svg',
                'button_callback' => ['cb.table_calendar_events_tags.permission', 'handleButtonCanEdit']
            ],
            'delete' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.svg',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))' .
                                     'return false;Backend.getScrollOffset()"',
                'button_callback' => ['cb.table_calendar_events_tags.permission', 'handleButtonCanDelete']
            ],
            'show' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['show'],
                'href'            => 'act=show',
                'icon'            => 'show.svg'
            ]
        ]
    ],

    'palettes' => [
        '__selector__' => ['tagLink'],
        'default'      => '{title_legend},title,alias;{calendar_legend},calendar,tagLink;{note_legend:hide},note'
    ],

    'subpalettes' => [
        'tagLink'      => 'tagLinkFallback'
    ],

    'fields' => [
        'id' => [
            'sql'              => 'int(10) unsigned NOT NULL auto_increment'
        ],
        'tstamp' => [
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['title'],
            'exclude'          => true,
            'search'           => true,
            'inputType'        => 'text',
            'eval'             => ['mandatory' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'              => "varchar(255) NOT NULL default ''"
        ],
        'alias' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['alias'],
            'exclude'          => true,
            'search'           => true,
            'inputType'        => 'text',
            'eval'             => ['rgxp' => 'alias', 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50 clr'],
            'save_callback'    => [
                ['cb.table_calendar_events_tags.alias_generator', 'handle']
            ],
            'sql'              => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ],
        'calendar' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['calendar'],
            'exclude'          => true,
            'search'           => true,
            'inputType'        => 'checkbox',
            'options_callback' => ['cb.table_calendar_events_tags.calendar_options', 'handle'],
            'eval'             => ['multiple' => true, 'mandatory' => true],
            'sql'              => 'blob NULL'
        ],
        'tagLink' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['tagLink'],
            'exclude'          => true,
            'inputType'        => 'checkbox',
            'eval'             => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'              => "char(1) NOT NULL default ''"
        ],
        'tagLinkFallback' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['tagLinkFallback'],
            'exclude'          => true,
            'inputType'        => 'pageTree',
            'foreignKey'       => 'tl_page.title',
            'eval'             => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
            'relation'         => ['mandatory' => true, 'type' => 'hasOne', 'load' => 'lazy']
        ],
        'note' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_tags']['note'],
            'exclude'          => true,
            'search'           => true,
            'inputType'        => 'textarea',
            'eval'             => ['style' => 'height:60px', 'tl_class' => 'clr'],
            'sql'              => 'text NULL'
        ]
    ]
];
