<?php

namespace Application\View\Helper\Navigation;

use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View\Helper\Navigation\Menu;

/**
 * Class Navbar
 *
 * @package Application\View\Helper\Navigation
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
     * @var string
     */
    protected $activeItemId;

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
     * @param string $id
     *
     * @return \Application\View\Helper\Navigation\Navbar
     */
    public function setActiveItemId(string $id): self
    {
        $this->activeItemId = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    )
    {
        $pages = $container->getPages();
        /** @var AbstractPage $page */
        foreach ($pages as $page) {
            if ($page->getId() === $this->activeItemId) {
                $page->setActive(true);
                break;
            }
        }

        return parent::renderNormalMenu($container,
            $ulClass,
            $indent,
            $minDepth,
            $maxDepth,
            $onlyActive,
            $escapeLabels,
            $addClassToListItem,
            $liActiveClass);
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
