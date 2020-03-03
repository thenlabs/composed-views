<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractCompositeView extends AbstractView implements CompositeComponentInterface
{
    use CompositeComponentTrait;

    protected $sidebars = [];

    public function validateChild(ComponentInterface $child): bool
    {
        return $child instanceof AbstractView ? true : false;
    }

    public function renderChildren(): string
    {
        $result = '';

        foreach ($this->children(false) as $child) {
            $result .= $child->render();
        }

        return $result;
    }

    public function getAdditionalDependencies(): array
    {
        $dependencies = [];

        foreach ($this->sidebars as $sidebar) {
            $dependencies = array_merge($dependencies, $sidebar->getDependencies());
        }

        return $dependencies;
    }

    public function getSidebars(): array
    {
        return $this->sidebars;
    }

    public function getSidebar(string $name): ?Sidebar
    {
        return $this->sidebars[$name] ?? null;
    }

    public function setSidebar(string $name, Sidebar $sidebar): void
    {
        $this->sidebars[$name] = $sidebar;
    }

    protected function createSidebar(string $name): void
    {
        $this->sidebars[$name] = new Sidebar;
    }

    public function renderSidebar(string $name): string
    {
        $sidebar = $this->sidebars[$name] ?? null;

        if (! $sidebar) {
            throw new Exception\UnexistentSidebarException($name);
        }

        return $sidebar->render();
    }
}
