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

namespace BlackForest\Contao\Calendar\Tags\EventListener\Module\ListView;

use BlackForest\Contao\Calendar\Tags\EventListener\Module\Enum;
use Contao\CoreBundle\Framework\Adapter;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Template;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * This handle for add the tags filter menu in the calendar events list.
 */
class AddFilterMenu
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The session bag.
     *
     * @var AttributeBag
     */
    private $sessionBag;

    /**
     * The input provider.
     *
     * @var Input
     */
    private $input;

    /**
     * The url suffix.
     *
     * @var string
     */
    private $urlSuffix;

    /**
     * The constructor.
     *
     * @param RequestStack $requestStack The request stack.
     * @param AttributeBag $sessionBag   The session bag.
     * @param Adapter      $input        The input provider.
     * @param string       $urlSuffix    The url suffix.
     */
    public function __construct(
        RequestStack $requestStack,
        AttributeBag $sessionBag,
        Adapter $input,
        $urlSuffix
    ) {
        $this->requestStack = $requestStack;
        $this->sessionBag   = $sessionBag;
        $this->input        = $input;
        $this->urlSuffix    = $urlSuffix;
    }

    /**
     * Handle for add the tags filter menu in the calendar events list.
     *
     * @param Template $template The template.
     *
     * @return void
     */
    public function handle(Template $template)
    {
        if (!('contao_frontend' === $this->requestStack->getCurrentRequest()->get('_route'))
            || !('eventlist' === $template->type)
            || !$template->calendarEventsTagsFilter
        ) {
            return;
        }

        $tags = $this->sessionBag->get(Enum::SESSION_TAG_COLLECTION);
        if (!$tags) {
            return;
        }

        $pathInfo = \urldecode($this->requestStack->getCurrentRequest()->getPathInfo());
        if ($this->urlSuffix) {
            $pathInfo = \substr($pathInfo, 0, -\strlen($this->urlSuffix));
        }

        if ($this->input->get('filterTag')) {
            $pathInfo = \substr($pathInfo, 0, -\strlen('/filterTag/' . $this->input->get('filterTag')));
        }

        $data = [
            'pathInfo'  => $this->requestStack->getCurrentRequest()->getBaseUrl() . $pathInfo,
            'urlSuffix' => $this->urlSuffix,
            'moduleId'  => $template->id,
            'tags'      => $tags,
            'active'    => $this->determineActiveTag($tags, $template->calendarEventsTagsPreFilter)
        ];

        $filterTemplate = new FrontendTemplate('calendar_events_list_tags_filter');
        $filterTemplate->setData($data);

        $template->events = $filterTemplate->parse(). PHP_EOL .  $template->events;

        $this->addListViewToSession($data['pathInfo']);
    }

    /**
     * Determine the active tag.
     *
     * @param array  $tags          The tag list.
     * @param string $tagIdentifier The tag identifier.
     *
     * @return string
     */
    private function determineActiveTag(array $tags, $tagIdentifier)
    {
        if ($this->input->get('filterTag')) {
            return $this->input->get('filterTag');
        } elseif ($tagIdentifier) {
            foreach ($tags as $tag) {
                if ((int) $tagIdentifier !== (int) $tag->id) {
                    continue;
                }

                return $tag->alias;
            }
        }

        return '';
    }

    /**
     * Add the relative list view url to the session.
     * For find the relative list view url by detail page to go back, when generate the tag list as link list.
     *
     * @param string $listViewUrl The relative list view url.
     *
     * @return void
     */
    private function addListViewToSession($listViewUrl)
    {
        $this->sessionBag->set(Enum::SESSION_LAST_LIST_VIEW, $listViewUrl);
    }
}
