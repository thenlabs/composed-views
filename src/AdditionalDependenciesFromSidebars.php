<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait AdditionalDependenciesFromSidebars
{
    public function getAdditionalDependencies(): array
    {
        $dependencies = [];

        foreach ($this->sidebars as $sidebar) {
            $dependencies = array_merge($dependencies, $sidebar->getDependencies());
        }

        return $dependencies;
    }
}