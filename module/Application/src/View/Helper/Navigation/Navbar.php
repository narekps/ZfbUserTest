<?php

namespace Application\View\Helper\Navigation;

use Zend\Navigation\Page\AbstractPage;
use Zend\View\Helper\Navigation\Menu;

/**
 * Class Navbar
 *
 * @package MDBootstrap\View\Helper\Navigation
 */
class Navbar extends Menu
{
    /**
     * CSS class for <a> element.
     *
     * @var string
     */
    protected $classToAItem = 'nav-link';

    /**
     * @param string $class
     *
     * @return \Application\View\Helper\Navigation\Navbar
     */
    public function setClassToAItem(string $class): self
    {
        $this->classToAItem = $class;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        $addClassToListItem = false;
        $page->setClass($this->classToAItem);

        return parent::htmlify($page, $escapeLabel, $addClassToListItem);
    }
}
