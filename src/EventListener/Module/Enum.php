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

namespace BlackForest\Contao\Calendar\Tags\EventListener\Module;

/**
 * This class has some constants for the module.
 */
class Enum
{
    /**
     * The session key for the last last view.
     */
    const SESSION_LAST_LIST_VIEW = 'cb.calendar_tags.last_list_view';

    /**
     * The session key for the tag collection.
     */
    const SESSION_TAG_COLLECTION = 'cb.calendar_tags.tag_collection';
}
