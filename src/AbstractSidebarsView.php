<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractSidebarsView extends AbstractCompositeView
{
    public function __construct()
    {
        foreach ($this->getSidebarsNames() as $name) {
            $sidebar = new Sidebar;
            $sidebar->setName("sidebar-{$name}");

            $this->addChild($sidebar);
        }
    }

    public function getSidebars(): array
    {
        return $this->findChilds(function ($child) {
            return $child instanceof Sidebar ? true : false;
        }, false);
    }

    public function getSidebarsNames(): array
    {
        return [];
    }
}
